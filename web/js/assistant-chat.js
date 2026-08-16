/**
 * Estado del diálogo + streaming SSE + persistencia en localStorage.
 * Puerto de useMozoChat.ts (estado) y mozoAiStream.ts (streamAiReply,
 * extractCompleteSentences) de mi-oshka/CamIA, sin carrito/pedido/menú.
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const STORAGE_KEY = 'cadipel_assistant_chat_v1';
  const MAX_HISTORY_MESSAGES = 20;
  const VOICE_PENDING = '…';

  let entries = [];   // [{role: 'user'|'bot'|'typing', content, pending?}]
  let history = [];   // [{role: 'user'|'assistant', content}] — lo que se manda al backend
  const entryListeners = new Set();
  let currentAbort = null;

  function notify() {
    for (const fn of entryListeners) fn(entries.slice());
  }

  function onEntriesChange(cb) {
    entryListeners.add(cb);
    return () => entryListeners.delete(cb);
  }

  function loadPersisted(greeting) {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed.entries) && parsed.entries.length > 0) {
          entries = parsed.entries;
          history = Array.isArray(parsed.history) ? parsed.history : [];
          return;
        }
      }
    } catch {
      // localStorage no disponible o corrupto — arrancamos de cero
    }
    entries = [{ role: 'bot', content: greeting }];
    history = [];
  }

  function persist() {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify({ entries, history }));
    } catch {
      // storage lleno o bloqueado — no es crítico, seguimos en memoria
    }
  }

  function resetChat(greeting) {
    entries = [{ role: 'bot', content: greeting }];
    history = [];
    persist();
    notify();
  }

  /** Divide un buffer de streaming en frases completas — mismo criterio que en mi-oshka. */
  function extractCompleteSentences(buf, minLen, isFinal) {
    const sentences = [];
    let pos = 0;
    const re = /(?:[.!?…]+\s+|\n\n)/g;
    let m;
    while ((m = re.exec(buf)) !== null) {
      const end = m.index + m[0].length;
      const sentence = buf.slice(pos, end).trim();
      if (sentence.length >= minLen) {
        sentences.push(sentence);
        pos = end;
      }
    }
    const rest = buf.slice(pos);
    if (isFinal && rest.trim().length >= 1) {
      sentences.push(rest.trim());
      return [sentences, ''];
    }
    return [sentences, rest];
  }

  async function streamAiReply(body, onChunk, signal) {
    const res = await fetch('/api/ai_stream.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
      signal,
    });

    if (!res.ok || !res.body) {
      throw new Error(`Stream HTTP ${res.status}`);
    }

    const reader = res.body.getReader();
    const decoder = new TextDecoder();
    let accumulated = '';
    let lineBuffer = '';

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;

      lineBuffer += decoder.decode(value, { stream: true });
      const lines = lineBuffer.split('\n');
      lineBuffer = lines.pop() || '';

      for (const line of lines) {
        if (!line.startsWith('data: ')) continue;
        const payload = line.slice(6);
        if (payload === '[DONE]') return accumulated;

        let parsed;
        try { parsed = JSON.parse(payload); } catch { continue; }

        if (parsed.error) throw new Error(parsed.error);
        if (parsed.text) {
          accumulated += parsed.text;
          onChunk(parsed.text, accumulated);
        }
      }
    }
    return accumulated;
  }

  function appendUser(text, pending) {
    entries.push({ role: 'user', content: text, pending: !!pending });
    notify();
  }

  function appendTyping() {
    entries.push({ role: 'typing' });
    notify();
  }

  function replaceTypingWithBot(content) {
    const idx = [...entries].reverse().findIndex((e) => e.role === 'typing');
    if (idx === -1) {
      entries.push({ role: 'bot', content });
    } else {
      const pos = entries.length - 1 - idx;
      entries[pos] = { role: 'bot', content };
    }
    notify();
  }

  function patchPendingUser(content) {
    for (let i = entries.length - 1; i >= 0; i--) {
      if (entries[i].role === 'user' && entries[i].pending) {
        entries[i] = { role: 'user', content };
        notify();
        return;
      }
    }
  }

  function removePendingUser() {
    const idx = [...entries].reverse().findIndex((e) => e.role === 'user' && e.pending);
    if (idx === -1) return;
    const pos = entries.length - 1 - idx;
    entries.splice(pos, 1);
    notify();
  }

  function trimHistory() {
    if (history.length > MAX_HISTORY_MESSAGES) {
      history = history.slice(history.length - MAX_HISTORY_MESSAGES);
    }
  }

  function updateLastBotBubble(content) {
    for (let i = entries.length - 1; i >= 0; i--) {
      if (entries[i].role === 'bot') {
        entries[i] = { role: 'bot', content };
        notify();
        return;
      }
      if (entries[i].role === 'typing') {
        entries[i] = { role: 'bot', content };
        notify();
        return;
      }
    }
  }

  function abortStream() {
    if (currentAbort) currentAbort.abort();
  }

  /** Streams the AI reply for whatever the last message in `history` is — shared by sendText/sendVoiceBlob. */
  async function runStreamingTurn(opts) {
    const abort = new AbortController();
    currentAbort = abort;

    let spokenPos = 0;
    let gotFirstChunk = false;

    try {
      const finalText = await streamAiReply(
        { messages: history, lang: opts.lang || 'es' },
        (_delta, accumulated) => {
          if (!gotFirstChunk) {
            gotFirstChunk = true;
            replaceTypingWithBot(accumulated);
          } else {
            updateLastBotBubble(accumulated);
          }

          if (opts.voiceMode && window.CadipelAssistant.tts) {
            const [ready, rest] = extractCompleteSentences(accumulated.slice(spokenPos), 20, false);
            if (ready.length > 0) {
              spokenPos += accumulated.slice(spokenPos).length - rest.length;
              for (const sentence of ready) {
                window.CadipelAssistant.tts.enqueue(sentence, opts.ttsOpts || {});
              }
            }
          }
        },
        abort.signal,
      );

      if (!gotFirstChunk) {
        replaceTypingWithBot(finalText || '');
      }

      if (opts.voiceMode && window.CadipelAssistant.tts) {
        const [ready] = extractCompleteSentences(finalText.slice(spokenPos), 1, true);
        for (const sentence of ready) {
          window.CadipelAssistant.tts.enqueue(sentence, opts.ttsOpts || {});
        }
      }

      history.push({ role: 'assistant', content: finalText || '' });
      persist();
    } catch (e) {
      if (e && e.name === 'AbortError') {
        return;
      }
      const errMsg = (window.CadipelAssistant.i18n && window.CadipelAssistant.i18n.t('assistant_error'))
        || 'No pudimos conectar con el asistente. Probá de nuevo en un momento.';
      replaceTypingWithBot(errMsg);
      persist();
    } finally {
      if (currentAbort === abort) currentAbort = null;
    }
  }

  /**
   * @param {string} text
   * @param {{lang: string, voiceMode?: boolean, ttsOpts?: object}} opts
   */
  async function sendText(text, opts) {
    const trimmed = (text || '').trim();
    if (!trimmed) return;

    appendUser(trimmed);
    appendTyping();

    history.push({ role: 'user', content: trimmed });
    trimHistory();

    await runStreamingTurn(opts);
  }

  /**
   * Graba → transcribe → envía, todo en un solo turno (igual que dictationFromBlob/
   * sendVoice en useCamiaChat.ts — mismo camino tanto para el mic de texto como
   * para el orbe de voz, la única diferencia es voiceMode/ttsOpts en `opts`).
   * @param {{blob: Blob, mimeType: string}} recording
   * @param {{lang: string, voiceMode?: boolean, ttsOpts?: object}} opts
   */
  async function sendVoiceBlob(recording, opts) {
    if (!recording || !recording.blob) return;

    appendUser(VOICE_PENDING, true);
    appendTyping();

    let transcript = '';
    try {
      transcript = await window.CadipelAssistant.voice.transcribe(recording, opts.lang || 'es');
    } catch {
      transcript = '';
    }

    if (!transcript) {
      removePendingUser();
      const msg = (window.CadipelAssistant.i18n && window.CadipelAssistant.i18n.t('assistant_stt_repeat'))
        || 'No te escuché bien — ¿podés repetirlo?';
      replaceTypingWithBot(msg);
      return;
    }

    patchPendingUser(transcript);
    history.push({ role: 'user', content: transcript });
    trimHistory();

    await runStreamingTurn(opts);
  }

  function getEntries() {
    return entries.slice();
  }

  window.CadipelAssistant.chat = {
    loadPersisted,
    resetChat,
    sendText,
    sendVoiceBlob,
    abortStream,
    getEntries,
    onEntriesChange,
  };
})();
