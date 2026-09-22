/**
 * Chat a pantalla completa del asistente Cadipel: conversaciones múltiples (localStorage),
 * streaming SSE, Markdown, copiar/regenerar/detener, sugerencias de seguimiento, dos modos de
 * voz — dictado (mantener presionado el mic, con lock y swipe-cancel, puerto literal de
 * assistant-voice-hold.js) y una pantalla de conversación por voz con orbe "mantené para
 * hablar" —, lectura en voz alta (TTS) y avatar con lip-sync.
 */
(function () {
  'use strict';

  const CA = window.CadipelAssistant;
  const Store = window.CadipelStore;
  const Md = window.CadipelMd;

  const LANG_KEY = 'cadipel_chat_lang';
  const THEME_KEY = 'cadipel_chat_theme';
  const CHIP_KEYS = ['chip_1', 'chip_2', 'chip_3', 'chip_4'];

  const $ = (id) => document.getElementById(id);
  const els = {
    app: $('app'), sidebarToggle: $('sidebar_toggle'), backdrop: $('sidebar_backdrop'),
    newChat: $('new_chat_btn'), convList: $('conv_list'), exportBtn: $('export_btn'),
    scroll: $('messages_scroll'), messages: $('messages'),
    composer: $('composer'), textField: $('text_field'), input: $('input'),
    sendBtn: $('send_btn'), langBtn: $('lang_btn'), langBtnSidebar: $('lang_btn_sidebar'),
    themeBtn: $('theme_btn'), themeBtnSidebar: $('theme_btn_sidebar'),
    toast: $('toast'), headAvatar: $('head_avatar'),
    // Dictado (hold-to-talk en el composer)
    holdStrip: $('hold_strip'), holdTrashAnchor: $('hold_trash_anchor'), holdTime: $('hold_time'),
    holdWave: $('hold_wave'), holdCancelHint: $('hold_cancel_hint'), replayStrip: $('replay_strip'),
    micSlot: $('mic_slot'), micLockZone: $('mic_lock_zone'), micBtn: $('mic_btn'),
    lockedActions: $('locked_actions'), lockedCancelBtn: $('locked_cancel_btn'), lockedSendBtn: $('locked_send_btn'),
    voiceToggleBtn: $('voice_toggle_btn'),
    // Pantalla de modo voz
    voiceView: $('voice_view'), backTextBtn: $('back_text_btn'), voiceAvatarHost: $('voice_avatar'),
    voiceCaption: $('voice_caption'), voiceCaptionBody: $('voice_caption_body'), voiceStopTtsBtn: $('voice_stop_tts_btn'),
    voiceOrbWrap: $('voice_orb_wrap'), voiceOrb: $('voice_orb'), voiceTimer: $('voice_timer'), voiceStatus: $('voice_status'),
  };

  // ---------- Estado ----------
  const state = {
    lang: 'es',
    dict: { es: {}, en: {} },
    streaming: false,
    abort: null,
    transcribing: false,
    voiceMode: false,
  };
  const persistentAvatars = []; // cabecera + pantalla de voz: viven toda la sesión
  let welcomeAvatar = null;      // se crea/destruye junto con la pantalla de bienvenida

  const store = (fn) => { try { return fn(); } catch { return undefined; } };

  function t(key) {
    return (state.dict[state.lang] && state.dict[state.lang][key]) || (state.dict.es && state.dict.es[key]) || key;
  }

  function toast(msg) {
    els.toast.textContent = msg;
    els.toast.classList.add('is-visible');
    clearTimeout(toast.timer);
    toast.timer = setTimeout(() => els.toast.classList.remove('is-visible'), 2600);
  }

  // ---------- i18n / tema ----------
  function langUrl(code) {
    const v = window.CADIPEL_LANG_V;
    return 'lang/chat/' + code + '.json' + (v ? ('?v=' + encodeURIComponent(v)) : '');
  }

  async function loadDict() {
    try {
      const [es, en] = await Promise.all([
        fetch(langUrl('es')).then((r) => r.json()),
        fetch(langUrl('en')).then((r) => r.json()),
      ]);
      state.dict = { es, en };
    } catch { /* sin diccionario: t() devuelve la key */ }
  }

  function initialLang() {
    const p = new URLSearchParams(location.search).get('lang');
    if (p === 'es' || p === 'en') return p;
    const s = store(() => localStorage.getItem(LANG_KEY));
    return s === 'en' ? 'en' : 'es';
  }

  function applyI18n() {
    document.documentElement.lang = state.lang;
    document.title = t('page_title');
    document.querySelectorAll('[data-i18n]').forEach((n) => { n.textContent = t(n.dataset.i18n); });
    document.querySelectorAll('[data-i18n-placeholder]').forEach((n) => { n.placeholder = t(n.dataset.i18nPlaceholder); });
    document.querySelectorAll('[data-i18n-aria-label]').forEach((n) => { n.setAttribute('aria-label', t(n.dataset.i18nAriaLabel)); });
    els.langBtn.textContent = els.langBtnSidebar.textContent = state.lang.toUpperCase();
    els.themeBtn.title = els.themeBtnSidebar.title = t('aria_theme');
  }

  function setLang(lang) {
    state.lang = lang;
    store(() => localStorage.setItem(LANG_KEY, lang));
    store(() => {
      const u = new URL(location.href);
      u.searchParams.set('lang', lang);
      history.replaceState(null, '', u);
    });
    applyI18n();
    renderAll();
  }

  const ICON_SUN = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>';
  const ICON_MOON = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>';

  function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    els.themeBtn.innerHTML = els.themeBtnSidebar.innerHTML = theme === 'dark' ? ICON_SUN : ICON_MOON;
  }
  function toggleTheme() {
    const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    store(() => localStorage.setItem(THEME_KEY, next));
    applyTheme(next);
  }

  // ---------- Avatares ----------
  function allAvatars() {
    return welcomeAvatar ? persistentAvatars.concat([welcomeAvatar]) : persistentAvatars;
  }
  function setActivity() {
    let activity = 'idle';
    if (CA.voice.isRecording()) activity = 'listening';
    else if (CA.tts.isPlaying()) activity = 'speaking';
    else if (state.streaming || state.transcribing) activity = 'thinking';
    allAvatars().forEach((a) => a.setActivity(activity));
    els.app.classList.toggle('is-busy', state.streaming);
    renderMicHold(micHold.getSnapshot());
    renderVoiceCaption();
  }

  // ---------- Parseo de la respuesta (texto + sugerencias) ----------
  function splitReply(acc) {
    let idx = acc.indexOf('[[');
    const single = acc.search(/\[\s*(?:sugerencias?|suggestions?)\s*:/i);
    if (single !== -1 && (idx === -1 || single < idx)) idx = single;
    if (idx === -1) {
      return { text: acc.endsWith('[') ? acc.slice(0, -1) : acc, suggestions: [] };
    }
    const text = acc.slice(0, idx).trimEnd();
    const m = /\[{1,2}\s*(?:sugerencias?|suggestions?)\s*:\s*([^\]]*)\]{1,2}/i.exec(acc.slice(idx));
    const suggestions = m
      ? m[1].split('|').map((s) => s.trim()).filter(Boolean).slice(0, 3)
      : [];
    return { text, suggestions };
  }

  /** Texto apto para leer en voz alta (sin sintaxis Markdown). */
  function speechClean(s) {
    return s
      .replace(/\[([^\]]+)\]\(https?:\/\/[^)]+\)/g, '$1')
      .replace(/https?:\/\/\S+/g, '')
      .replace(/[*_`#>]/g, '')
      .replace(/^\s*[-•]\s+/gm, '')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function extractSentences(buf, minLen, isFinal) {
    const out = [];
    let pos = 0;
    const re = /(?:[.!?…]+\s+|\n\n)/g;
    let m;
    while ((m = re.exec(buf)) !== null) {
      const end = m.index + m[0].length;
      const sentence = buf.slice(pos, end).trim();
      if (sentence.length >= minLen) {
        out.push(sentence);
        pos = end;
      }
    }
    const rest = buf.slice(pos);
    if (isFinal && rest.trim().length >= 1) {
      out.push(rest.trim());
      return [out, ''];
    }
    return [out, rest];
  }

  // ---------- Streaming SSE ----------
  class HttpError extends Error {
    constructor(status) { super(`HTTP ${status}`); this.status = status; }
  }

  async function streamReply(messages, onText, signal) {
    const res = await fetch('api/ai_stream.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ messages, lang: state.lang }),
      signal,
    });
    if (!res.ok || !res.body) throw new HttpError(res.status);

    const reader = res.body.getReader();
    const decoder = new TextDecoder();
    let acc = '';
    let buf = '';
    while (true) {
      const { done, value } = await reader.read();
      if (done) break;
      buf += decoder.decode(value, { stream: true });
      const lines = buf.split('\n');
      buf = lines.pop() || '';
      for (const line of lines) {
        if (!line.startsWith('data: ')) continue;
        const payload = line.slice(6);
        if (payload === '[DONE]') return acc;
        let parsed;
        try { parsed = JSON.parse(payload); } catch { continue; }
        if (parsed.error) throw new Error(parsed.error);
        if (parsed.text) {
          acc += parsed.text;
          onText(acc);
        }
      }
    }
    return acc;
  }

  // ---------- Render ----------
  function nearBottom() {
    const s = els.scroll;
    return s.scrollHeight - s.scrollTop - s.clientHeight < 140;
  }
  function scrollToBottom() {
    els.scroll.scrollTop = els.scroll.scrollHeight;
  }

  function node(tag, cls, html) {
    const n = document.createElement(tag);
    if (cls) n.className = cls;
    if (html != null) n.innerHTML = html;
    return n;
  }

  const ICON_COPY = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V6a2 2 0 0 1 2-2h9"/></svg>';
  const ICON_REDO = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8M21 3v5h-5M21 12a9 9 0 0 1-15.5 6.2L3 16M3 21v-5h5"/></svg>';
  const ICON_SPEAK = '<svg class="ic_speak" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5 6 9H2v6h4l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/></svg>';
  const ICON_STOP_SM = '<svg class="ic_stop_sm" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>';

  // Reproducción de una respuesta puntual (botón "Escuchar" de cada burbuja) — a diferencia del
  // modo voz, acá nada se lee automáticamente; es una acción explícita por mensaje.
  let speakingKey = null;
  function setSpeakingKey(key) {
    speakingKey = key;
    document.querySelectorAll('.act_btn--speak').forEach((b) => {
      b.classList.toggle('is-playing', b.dataset.key === key);
    });
  }
  function toggleSpeakMessage(key, text) {
    if (speakingKey === key && CA.tts.isPlaying()) {
      CA.tts.stop();
      return;
    }
    CA.tts.stop();
    CA.tts.speak(speechClean(text), { lang: state.lang, rate: 1 });
    setSpeakingKey(key);
  }

  function chipRow(list, onPick) {
    const row = node('div', 'chips');
    list.forEach((text) => {
      const b = node('button', 'chip');
      b.type = 'button';
      b.textContent = text;
      b.addEventListener('click', () => onPick(text));
      row.appendChild(b);
    });
    return row;
  }

  function renderWelcome() {
    const w = node('div', 'welcome');
    const host = node('div', 'welcome_avatar');
    w.appendChild(host);
    w.appendChild(node('h1', 'welcome_title')).textContent = t('welcome_title');
    w.appendChild(node('p', 'welcome_text')).textContent = t('welcome_text');
    w.appendChild(chipRow(CHIP_KEYS.map(t), (text) => void send(text)));
    els.messages.appendChild(w);
    welcomeAvatar = CA.avatar.create(host, { size: 112 });
  }

  function clearWelcomeAvatar() {
    if (welcomeAvatar) {
      welcomeAvatar.destroy();
      welcomeAvatar = null;
    }
  }

  function botRow(conv, i, isLast) {
    const msg = conv.messages[i];
    const row = node('div', 'msg msg--bot');
    const bubble = node('div', 'bubble md');
    if (msg.error && !msg.silent) bubble.classList.add('bubble--error');
    if (!msg.content && state.streaming && isLast) {
      bubble.innerHTML = '<span class="typing"><span></span><span></span><span></span></span>';
    } else {
      bubble.innerHTML = Md.render(msg.content);
    }
    row.appendChild(bubble);

    const streamingThis = state.streaming && isLast;
    if (!streamingThis && msg.content && !msg.silent) {
      const actions = node('div', 'msg_actions');
      if (!msg.error) {
        const key = `${conv.id}:${i}`;
        const speakBtn = node('button', 'act_btn act_btn--speak', ICON_SPEAK + ICON_STOP_SM);
        speakBtn.type = 'button';
        speakBtn.dataset.key = key;
        speakBtn.title = t('listen');
        speakBtn.setAttribute('aria-label', t('listen'));
        speakBtn.classList.toggle('is-playing', speakingKey === key);
        speakBtn.addEventListener('click', () => toggleSpeakMessage(key, msg.content));
        actions.appendChild(speakBtn);

        const copy = node('button', 'act_btn', ICON_COPY);
        copy.type = 'button';
        copy.title = t('copy');
        copy.setAttribute('aria-label', t('copy'));
        copy.addEventListener('click', () => copyText(msg.content));
        actions.appendChild(copy);
      }
      if (isLast) {
        const redo = node('button', 'act_btn', ICON_REDO);
        redo.type = 'button';
        redo.title = t('regenerate');
        redo.setAttribute('aria-label', t('regenerate'));
        redo.addEventListener('click', () => void regenerate());
        actions.appendChild(redo);
      }
      row.appendChild(actions);
    }
    if (isLast && !state.streaming && msg.suggestions && msg.suggestions.length) {
      row.appendChild(chipRow(msg.suggestions, (text) => void send(text)));
    }
    return row;
  }

  function renderMessages() {
    const conv = Store.active();
    els.messages.innerHTML = '';
    clearWelcomeAvatar();
    els.app.classList.toggle('is-empty', !conv || conv.messages.length === 0);
    if (!conv || conv.messages.length === 0) {
      renderWelcome();
    } else {
      conv.messages.forEach((m, i) => {
        if (m.role === 'user') {
          const row = node('div', 'msg msg--user');
          row.appendChild(node('div', 'bubble')).textContent = m.content;
          els.messages.appendChild(row);
        } else {
          els.messages.appendChild(botRow(conv, i, i === conv.messages.length - 1));
        }
      });
    }
    setActivity();
  }

  function renderConvList() {
    const active = Store.active();
    els.convList.innerHTML = '';
    Store.all().filter((c) => c.messages.length > 0).forEach((c) => {
      const li = node('li', 'conv_item' + (active && active.id === c.id ? ' is-active' : ''));
      const open = node('button', 'conv_open');
      open.type = 'button';
      open.textContent = c.title || t('untitled');
      open.addEventListener('click', () => { openConversation(c.id); closeSidebar(); });
      const del = node('button', 'conv_del', '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>');
      del.type = 'button';
      del.title = t('delete');
      del.setAttribute('aria-label', t('delete'));
      del.addEventListener('click', (e) => { e.stopPropagation(); deleteConversation(c); });
      li.appendChild(open);
      li.appendChild(del);
      els.convList.appendChild(li);
    });
  }

  function renderAll() {
    renderConvList();
    renderMessages();
    scrollToBottom();
  }

  /** Actualiza solo la última burbuja durante el streaming (sin reconstruir todo). */
  function patchLastBubble(conv) {
    const last = conv.messages[conv.messages.length - 1];
    const bubble = els.messages.querySelector('.msg--bot:last-child .bubble');
    if (!bubble) return renderMessages();
    const stick = nearBottom();
    bubble.innerHTML = last.content
      ? Md.render(last.content)
      : '<span class="typing"><span></span><span></span><span></span></span>';
    if (stick) scrollToBottom();
    renderVoiceCaption();
  }

  /** Última respuesta/pregunta relevante de la conversación activa, para la pantalla de voz. */
  function renderVoiceCaption() {
    const conv = Store.active();
    const msgs = conv ? conv.messages : [];
    const last = msgs[msgs.length - 1];
    if ((state.transcribing || state.streaming) && !(last && last.role === 'assistant' && last.content)) {
      els.voiceCaptionBody.innerHTML = '<span class="typing"><span></span><span></span><span></span></span>';
      return;
    }
    els.voiceCaptionBody.innerHTML = Md.render(last ? last.content : t('welcome_text'));
  }

  // ---------- Acciones de conversación ----------
  function openConversation(id) {
    stopEverything();
    Store.setActive(id);
    renderAll();
  }

  function newChat() {
    stopEverything();
    Store.create();
    renderAll();
    els.input.focus();
  }

  function deleteConversation(c) {
    if (c.messages.length && !confirm(t('confirm_delete'))) return;
    if (Store.active() && Store.active().id === c.id) stopEverything();
    Store.remove(c.id);
    renderAll();
  }

  function stopEverything() {
    if (state.abort) state.abort.abort();
    CA.tts.stop();
    if (CA.voice.isRecording()) CA.voice.cancelRecording();
  }

  async function copyText(text) {
    try {
      await navigator.clipboard.writeText(text);
    } catch {
      const ta = document.createElement('textarea');
      ta.value = text;
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); } catch { /* ignore */ }
      ta.remove();
    }
    toast(t('copied'));
  }

  function exportChat() {
    const conv = Store.active();
    if (!conv || conv.messages.length === 0) return toast(t('export_empty'));
    const who = { user: t('export_you'), assistant: t('export_assistant') };
    const body = conv.messages
      .filter((m) => !m.error)
      .map((m) => `${who[m.role]}:\n${m.content}`)
      .join('\n\n');
    const head = `${t('page_title')}\n${new Date(conv.updated).toLocaleString(state.lang === 'en' ? 'en-US' : 'es-AR')}\n\n`;
    const blob = new Blob([head + body + '\n'], { type: 'text/plain;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `cadipel-chat-${new Date(conv.updated).toISOString().slice(0, 10)}.txt`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    setTimeout(() => URL.revokeObjectURL(a.href), 1000);
  }

  // ---------- Envío ----------
  function autoGrow() {
    els.input.style.height = 'auto';
    els.input.style.height = Math.min(160, els.input.scrollHeight) + 'px';
  }

  async function send(text) {
    text = (text || '').trim();
    if (!text || state.streaming || state.transcribing) return;
    let conv = Store.active();
    if (!conv) conv = Store.create();
    conv.messages.push({ role: 'user', content: text });
    Store.touch(conv);
    els.input.value = '';
    autoGrow();
    await runTurn(conv);
  }

  async function regenerate() {
    const conv = Store.active();
    if (!conv || state.streaming) return;
    const last = conv.messages[conv.messages.length - 1];
    if (!last || last.role !== 'assistant') return;
    conv.messages.pop();
    await runTurn(conv);
  }

  /**
   * Historial para la API. Los mensajes de error no se envían, y dos turnos seguidos del mismo
   * rol se funden: Gemini rechaza user/user (pasa tras un error o un refresh a mitad de respuesta).
   */
  function apiHistory(messages) {
    const out = [];
    for (const m of messages) {
      if (!m || m.error) continue;
      const role = m.role === 'assistant' ? 'assistant' : 'user';
      let content = String(m.content || '').trim();
      if (role === 'assistant' && m.suggestions && m.suggestions.length) {
        content = `${content}\n[[sugerencias: ${m.suggestions.join(' | ')}]]`.trim();
      }
      if (!content) continue;
      const prev = out[out.length - 1];
      if (prev && prev.role === role) prev.content += `\n\n${content}`;
      else out.push({ role, content });
    }
    while (out.length && out[0].role !== 'user') out.shift();
    return out.slice(-20);
  }

  async function runTurn(conv) {
    const history = apiHistory(conv.messages);
    const reply = { role: 'assistant', content: '' };
    conv.messages.push(reply);

    state.streaming = true;
    state.abort = new AbortController();
    const abort = state.abort;
    // En modo voz la respuesta siempre se lee en voz alta (como en la pantalla de voz original).
    // En modo texto no se auto-lee: para eso está el botón "Escuchar" de cada respuesta (botRow).
    const speak = state.voiceMode;
    let spokenPos = 0;
    let firstChunkSent = false;
    CA.tts.stop();
    renderAll();
    scrollToBottom();

    try {
      const final = await streamReply(history, (acc) => {
        const { text, suggestions } = splitReply(acc);
        reply.content = text;
        reply.suggestions = suggestions;
        patchLastBubble(conv);
        if (speak) {
          const clean = speechClean(text);
          const [ready, rest] = extractSentences(clean.slice(spokenPos), firstChunkSent ? 110 : 30, false);
          if (ready.length) {
            spokenPos += clean.slice(spokenPos).length - rest.length;
            firstChunkSent = true;
            CA.tts.enqueue(ready.join(' '), { lang: state.lang, rate: 1 });
          }
        }
      }, abort.signal);

      const { text, suggestions } = splitReply(final);
      reply.content = text.trim();
      reply.suggestions = suggestions;
      if (speak && reply.content) {
        const clean = speechClean(reply.content);
        const [tail] = extractSentences(clean.slice(spokenPos), 1, true);
        if (tail.length) CA.tts.enqueue(tail.join(' '), { lang: state.lang, rate: 1 });
      }
      if (!reply.content) {
        reply.content = t('err_generic');
        reply.error = true;
      }
    } catch (e) {
      if (e && e.name === 'AbortError') {
        if (!reply.content) conv.messages.pop();
      } else {
        reply.content = t(e instanceof HttpError && e.status === 429 ? 'err_rate' : 'err_generic');
        reply.error = true;
        reply.suggestions = [];
      }
    } finally {
      state.streaming = false;
      state.abort = null;
      Store.touch(conv);
      renderAll();
      scrollToBottom();
    }
  }

  // ---------- Modo voz (pantalla dedicada) ----------
  function setVoiceMode(on) {
    state.voiceMode = on;
    els.app.classList.toggle('voice-mode', on);
    if (!on) CA.tts.stop();
    setActivity();
  }

  /**
   * Aviso del asistente sobre la voz (mic denegado, no se entendió, límite de uso) como un mensaje
   * más de la conversación — igual que un error de red, pero sin el estilo rojo ni acciones, y
   * excluido del historial que se manda a la IA (mismo mecanismo que `error`, ver runTurn/history).
   */
  function addNotice(text) {
    let conv = Store.active();
    if (!conv) conv = Store.create();
    conv.messages.push({ role: 'assistant', content: text, error: true, silent: true });
    Store.touch(conv);
    renderAll();
    scrollToBottom();
  }

  // ---------- Dictado por voz: transcribe + envía (dictado y orbe comparten este turno) ----------
  async function finishVoiceTurn(recording) {
    state.transcribing = true;
    setActivity();
    let transcript = '';
    let rateLimited = false;
    try {
      transcript = await CA.voice.transcribe(recording, state.lang);
    } catch (e) {
      rateLimited = !!(e && e.message === 'rate');
    }
    state.transcribing = false;
    setActivity();
    if (rateLimited) return addNotice(t('err_rate'));
    if (!transcript) return addNotice(t('stt_repeat'));
    await send(transcript);
  }

  // ---------- Gesto "mantener presionado para hablar" — puerto literal de assistant-voice-hold.js ----------
  const micHold = CA.voiceHold.create({
    isRecording: () => CA.voice.isRecording(),
    isSending: () => state.streaming || state.transcribing,
    isTtsPlaying: () => CA.tts.isPlaying(),
    getRecordingStartedAt: () => CA.voice.getRecordingStartedAt(),
    voiceBarEl: els.voiceOrbWrap,
    textBarEl: els.composer,
    stopSpeaking: () => CA.tts.stop(),
    async startRecording(mode) {
      try {
        await CA.voice.requestMic();
      } catch {
        addNotice(t('mic_denied'));
        return;
      }
      try { CA.micFeedback.micFeedbackRecordStart(); } catch { /* ignore */ }
      await CA.voice.startRecording();
      setActivity();
      void mode;
    },
    async finishRecording() {
      const recording = await CA.voice.stopRecording();
      setActivity();
      if (recording) await finishVoiceTurn(recording);
    },
    cancelRecording() {
      CA.voice.cancelRecording();
      try { CA.micFeedback.micFeedbackRecordCancel(); } catch { /* ignore */ }
      setActivity();
    },
  });

  let replayBuilt = false;

  /** Puerto literal de MozoMicTrashReplay: tapa + cuerpo con clip-path, el mic "cae" al tacho. */
  function buildTrashReplay() {
    const uid = Math.random().toString(36).slice(2, 10);
    const bodyClip = `trash_body_clip_${uid}`;
    const lidClip = `trash_lid_clip_${uid}`;
    const TRASH_PATH_MAIN = 'M23 5C23 5.553 22.553 6 22 6H21.114L19.837 19.472C19.593 22.053 17.453 24 14.86 24H9.13202C6.54302 24 4.40302 22.057 4.15502 19.479L2.85902 6H1.99902C1.44702 6 0.999023 5.553 0.999023 5C0.999023 4.447 1.44702 4 1.99902 4H6.10002C6.56502 1.721 8.58502 0 10.999 0H12.999C15.413 0 17.434 1.721 17.898 4H21.999C22.552 4 23 4.447 23 5ZM8.17202 4H15.828C15.415 2.836 14.304 2 13 2H11C9.69602 2 8.58502 2.836 8.17202 4ZM19.106 6H4.87002L6.14802 19.287C6.29602 20.834 7.58002 22 9.13402 22H14.862C16.418 22 17.701 20.832 17.848 19.283L19.106 6Z';
    const TRASH_PATH_SLOT_L = 'M10 18C10.2652 18 10.5196 17.8946 10.7071 17.7071C10.8946 17.5196 11 17.2652 11 17V11C11 10.7348 10.8946 10.4804 10.7071 10.2929C10.5196 10.1054 10.2652 10 10 10C9.73478 10 9.48043 10.1054 9.29289 10.2929C9.10536 10.4804 9 10.7348 9 11V17C9 17.2652 9.10536 17.5196 9.29289 17.7071C9.48043 17.8946 9.73478 18 10 18Z';
    const TRASH_PATH_SLOT_R = 'M14 18C14.2652 18 14.5196 17.8946 14.7071 17.7071C14.8946 17.5196 15 17.2652 15 17V11C15 10.7348 14.8946 10.4804 14.7071 10.2929C14.5196 10.1054 14.2652 10 14 10C13.7348 10 13.4804 10.1054 13.2929 10.2929C13.1054 10.4804 13 10.7348 13 11V17C13 17.2652 13.1054 17.5196 13.2929 17.7071C13.4804 17.8946 13.7348 18 14 18Z';
    const paths = `
      <path d="${TRASH_PATH_MAIN}" class="mic_trash_shape" />
      <path d="${TRASH_PATH_SLOT_L}" class="mic_trash_shape" />
      <path d="${TRASH_PATH_SLOT_R}" class="mic_trash_shape" />
    `;
    return node('div', 'mic_trash_bin mic_trash_bin--replay', `
      <div class="mic_trash_drop_mic">
        <img src="img/icons/microphone_icon.svg" alt="" width="16" height="16" class="mic_drop_icon" />
      </div>
      <svg class="mic_trash_svg mic_trash_svg--body" viewBox="0 0 24 24" width="26" height="26">
        <defs><clipPath id="${bodyClip}"><rect x="0" y="6.2" width="24" height="17.8" /></clipPath></defs>
        <g class="mic_trash_body_grp" clip-path="url(#${bodyClip})">${paths}</g>
      </svg>
      <svg class="mic_trash_svg mic_trash_svg--lid" viewBox="0 0 24 24" width="26" height="26">
        <defs><clipPath id="${lidClip}"><rect x="0" y="0" width="24" height="6.2" /></clipPath></defs>
        <g class="mic_trash_lid_grp" clip-path="url(#${lidClip})">${paths}</g>
      </svg>
    `);
  }

  const holdWaveBars = CA.voiceHold.createWaveBars(els.holdWave, 12);
  let lastMicLevels = null;

  function renderMicHold(snap) {
    const isDictationHold = snap.mode === 'dictation' && snap.holding;
    const showHoldStrip = isDictationHold && !snap.micLocked;
    const showLockedStrip = snap.mode === 'dictation' && snap.micLocked;
    const showReplay = snap.mode === 'dictation' && snap.cancelReplay;
    const showIdleText = !showHoldStrip && !showLockedStrip && !showReplay;

    els.composer.classList.toggle('is-holding', !showIdleText);
    els.input.style.display = showIdleText ? '' : 'none';
    els.holdStrip.hidden = !(showHoldStrip || showLockedStrip);
    els.replayStrip.hidden = !showReplay;
    if (showReplay && !replayBuilt) {
      els.replayStrip.appendChild(buildTrashReplay());
      replayBuilt = true;
    }

    els.composer.classList.toggle('is-swiping-cancel', snap.cancelProgress > 0.04);
    els.composer.classList.toggle('is-cancel', snap.micCancelArmed || showReplay);
    els.textField.style.setProperty('--cancel-progress', String(snap.cancelProgress));
    els.holdTrashAnchor.style.display = showLockedStrip ? 'none' : '';
    els.holdCancelHint.style.display = showLockedStrip ? 'none' : '';

    els.holdTime.textContent = snap.recordTimeLocked;
    holdWaveBars.update(CA.voice.isRecording() ? lastMicLevels : null);

    els.micLockZone.style.display = (showHoldStrip && snap.cancelProgress < 0.12) ? '' : 'none';
    els.micLockZone.classList.toggle('is-armed', snap.lockProgress > 0.45);
    els.micSlot.style.setProperty('--lock-progress', String(snap.lockProgress));

    els.micBtn.classList.toggle('is-recording', isDictationHold);
    els.micBtn.classList.toggle('is-cancel-armed', snap.micCancelArmed);

    const micVisible = !showLockedStrip;
    const hasText = els.input.value.trim().length > 0;
    els.micSlot.style.display = micVisible ? '' : 'none';
    els.sendBtn.style.display = (state.streaming || (micVisible && hasText)) ? '' : 'none';
    els.voiceToggleBtn.style.display = (micVisible && !hasText && !state.streaming) ? '' : 'none';
    els.lockedActions.hidden = !showLockedStrip;
    els.lockedSendBtn.disabled = !snap.lockedSendReady;

    els.sendBtn.classList.toggle('is-stop', state.streaming);
    els.sendBtn.disabled = !state.streaming && !hasText;
    els.sendBtn.setAttribute('aria-label', t(state.streaming ? 'aria_stop' : 'aria_send'));

    // Orbe de la pantalla de voz
    const orbLive = snap.mode === 'conversation' && snap.voiceOrbLive;
    const orbPressing = snap.mode === 'conversation' && snap.holding;
    els.voiceOrb.classList.toggle('is-pressing', orbPressing);
    els.voiceOrb.classList.toggle('is-live', orbLive);
    els.voiceOrbWrap.classList.toggle('is-live', orbLive);
    els.voiceTimer.style.visibility = orbLive ? 'visible' : 'hidden';
    els.voiceTimer.textContent = snap.recordTimeLocked;
    els.voiceStatus.textContent = (state.streaming || state.transcribing) && !CA.tts.isPlaying()
      ? t('responding')
      : orbLive ? t('release_to_send') : t('hold_to_talk');

    const showStopTts = CA.tts.isPlaying() && !CA.voice.isRecording();
    els.voiceStopTtsBtn.hidden = !showStopTts;
    els.voiceCaption.classList.toggle('is-tts', showStopTts);
  }

  // ---------- Sidebar (móvil) ----------
  function openSidebar() { els.app.classList.add('sidebar_open'); }
  function closeSidebar() { els.app.classList.remove('sidebar_open'); }

  // ---------- Init ----------
  async function init() {
    await loadDict();
    state.lang = initialLang();

    applyTheme(document.documentElement.getAttribute('data-theme') || 'light');
    applyI18n();

    persistentAvatars.push(CA.avatar.create(els.headAvatar, { size: 40 }));
    persistentAvatars.push(CA.avatar.create(els.voiceAvatarHost, { size: 128 }));
    if (CA.lipsync) {
      CA.lipsync.subscribeMouthLevel(({ level, spectrum }) => allAvatars().forEach((a) => a.setMouthLevel(level, spectrum)));
    }
    CA.tts.setOnRateLimit(() => toast(t('err_rate')));
    CA.tts.onPlaybackChange((isPlaying) => {
      setActivity();
      if (!isPlaying) setSpeakingKey(null);
    });
    CA.voice.onLevels((levels) => {
      lastMicLevels = levels;
      const avg = levels.reduce((a, b) => a + b, 0) / levels.length;
      els.voiceOrbWrap.style.setProperty('--cadipel-mic-level', String(Math.min(1, avg)));
      els.micSlot.style.setProperty('--cadipel-mic-level', String(Math.min(1, avg)));
      if (CA.voice.isRecording()) holdWaveBars.update(levels);
    });


    if (!Store.active()) {
      const existing = Store.all().find((c) => c.messages.length > 0);
      if (existing) Store.setActive(existing.id); else Store.create();
    }

    micHold.onChange(renderMicHold);
    els.input.addEventListener('input', () => { autoGrow(); renderMicHold(micHold.getSnapshot()); });
    els.input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
        e.preventDefault();
        void send(els.input.value);
      }
    });
    els.sendBtn.addEventListener('click', () => {
      if (state.streaming) { stopEverything(); return; }
      void send(els.input.value);
    });
    els.micBtn.addEventListener('pointerdown', (e) => micHold.handlePointerDown(e, 'dictation'));
    els.micBtn.addEventListener('contextmenu', (e) => e.preventDefault());
    els.voiceOrb.addEventListener('pointerdown', (e) => micHold.handlePointerDown(e, 'conversation'));
    els.voiceOrb.addEventListener('contextmenu', (e) => e.preventDefault());
    els.lockedCancelBtn.addEventListener('click', () => micHold.handleLockedCancel());
    els.lockedSendBtn.addEventListener('click', () => micHold.handleLockedSend());
    els.voiceStopTtsBtn.addEventListener('click', () => { CA.tts.stop(); setActivity(); });
    els.voiceToggleBtn.addEventListener('click', () => setVoiceMode(true));
    els.backTextBtn.addEventListener('click', () => setVoiceMode(false));
    els.newChat.addEventListener('click', () => { newChat(); closeSidebar(); });
    els.exportBtn.addEventListener('click', exportChat);
    els.langBtn.addEventListener('click', () => setLang(state.lang === 'es' ? 'en' : 'es'));
    els.langBtnSidebar.addEventListener('click', () => setLang(state.lang === 'es' ? 'en' : 'es'));
    els.themeBtn.addEventListener('click', toggleTheme);
    els.themeBtnSidebar.addEventListener('click', toggleTheme);
    els.sidebarToggle.addEventListener('click', () => (els.app.classList.contains('sidebar_open') ? closeSidebar() : openSidebar()));
    els.backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSidebar(); });

    // Varias pestañas comparten el mismo localStorage: al volver a esta pestaña o si otra guarda,
    // se relee para no pisar conversaciones (salvo mientras esta pestaña está respondiendo/grabando).
    const resync = () => {
      if (state.streaming || state.transcribing || CA.voice.isRecording()) return;
      Store.reload();
      renderConvList();
      renderMessages();
    };
    window.addEventListener('storage', (e) => { if (e.key === 'cadipel_chat_v2') resync(); });
    document.addEventListener('visibilitychange', () => { if (!document.hidden) resync(); });

    renderAll();
    renderMicHold(micHold.getSnapshot());
    els.input.focus();
  }

  init().finally(() => document.body.classList.add('is-ready'));
})();
