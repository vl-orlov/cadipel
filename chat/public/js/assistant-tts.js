/**
 * Cola de texto-a-voz: divide la respuesta en frases, pide audio a /api/tts,
 * precarga la frase siguiente mientras suena la actual (sin huecos entre frases).
 * Puerto de drainTtsQueue/fetchTtsPayload/speakText en useMozoChat.ts (mi-oshka).
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const MIN_CHUNK_LEN = 80; // agrupa 1-2 frases por request: TTS tiene rate limit
  const MAX_CHUNK_LEN = 220;

  /** Divide el texto en frases hablables — mismo criterio que extractCompleteSentences (mozoAiStream.ts). */
  function splitIntoTtsChunks(text) {
    const clean = text.replace(/\s+/g, ' ').trim();
    if (!clean) return [];

    const raw = clean.split(/(?<=[.!?…])\s+/);
    const chunks = [];
    let buffer = '';
    for (const part of raw) {
      const next = buffer ? `${buffer} ${part}` : part;
      // Junta frases hasta ~220 caracteres. Por debajo de 80 no corta: un request por frase
      // quema el rate limit. Una frase sola más larga que el máximo se manda entera.
      if (buffer && buffer.length >= MIN_CHUNK_LEN && next.length > MAX_CHUNK_LEN) {
        chunks.push(buffer.trim());
        buffer = part;
      } else {
        buffer = next;
      }
    }
    if (buffer.trim()) chunks.push(buffer.trim());
    return chunks;
  }

  let queue = [];
  let running = false;
  let session = 0;
  let currentAbort = null;
  let currentAudio = null;
  let onRateLimit = null;
  let rateNotified = false;
  const stateListeners = new Set();

  function setPlaying(isPlaying) {
    for (const fn of stateListeners) fn(isPlaying);
  }

  function onPlaybackChange(cb) {
    stateListeners.add(cb);
    return () => stateListeners.delete(cb);
  }

  async function fetchTtsPayload(text, opts, signal) {
    try {
      const res = await fetch('api/tts.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          text,
          lang: opts.lang || 'es',
          rate: opts.rate || 1,
        }),
        signal,
      });
      if (res.status === 429) return { rateLimited: true };
      if (!res.ok) return null;
      const data = await res.json();
      if (data && data.audio && !data.error) {
        return { audio: data.audio, mimeType: data.mimeType || 'audio/mpeg' };
      }
      return null;
    } catch {
      return null;
    }
  }

  function playPayload(payload, signal) {
    return new Promise((resolve) => {
      if (signal.aborted) return resolve(false);

      const audio = new Audio(`data:${payload.mimeType};base64,${payload.audio}`);
      currentAudio = audio;

      const onAbort = () => {
        audio.pause();
        resolve(false);
      };
      signal.addEventListener('abort', onAbort, { once: true });

      audio.addEventListener('canplay', () => {
        if (window.CadipelAssistant.lipsync) {
          window.CadipelAssistant.lipsync.tapPlaybackElement(audio);
        }
      }, { once: true });

      audio.addEventListener('ended', () => {
        signal.removeEventListener('abort', onAbort);
        resolve(true);
      });
      audio.addEventListener('error', () => {
        signal.removeEventListener('abort', onAbort);
        resolve(false);
      });

      audio.play().catch(() => resolve(false));
    });
  }

  async function drainQueue(opts) {
    if (running) return;
    running = true;
    const mySession = ++session;
    const abort = new AbortController();
    currentAbort = abort;
    const alive = () => mySession === session && !abort.signal.aborted;

    setPlaying(true);

    try {
      let prefetched = null;
      if (queue.length > 0) {
        prefetched = await fetchTtsPayload(queue[0], opts, abort.signal);
      }

      while (queue.length > 0 && alive()) {
        const chunk = queue.shift();
        const payload = prefetched;
        prefetched = null;
        if (payload && payload.rateLimited) {
          noteRateLimit();
          break;
        }

        let nextFetch = null;
        if (queue.length > 0 && alive()) {
          nextFetch = fetchTtsPayload(queue[0], opts, abort.signal).catch(() => null);
        }

        if (payload) {
          await playPayload(payload, abort.signal);
        }
        if (!alive()) break;

        if (nextFetch) prefetched = await nextFetch;
        void chunk;
      }
    } finally {
      // Tras stop() puede haber arrancado ya otra cola: solo la sesión vigente libera `running`.
      if (mySession === session) {
        running = false;
        queue = [];
        setPlaying(false);
        if (window.CadipelAssistant.lipsync) window.CadipelAssistant.lipsync.stop();
      }
    }
  }

  function noteRateLimit() {
    queue = [];
    if (rateNotified) return;
    rateNotified = true;
    if (onRateLimit) onRateLimit();
  }

  async function speak(text, opts) {
    const chunks = splitIntoTtsChunks(text);
    if (chunks.length === 0) return;
    stop();
    queue = chunks;
    await drainQueue(opts || {});
  }

  /** Agrega frases a la cola sin interrumpir la reproducción en curso (para streaming). */
  function enqueue(text, opts) {
    if (rateNotified) return;
    const chunks = splitIntoTtsChunks(text);
    if (chunks.length === 0) return;
    queue.push(...chunks);
    if (!running) void drainQueue(opts || {});
  }

  function stop() {
    rateNotified = false;
    session++;
    if (currentAbort) currentAbort.abort();
    if (currentAudio) {
      try { currentAudio.pause(); } catch {}
    }
    queue = [];
    running = false;
    setPlaying(false);
    if (window.CadipelAssistant.lipsync) window.CadipelAssistant.lipsync.stop();
  }

  function isPlaying() {
    return running;
  }

  window.CadipelAssistant.tts = {
    speak, enqueue, stop, isPlaying, onPlaybackChange, splitIntoTtsChunks, setOnRateLimit(fn) { onRateLimit = fn; },
  };
})();
