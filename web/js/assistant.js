/**
 * Orquestador del asistente Cadipel: botón flotante + burbuja (puerto de
 * FloatingAssistant.tsx) y panel de chat texto/voz (puerto de Mozo.tsx),
 * usando assistant-chat.js / assistant-tts.js / assistant-voice.js /
 * assistant-voice-hold.js / assistant-avatar.js.
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const DISMISS_KEY = 'cadipel_assistant_dismissed';
  const SHOW_DELAY_MS = 1000;
  const HIDE_AFTER_MS = 40000;
  const RESHOW_AFTER_MS = 120000;
  const TTS_SPEEDS = [1, 1.25, 1.5, 1.75, 2];
  const SPEED_KEY = 'cadipel_assistant_tts_speed';
  const GENDER_KEY = 'cadipel_assistant_tts_gender';

  function getStoredSpeed() {
    const v = Number(localStorage.getItem(SPEED_KEY));
    return TTS_SPEEDS.includes(v) ? v : 1;
  }
  function getStoredGender() {
    return localStorage.getItem(GENDER_KEY) === 'm' ? 'm' : 'f';
  }

  let i18nDict = { es: {}, en: {} };

  function currentLang() {
    const l = localStorage.getItem('lang');
    return l === 'en' ? 'en' : 'es';
  }

  function t(key) {
    const dict = i18nDict[currentLang()] || {};
    return dict[key] || key;
  }
  window.CadipelAssistant.i18n = { t, currentLang };

  async function loadI18n() {
    try {
      const [es, en] = await Promise.all([
        fetch('/lang/assistant/es.json').then((r) => r.json()),
        fetch('/lang/assistant/en.json').then((r) => r.json()),
      ]);
      i18nDict = { es, en };
    } catch {
      // sin traducciones cargadas, t() devuelve la key como fallback visible
    }
  }

  function el(tag, className, attrs) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (attrs) for (const k in attrs) node.setAttribute(k, attrs[k]);
    return node;
  }

  document.addEventListener('DOMContentLoaded', init);

  async function init() {
    const root = document.getElementById('cadipel-assistant-root');
    if (!root) return;

    await loadI18n();

    const state = {
      panelOpen: false,
      voiceMode: false,
      expanded: true,
      busy: false,
      ttsSpeed: getStoredSpeed(),
      ttsGender: getStoredGender(),
    };
    // ---------- Floating button + bubble (camia-float__*: columna, burbuja arriba) ----------
    const float = el('div', 'cadipel_assistant_float');

    const bubble = el('div', 'cadipel_assistant_bubble', { role: 'button', tabindex: '0' });
    const bubbleClose = el('button', 'cadipel_assistant_bubble_close', { type: 'button', 'aria-label': t('assistant_close_aria') });
    bubbleClose.textContent = '✕';
    const bubbleEyebrow = el('p', 'cadipel_assistant_bubble_eyebrow');
    bubbleEyebrow.textContent = t('assistant_panel_title_short');
    const bubbleText = el('p', 'cadipel_assistant_bubble_text');
    bubbleText.textContent = t('assistant_bubble_greeting');
    bubble.appendChild(bubbleClose);
    bubble.appendChild(bubbleEyebrow);
    bubble.appendChild(bubbleText);

    const btn = el('button', 'cadipel_assistant_btn', { type: 'button', 'aria-label': t('assistant_panel_title') });
    const btnRing = el('span', 'cadipel_assistant_btn_ring', { 'aria-hidden': 'true' });
    const btnAvatarHost = el('span', 'cadipel_assistant_btn_avatar');
    btn.appendChild(btnRing);
    btn.appendChild(btnAvatarHost);
    const btnAvatar = window.CadipelAssistant.avatar.create(btnAvatarHost, { size: 58 });

    float.appendChild(bubble);
    float.appendChild(btn);
    root.appendChild(float);

    let showTimer, hideTimer;
    let dismissed = sessionStorage.getItem(DISMISS_KEY) === '1';

    function showBubble() {
      if (dismissed || state.panelOpen) return;
      bubble.classList.add('is-visible');
      hideTimer = setTimeout(hideBubble, HIDE_AFTER_MS);
    }
    function hideBubble() {
      clearTimeout(hideTimer);
      bubble.classList.remove('is-visible');
      if (!dismissed) showTimer = setTimeout(showBubble, RESHOW_AFTER_MS);
    }
    function dismissBubble(e) {
      e.preventDefault();
      e.stopPropagation();
      dismissed = true;
      sessionStorage.setItem(DISMISS_KEY, '1');
      clearTimeout(hideTimer);
      clearTimeout(showTimer);
      bubble.classList.remove('is-visible');
    }
    bubbleClose.addEventListener('click', dismissBubble);
    bubble.addEventListener('click', () => openPanel());
    bubble.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openPanel(); }
    });
    if (!dismissed) showTimer = setTimeout(showBubble, SHOW_DELAY_MS);

    btn.addEventListener('click', () => {
      if (state.panelOpen) closePanel(); else openPanel();
    });

    // ---------- Chat panel ----------
    const panel = el('div', 'cadipel_assistant_panel', { role: 'dialog', 'aria-modal': 'true' });

    const header = el('div', 'cadipel_assistant_panel_header');

    const brandBtn = el('button', 'cadipel_assistant_brand', { type: 'button', 'aria-label': t('assistant_text_mode_aria') });
    const headerAvatarHost = el('span', 'cadipel_assistant_panel_header_avatar');
    const headerAvatar = window.CadipelAssistant.avatar.create(headerAvatarHost, { size: 36 });
    const brandText = el('span', 'cadipel_assistant_brand_text');
    const brandEyebrow = el('span', 'cadipel_assistant_eyebrow');
    brandEyebrow.textContent = t('assistant_eyebrow');
    const headerTitle = el('span', 'cadipel_assistant_panel_title');
    headerTitle.textContent = t('assistant_panel_title_short');
    brandText.appendChild(brandEyebrow);
    brandText.appendChild(headerTitle);
    brandBtn.appendChild(headerAvatarHost);
    brandBtn.appendChild(brandText);

    const tools = el('div', 'cadipel_assistant_tools');
    const speedBtn = el('button', 'cadipel_assistant_tool', { type: 'button', 'aria-label': t('assistant_speed_aria'), title: t('assistant_speed_aria') });
    const genderBtn = el('button', 'cadipel_assistant_tool', { type: 'button', 'aria-label': t('assistant_gender_aria'), title: t('assistant_gender_aria') });
    const chevronBtn = el('button', 'cadipel_assistant_tool cadipel_assistant_chevron', { type: 'button' });
    const headerClose = el('button', 'cadipel_assistant_tool cadipel_assistant_panel_close', { type: 'button', 'aria-label': t('assistant_close_aria') });
    headerClose.innerHTML = '✕';
    tools.appendChild(speedBtn);
    tools.appendChild(genderBtn);
    tools.appendChild(chevronBtn);
    tools.appendChild(headerClose);
    tools.addEventListener('click', (e) => e.stopPropagation());

    header.appendChild(brandBtn);
    header.appendChild(tools);
    headerClose.addEventListener('click', () => closePanel());
    brandBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      setExpanded(!state.expanded);
    });
    chevronBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      setExpanded(!state.expanded);
    });
    header.addEventListener('click', () => {
      if (!state.expanded) setExpanded(true);
    });

    const content = el('div', 'cadipel_assistant_panel_content');
    const body = el('div', 'cadipel_assistant_panel_body');

    // Text-mode message list
    const messages = el('div', 'cadipel_assistant_messages');

    // Voice-mode centerpiece
    const voiceView = el('div', 'cadipel_assistant_voice_view');
    const voiceAvatarHost = el('div', 'cadipel_assistant_voice_avatar_wrap');
    const voiceAvatar = window.CadipelAssistant.avatar.create(voiceAvatarHost, { size: 120 });
    const voiceCaption = el('div', 'cadipel_assistant_voice_caption');
    const voiceStopTtsBtn = el('button', 'cadipel_assistant_stop_tts_btn', { type: 'button', 'aria-label': t('assistant_stop_audio_aria') });
    voiceStopTtsBtn.innerHTML = '<img src="/img/icons/volume_mute_icon.svg" alt="" width="16" height="16" />';
    voiceStopTtsBtn.style.display = 'none';
    const voiceCaptionBody = el('div', 'cadipel_assistant_voice_caption_body');
    voiceCaption.appendChild(voiceStopTtsBtn);
    voiceCaption.appendChild(voiceCaptionBody);

    const voiceOrbWrap = el('div', 'cadipel_assistant_voice_orb_wrap');
    const voiceOrb = el('button', 'cadipel_assistant_voice_orb', { type: 'button', 'aria-label': t('assistant_voice_orb_aria') });
    const orbWave = el('span', 'cadipel_assistant_orb_wave', { 'aria-hidden': 'true' });
    orbWave.innerHTML = '<i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i>';
    voiceOrb.appendChild(orbWave);
    voiceOrbWrap.appendChild(voiceOrb);

    const voiceFooter = el('div', 'cadipel_assistant_voice_footer');
    const voiceTimer = el('span', 'cadipel_assistant_voice_timer');
    const voiceStatus = el('p', 'cadipel_assistant_voice_status');
    voiceStatus.textContent = t('assistant_hold_to_talk');
    voiceFooter.appendChild(voiceTimer);
    voiceFooter.appendChild(voiceStatus);

    const voiceHero = el('div', 'cadipel_assistant_voice_hero');
    voiceHero.appendChild(voiceAvatarHost);
    voiceHero.appendChild(voiceCaption);

    const voiceDock = el('div', 'cadipel_assistant_voice_dock');
    voiceDock.appendChild(voiceOrbWrap);
    voiceDock.appendChild(voiceFooter);

    voiceView.appendChild(voiceHero);
    voiceView.appendChild(voiceDock);

    body.appendChild(messages);
    body.appendChild(voiceView);

    // ---------- Input bar (text mode) ----------
    const inputBar = el('div', 'cadipel_assistant_inputbar');

    // idle: textarea
    const textField = el('div', 'cadipel_assistant_text_field');
    const textarea = el('textarea', 'cadipel_assistant_textarea', { rows: '1', placeholder: t('assistant_placeholder') });
    textField.appendChild(textarea);

    // holding / locked: strip with dot + timer + wave bars (+ cancel hint while swiping)
    const holdStrip = el('div', 'cadipel_mic_hold_strip');
    const holdTrashAnchor = el('div', 'cadipel_mic_hold_trash_anchor', { 'aria-hidden': 'true' });
    holdTrashAnchor.innerHTML = '<img src="/img/icons/eliminar.svg" class="cadipel_mic_hold_trash" alt="" />';
    const holdDot = el('span', 'cadipel_mic_locked_dot', { 'aria-hidden': 'true' });
    const holdTime = el('span', 'cadipel_mic_locked_time');
    holdTime.textContent = '0:00,00';
    const holdWaveWrap = el('div', 'cadipel_mic_locked_wave', { 'aria-hidden': 'true' });
    const holdWaveBarsEl = el('div', '');
    holdWaveWrap.appendChild(holdWaveBarsEl);
    const holdWaveBars = window.CadipelAssistant.voiceHold.createWaveBars(holdWaveBarsEl, 12);
    const holdCancelHint = el('span', 'cadipel_mic_hold_cancel_hint', { 'aria-hidden': 'true' });
    holdCancelHint.textContent = t('assistant_release_to_cancel');
    holdStrip.appendChild(holdTrashAnchor);
    holdStrip.appendChild(holdDot);
    holdStrip.appendChild(holdTime);
    holdStrip.appendChild(holdWaveWrap);
    holdStrip.appendChild(holdCancelHint);
    holdStrip.style.display = 'none';
    textField.appendChild(holdStrip);

    // cancel-replay: trash-can drop animation
    const replayStrip = el('div', 'cadipel_text_replay_strip', { 'aria-hidden': 'true' });
    replayStrip.appendChild(buildTrashReplay());
    replayStrip.style.display = 'none';
    textField.appendChild(replayStrip);

    inputBar.appendChild(textField);

    // mic slot: real hold-to-talk button + lock-zone tooltip above it
    const micSlot = el('div', 'cadipel_mic_slot');
    const micBtn = el('button', 'cadipel_mic_btn', { type: 'button', 'aria-label': t('assistant_mic_aria'), style: 'touch-action:none' });
    micBtn.innerHTML = '<img src="/img/icons/microphone_icon.svg" alt="" width="28" height="28" />';
    const lockZone = el('div', 'cadipel_mic_lock_zone', { 'aria-hidden': 'true' });
    lockZone.innerHTML = `
      <img src="/img/icons/mic_lock.svg" class="cadipel_mic_lock_icon" alt="" />
      <img src="/img/icons/mic_arrow_up.svg" class="cadipel_mic_lock_arrow" alt="" />
      <span class="cadipel_mic_lock_label">${t('assistant_lock_mic')}</span>
    `;
    micSlot.appendChild(lockZone);
    micSlot.appendChild(micBtn);
    inputBar.appendChild(micSlot);

    // locked actions: cancel (x) + send (arrow) — shown only while locked (hands-free)
    const lockedActions = el('div', 'cadipel_mic_locked_actions');
    const lockedCancelBtn = el('button', 'unset cadipel_mic_locked_cancel', { type: 'button', 'aria-label': t('assistant_cancel_recording_aria') });
    lockedCancelBtn.innerHTML = '<img src="/img/icons/mic_close.svg" alt="" width="12" height="12" />';
    const lockedSendBtn = el('button', 'unset cadipel_mic_locked_send', { type: 'button', 'aria-label': t('assistant_send_dictation_aria') });
    lockedSendBtn.innerHTML = '<img src="/img/icons/mic_arrow_up.svg" alt="" width="16" height="16" />';
    lockedActions.appendChild(lockedCancelBtn);
    lockedActions.appendChild(lockedSendBtn);
    lockedActions.style.display = 'none';
    inputBar.appendChild(lockedActions);

    const sendBtn = el('button', 'cadipel_assistant_send_btn', { type: 'button', 'aria-label': t('assistant_send_aria') });
    sendBtn.innerHTML = '<img src="/img/icons/camia_send.svg" alt="" width="18" height="18" />';
    const voiceToggleBtn = el('button', 'cadipel_assistant_voice_toggle_btn', { type: 'button', 'aria-label': t('assistant_voice_mode_aria') });
    voiceToggleBtn.innerHTML = '<img src="/img/icons/camia_voice.svg" alt="" width="44" height="44" />';
    inputBar.appendChild(voiceToggleBtn);
    inputBar.appendChild(sendBtn);

    content.appendChild(body);
    content.appendChild(inputBar);
    panel.appendChild(header);
    panel.appendChild(content);
    root.appendChild(panel);

    function chevronSvg(expanded) {
      const d = expanded ? 'M3 5.5 L7 9.5 L11 5.5' : 'M3 8.5 L7 4.5 L11 8.5';
      return `<svg width="14" height="14" viewBox="0 0 14 14"><path d="${d}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
    }

    /** Puerto literal de MozoMicTrashReplay (CamiaVoiceHold.tsx): tapa + cuerpo con clip-path, cae el mic. */
    function buildTrashReplay() {
      const uid = Math.random().toString(36).slice(2, 10);
      const bodyClip = `cadipel_trash_body_clip_${uid}`;
      const lidClip = `cadipel_trash_lid_clip_${uid}`;
      const TRASH_PATH_MAIN = 'M23 5C23 5.553 22.553 6 22 6H21.114L19.837 19.472C19.593 22.053 17.453 24 14.86 24H9.13202C6.54302 24 4.40302 22.057 4.15502 19.479L2.85902 6H1.99902C1.44702 6 0.999023 5.553 0.999023 5C0.999023 4.447 1.44702 4 1.99902 4H6.10002C6.56502 1.721 8.58502 0 10.999 0H12.999C15.413 0 17.434 1.721 17.898 4H21.999C22.552 4 23 4.447 23 5ZM8.17202 4H15.828C15.415 2.836 14.304 2 13 2H11C9.69602 2 8.58502 2.836 8.17202 4ZM19.106 6H4.87002L6.14802 19.287C6.29602 20.834 7.58002 22 9.13402 22H14.862C16.418 22 17.701 20.832 17.848 19.283L19.106 6Z';
      const TRASH_PATH_SLOT_L = 'M10 18C10.2652 18 10.5196 17.8946 10.7071 17.7071C10.8946 17.5196 11 17.2652 11 17V11C11 10.7348 10.8946 10.4804 10.7071 10.2929C10.5196 10.1054 10.2652 10 10 10C9.73478 10 9.48043 10.1054 9.29289 10.2929C9.10536 10.4804 9 10.7348 9 11V17C9 17.2652 9.10536 17.5196 9.29289 17.7071C9.48043 17.8946 9.73478 18 10 18Z';
      const TRASH_PATH_SLOT_R = 'M14 18C14.2652 18 14.5196 17.8946 14.7071 17.7071C14.8946 17.5196 15 17.2652 15 17V11C15 10.7348 14.8946 10.4804 14.7071 10.2929C14.5196 10.1054 14.2652 10 14 10C13.7348 10 13.4804 10.1054 13.2929 10.2929C13.1054 10.4804 13 10.7348 13 11V17C13 17.2652 13.1054 17.5196 13.2929 17.7071C13.4804 17.8946 13.7348 18 14 18Z';
      const paths = `
        <path d="${TRASH_PATH_MAIN}" class="cadipel_mic_trash_shape" />
        <path d="${TRASH_PATH_SLOT_L}" class="cadipel_mic_trash_shape" />
        <path d="${TRASH_PATH_SLOT_R}" class="cadipel_mic_trash_shape" />
      `;

      const bin = el('div', 'cadipel_mic_trash_bin cadipel_mic_trash_bin--replay');
      bin.innerHTML = `
        <div class="cadipel_mic_trash_drop_mic">
          <img src="/img/icons/microphone_icon.svg" alt="" width="16" height="16" class="cadipel_mic_drop_icon" />
        </div>
        <svg class="cadipel_mic_trash_svg cadipel_mic_trash_svg--body" viewBox="0 0 24 24" width="26" height="26">
          <defs><clipPath id="${bodyClip}"><rect x="0" y="6.2" width="24" height="17.8" /></clipPath></defs>
          <g class="cadipel_mic_trash_body_grp" clip-path="url(#${bodyClip})">${paths}</g>
        </svg>
        <svg class="cadipel_mic_trash_svg cadipel_mic_trash_svg--lid" viewBox="0 0 24 24" width="26" height="26">
          <defs><clipPath id="${lidClip}"><rect x="0" y="0" width="24" height="6.2" /></clipPath></defs>
          <g class="cadipel_mic_trash_lid_grp" clip-path="url(#${lidClip})">${paths}</g>
        </svg>
      `;
      return bin;
    }

    // ---------- Activity (avatar) wiring ----------
    const avatars = [btnAvatar, headerAvatar, voiceAvatar];
    function setActivity(activity) {
      for (const a of avatars) a.setActivity(activity);
    }
    function setMouthLevel(level, spectrum) {
      for (const a of avatars) a.setMouthLevel(level, spectrum);
    }
    if (window.CadipelAssistant.lipsync) {
      window.CadipelAssistant.lipsync.subscribeMouthLevel(({ level, spectrum }) => setMouthLevel(level, spectrum));
    }
    function updateActivity() {
      if (window.CadipelAssistant.voice.isRecording()) setActivity('listening');
      else if (window.CadipelAssistant.tts.isPlaying()) setActivity('speaking');
      else if (state.busy) setActivity('thinking');
      else setActivity('idle');
      // Recording/tts/busy can change without a mic-hold gesture event (e.g. right
      // after stopRecording() resolves inside finishRecording) — keep the orb/timer/
      // status text in sync with real voice.isRecording()/tts.isPlaying() either way.
      renderMicHold(micHold.getSnapshot());
    }
    window.CadipelAssistant.tts.onPlaybackChange(() => updateActivity());

    // ---------- Header tools: velocidad / voz / minimizar ----------
    function updateToolsUI() {
      speedBtn.textContent = state.ttsSpeed === 1 ? '1×' : `${state.ttsSpeed}×`;
      speedBtn.classList.toggle('cadipel_assistant_tool--active', state.ttsSpeed !== 1);
      speedBtn.style.display = state.expanded ? '' : 'none';
      genderBtn.innerHTML = `<img src="${state.ttsGender === 'f' ? '/img/icons/woman_voice_icon.svg' : '/img/icons/man_voice_icon.svg'}" alt="" width="14" height="14" />`;
      genderBtn.style.display = (state.expanded && state.voiceMode) ? '' : 'none';
      chevronBtn.innerHTML = chevronSvg(state.expanded);
      chevronBtn.setAttribute('aria-label', state.expanded ? t('assistant_minimize_aria') : t('assistant_expand_aria'));
    }
    function cycleSpeed() {
      const idx = TTS_SPEEDS.indexOf(state.ttsSpeed);
      state.ttsSpeed = TTS_SPEEDS[(idx + 1) % TTS_SPEEDS.length];
      localStorage.setItem(SPEED_KEY, String(state.ttsSpeed));
      updateToolsUI();
    }
    function toggleGender() {
      state.ttsGender = state.ttsGender === 'f' ? 'm' : 'f';
      localStorage.setItem(GENDER_KEY, state.ttsGender);
      updateToolsUI();
    }
    function setExpanded(v) {
      state.expanded = v;
      panel.classList.toggle('cadipel_assistant_panel--collapsed', !v);
      content.classList.toggle('cadipel_assistant_panel_content--closed', !v);
      updateToolsUI();
    }
    speedBtn.addEventListener('click', () => cycleSpeed());
    genderBtn.addEventListener('click', () => toggleGender());
    updateToolsUI();

    function ttsOptsForVoice() {
      return { lang: currentLang(), gender: state.ttsGender, rate: state.ttsSpeed };
    }

    // ---------- Panel open/close ----------
    function openPanel() {
      state.panelOpen = true;
      panel.classList.add('is-open');
      float.style.display = 'none';
      bubble.classList.remove('is-visible');
      clearTimeout(hideTimer);
      clearTimeout(showTimer);
      if (window.CadipelAssistant.chat.getEntries().length === 0) {
        window.CadipelAssistant.chat.loadPersisted(t('assistant_greeting_bot'));
      }
      setVoiceMode(true);
      renderMessages(window.CadipelAssistant.chat.getEntries());
      updateVoiceCaption(window.CadipelAssistant.chat.getEntries());
      textarea.focus();
    }
    function closePanel() {
      state.panelOpen = false;
      panel.classList.remove('is-open');
      float.style.display = '';
      if (window.CadipelAssistant.voice.isRecording()) window.CadipelAssistant.voice.cancelRecording();
      window.CadipelAssistant.tts.stop();
      updateActivity();
      if (!dismissed) showTimer = setTimeout(showBubble, RESHOW_AFTER_MS);
    }

    // ---------- Text <-> voice mode ----------
    function setVoiceMode(on) {
      state.voiceMode = on;
      panel.classList.toggle('cadipel_assistant_panel--voice', on);
      updateToolsUI();
      if (!on) {
        window.CadipelAssistant.tts.stop();
        updateActivity();
      }
    }
    voiceToggleBtn.addEventListener('click', () => setVoiceMode(true));

    const backToTextBtn = el('button', 'cadipel_assistant_back_text_btn', { type: 'button', 'aria-label': t('assistant_text_mode_aria') });
    backToTextBtn.innerHTML = '<img src="/img/icons/camia_text.svg" alt="" width="20" height="20" />';
    backToTextBtn.addEventListener('click', () => setVoiceMode(false));
    voiceView.appendChild(backToTextBtn);

    // ---------- Rendering ----------
    function scrollToBottom() {
      messages.scrollTop = messages.scrollHeight;
    }

    function renderMessages(entries) {
      messages.innerHTML = '';
      for (const entry of entries) {
        if (entry.role === 'typing') {
          const row = el('div', 'cadipel_assistant_msg cadipel_assistant_msg--bot');
          row.innerHTML = '<span class="cadipel_assistant_typing"><span></span><span></span><span></span></span>';
          messages.appendChild(row);
          continue;
        }
        const row = el('div', `cadipel_assistant_msg cadipel_assistant_msg--${entry.role}`);
        row.textContent = entry.content;
        messages.appendChild(row);
      }
      scrollToBottom();
    }

    /** Última entrada relevante (de atrás hacia adelante) — puntos si está "pensando". */
    function updateVoiceCaption(entries) {
      for (let i = entries.length - 1; i >= 0; i--) {
        const e = entries[i];
        if (e.role === 'typing') {
          voiceCaptionBody.innerHTML = '<span class="cadipel_assistant_typing"><span></span><span></span><span></span></span>';
          return;
        }
        if (e.role === 'user') {
          voiceCaptionBody.textContent = e.content;
          return;
        }
        if (e.role === 'bot' && e.content) {
          voiceCaptionBody.textContent = e.content;
          return;
        }
      }
      voiceCaptionBody.textContent = t('assistant_greeting_bot');
    }

    window.CadipelAssistant.chat.onEntriesChange((entries) => {
      renderMessages(entries);
      updateVoiceCaption(entries);
    });

    // ---------- Sending (typed text) ----------
    function autoGrow() {
      textarea.style.height = 'auto';
      textarea.style.height = Math.min(120, textarea.scrollHeight) + 'px';
    }
    textarea.addEventListener('input', autoGrow);
    textarea.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        void send();
      }
    });

    async function send() {
      const text = textarea.value.trim();
      if (!text || state.busy) return;
      textarea.value = '';
      autoGrow();
      state.busy = true;
      updateActivity();
      try {
        await window.CadipelAssistant.chat.sendText(text, { lang: currentLang(), voiceMode: false });
      } finally {
        state.busy = false;
        updateActivity();
      }
    }
    sendBtn.addEventListener('click', () => void send());

    // ---------- Hold-to-talk mic (dictation in text mode, voice orb in voice mode) ----------
    const micHold = window.CadipelAssistant.voiceHold.create({
      isRecording: () => window.CadipelAssistant.voice.isRecording(),
      isSending: () => state.busy,
      isTtsPlaying: () => window.CadipelAssistant.tts.isPlaying(),
      getRecordingStartedAt: () => window.CadipelAssistant.voice.getRecordingStartedAt(),
      voiceBarEl: voiceOrbWrap,
      textBarEl: inputBar,
      stopSpeaking: () => window.CadipelAssistant.tts.stop(),
      async startRecording(mode) {
        try {
          await window.CadipelAssistant.voice.requestMic();
        } catch {
          renderMicHoldError();
          return;
        }
        try { window.CadipelAssistant.micFeedback.micFeedbackRecordStart(); } catch { /* ignore */ }
        await window.CadipelAssistant.voice.startRecording();
        updateActivity();
        void mode;
      },
      async finishRecording() {
        const recording = await window.CadipelAssistant.voice.stopRecording();
        updateActivity();
        if (!recording) return;
        state.busy = true;
        updateActivity();
        try {
          await window.CadipelAssistant.chat.sendVoiceBlob(recording, {
            lang: currentLang(),
            voiceMode: state.voiceMode,
            ttsOpts: ttsOptsForVoice(),
          });
        } finally {
          state.busy = false;
          updateActivity();
        }
      },
      cancelRecording() {
        window.CadipelAssistant.voice.cancelRecording();
        try { window.CadipelAssistant.micFeedback.micFeedbackRecordCancel(); } catch { /* ignore */ }
        updateActivity();
      },
    });

    function renderMicHoldError() {
      const row = el('div', 'cadipel_assistant_msg cadipel_assistant_msg--bot');
      row.textContent = t('assistant_mic_denied');
      messages.appendChild(row);
      scrollToBottom();
    }

    function renderMicHold(snap) {
      const isDictationHold = snap.mode === 'dictation' && snap.holding;
      const showHoldStrip = isDictationHold && !snap.micLocked;
      const showLockedStrip = snap.mode === 'dictation' && snap.micLocked;
      const showReplay = snap.mode === 'dictation' && snap.cancelReplay;
      const showIdleText = !showHoldStrip && !showLockedStrip && !showReplay;

      textarea.style.display = showIdleText ? '' : 'none';
      holdStrip.style.display = (showHoldStrip || showLockedStrip) ? 'flex' : 'none';
      replayStrip.style.display = showReplay ? 'flex' : 'none';

      textField.classList.toggle('cadipel_assistant_text_field--swiping-cancel', snap.cancelProgress > 0.04);
      textField.classList.toggle('cadipel_assistant_text_field--cancel', snap.micCancelArmed || showReplay);
      textField.style.setProperty('--cancel-progress', String(snap.cancelProgress));
      holdTrashAnchor.style.display = showLockedStrip ? 'none' : '';
      holdCancelHint.style.display = showLockedStrip ? 'none' : '';

      holdTime.textContent = snap.recordTimeLocked;
      holdWaveBars.update(window.CadipelAssistant.voice.isRecording() ? lastMicLevels : null);

      lockZone.style.display = (showHoldStrip && snap.cancelProgress < 0.12) ? '' : 'none';
      lockZone.classList.toggle('cadipel_mic_lock_zone--armed', snap.lockProgress > 0.45);
      micSlot.style.setProperty('--lock-progress', String(snap.lockProgress));

      micBtn.classList.toggle('cadipel_mic_btn--recording', isDictationHold);
      micBtn.classList.toggle('cadipel_mic_btn--cancel-armed', snap.micCancelArmed);

      const micVisible = !showLockedStrip;
      micSlot.style.display = micVisible ? '' : 'none';
      sendBtn.style.display = (micVisible && hasInputText()) ? '' : 'none';
      voiceToggleBtn.style.display = (micVisible && !hasInputText()) ? '' : 'none';
      lockedActions.style.display = showLockedStrip ? 'flex' : 'none';
      lockedSendBtn.disabled = !snap.lockedSendReady;

      // Voice-mode orb
      const orbLive = snap.mode === 'conversation' && snap.voiceOrbLive;
      const orbPressing = snap.mode === 'conversation' && snap.holding;
      voiceOrb.classList.toggle('cadipel_assistant_voice_orb--pressing', orbPressing);
      voiceOrb.classList.toggle('cadipel_assistant_voice_orb--live', orbLive);
      voiceOrbWrap.classList.toggle('cadipel_assistant_voice_orb_wrap--live', orbLive);
      voiceTimer.style.display = orbLive ? '' : 'none';
      voiceTimer.textContent = snap.recordTimeLocked;
      voiceStatus.textContent = state.busy && !window.CadipelAssistant.tts.isPlaying()
        ? t('assistant_responding')
        : orbLive
          ? t('assistant_release_to_send')
          : t('assistant_hold_to_talk');

      const showStopTts = window.CadipelAssistant.tts.isPlaying() && !window.CadipelAssistant.voice.isRecording();
      voiceStopTtsBtn.style.display = showStopTts ? '' : 'none';
      voiceCaption.classList.toggle('cadipel_assistant_voice_caption--tts', showStopTts);
    }

    let lastMicLevels = null;
    window.CadipelAssistant.voice.onLevels((levels) => {
      lastMicLevels = levels;
      const avg = levels.reduce((a, b) => a + b, 0) / levels.length;
      voiceOrbWrap.style.setProperty('--cadipel-mic-level', String(Math.min(1, avg)));
      micSlot.style.setProperty('--cadipel-mic-level', String(Math.min(1, avg)));
      if (window.CadipelAssistant.voice.isRecording()) holdWaveBars.update(levels);
    });

    micHold.onChange(renderMicHold);
    renderMicHold(micHold.getSnapshot());

    micBtn.addEventListener('pointerdown', (e) => micHold.handlePointerDown(e, 'dictation'));
    micBtn.addEventListener('contextmenu', (e) => e.preventDefault());
    voiceOrb.addEventListener('pointerdown', (e) => micHold.handlePointerDown(e, 'conversation'));
    voiceOrb.addEventListener('contextmenu', (e) => e.preventDefault());
    lockedCancelBtn.addEventListener('click', () => micHold.handleLockedCancel());
    lockedSendBtn.addEventListener('click', () => micHold.handleLockedSend());
    voiceStopTtsBtn.addEventListener('click', () => {
      window.CadipelAssistant.tts.stop();
      updateActivity();
    });

    function hasInputText() {
      return textarea.value.trim().length > 0;
    }
    textarea.addEventListener('input', () => renderMicHold(micHold.getSnapshot()));
  }
})();
