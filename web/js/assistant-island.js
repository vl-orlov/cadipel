/**
 * Asistente IA de Cadipel — "isla" pegada a la cabecera (markup: includes/assistant_island.php).
 * - Forma única (clip-path + trazo SVG) que se recalcula al animar el ancho.
 * - Con scroll se esconde bajo la cabecera y queda una ruedita fina (handle).
 * - Panel de conversación: responde en streaming desde cadipel.pribridge.pro/api/ai_stream.php
 *   (mismo backend que el chat completo) y puede llevar al visitante por el sitio (navigate_site).
 */
(function () {
  'use strict';

  var island = document.getElementById('cadipel_island');
  if (!island) return;

  var isLocal = /^(localhost|127\.0\.0\.1)$/.test(location.hostname);
  var CHAT_ORIGIN = isLocal ? 'http://localhost:8899' : 'https://cadipel.pribridge.pro';
  var STORE_KEY = 'cadipel_island_v1';
  var MAX_HISTORY = 20;
  var NAV_DELAY_MS = 1400;

  // Lista cerrada de destinos: espejo de cadipel_site_targets() en chat/src/site_actions.php.
  var SITE_TARGETS = {
    soluciones: { anchor: 'soluciones' },
    contacto: { anchor: 'contacto' },
    nosotros: { page: 'nosotros' },
    companias_asociadas: { page: 'companias_asociadas' },
    lo_que_hacemos: { page: 'lo_que_hacemos' },
    casos_de_exito: { page: 'casos_de_exito' },
    ingenieria_desarrollo: { page: 'ingenieria_desarrollo' },
    fin_tech: { page: 'fin_tech' },
    soluciones_agro: { page: 'soluciones_agro' },
    automatizacion_industrial: { page: 'automatizacion_industrial' },
    soluciones_integrales: { page: 'soluciones_integrales' },
    soluciones_industria: { page: 'soluciones_industria' },
    seguridad_personal: { page: 'seguridad_personal' },
    sistemas_especiales: { page: 'sistemas_especiales' }
  };

  var TEXT = {
    es: {
      errRate: 'Hay muchas consultas en este momento. Probá de nuevo en un minuto o abrí el chat completo.',
      errGeneric: 'No pude responder ahora. Probá de nuevo o abrí el chat completo.',
      goTo: 'Ir a ',
      going: 'Te llevo a '
    },
    en: {
      errRate: 'Too many requests right now. Try again in a minute or open the full chat.',
      errGeneric: "I couldn't answer right now. Try again or open the full chat.",
      goTo: 'Go to ',
      going: 'Taking you to '
    }
  };

  function lang() {
    var el = document.getElementById('current_lang');
    return el && el.textContent.trim().toLowerCase() === 'en' ? 'en' : 'es';
  }
  function tr(key) { return TEXT[lang()][key]; }

  // ── Avatar ────────────────────────────────────────────────────────────────
  var avatarHost = document.getElementById('cadipel_bar_avatar');
  if (window.CadipelAssistant && CadipelAssistant.avatar) {
    CadipelAssistant.avatar.create(avatarHost, { size: 60 });
  }

  // ── Forma única cabecera + isla ───────────────────────────────────────────
  // La cápsula de la cabecera y la gota del isla se dibujan como UN solo contorno (vidrio, trazo y
  // resplandor comparten el mismo path): sin costura, el isla "sale" de la cabecera. Al esconderse o
  // aparecer, la gota se encoge/crece porque se recalcula cada frame mientras dura la animación.
  var body = island.querySelector('.cadipel_island_body');
  var container = document.querySelector('.header_container');
  var shellEls = null;

  function buildShell() {
    var shell = document.createElement('div');
    shell.className = 'cadipel_shell';
    shell.setAttribute('aria-hidden', 'true');
    shell.innerHTML =
      '<svg class="cadipel_shell_glow"><path id="cadipel_shell_glow_path"/></svg>' +
      '<div class="cadipel_shell_glass"></div>' +
      '<svg class="cadipel_shell_edge"><defs><linearGradient id="cadipel_shell_stroke" x1="0" y1="0" x2="0" y2="1">' +
      '<stop offset="0" stop-color="#fff" stop-opacity="0.85"/><stop offset="0.45" stop-color="#fff" stop-opacity="0.26"/>' +
      '<stop offset="1" stop-color="#fff" stop-opacity="0.9"/></linearGradient></defs>' +
      '<path id="cadipel_shell_edge_path" fill="none" stroke="url(#cadipel_shell_stroke)" stroke-width="1.4" stroke-linejoin="round"/></svg>';
    container.insertBefore(shell, container.firstChild);
    return {
      root: shell,
      glass: shell.querySelector('.cadipel_shell_glass'),
      glowSvg: shell.querySelector('.cadipel_shell_glow'),
      glowPath: shell.querySelector('#cadipel_shell_glow_path'),
      edgeSvg: shell.querySelector('.cadipel_shell_edge'),
      edgePath: shell.querySelector('#cadipel_shell_edge_path')
    };
  }

  function shape() {
    if (!container) return;
    if (!shellEls) {
      shellEls = buildShell();
      container.classList.add('has-shell');
    }
    var c = container.getBoundingClientRect();
    var W = c.width, P = c.height;
    var il = island.getBoundingClientRect();
    var bd = body.getBoundingClientRect();
    var cs = getComputedStyle(island);
    var F = parseFloat(cs.getPropertyValue('--island-flare')) || 0;
    var R = parseFloat(cs.getPropertyValue('--island-radius')) || 0;
    var h = Math.max(0, bd.bottom - c.bottom);          // cuánto cuelga la gota bajo la cápsula
    var Rp = P / 2;

    var d = 'M' + Rp + ',0 L' + (W - Rp) + ',0 A' + Rp + ',' + Rp + ' 0 0 1 ' + (W - Rp) + ',' + P;
    if (h >= 6) {
      var f = Math.min(F, h * 0.62);
      var r = Math.max(0, Math.min(R, h - f));
      var bl = il.left - c.left + F, br = il.right - c.left - F;   // bordes del cuerpo (sin las curvas)
      d += ' L' + (br + f) + ',' + P +
        ' A' + f + ',' + f + ' 0 0 0 ' + br + ',' + (P + f) +
        ' L' + br + ',' + (P + h - r) + ' A' + r + ',' + r + ' 0 0 1 ' + (br - r) + ',' + (P + h) +
        ' L' + (bl + r) + ',' + (P + h) + ' A' + r + ',' + r + ' 0 0 1 ' + bl + ',' + (P + h - r) +
        ' L' + bl + ',' + (P + f) + ' A' + f + ',' + f + ' 0 0 0 ' + (bl - f) + ',' + P;
    }
    d += ' L' + Rp + ',' + P + ' A' + Rp + ',' + Rp + ' 0 0 1 ' + Rp + ',0 Z';

    var H = P + h + 8;
    var root = shellEls.root;
    root.style.height = H + 'px';
    root.style.setProperty('--shell-pill', P + 'px');
    shellEls.glass.style.clipPath = "path('" + d + "')";
    [shellEls.glowSvg, shellEls.edgeSvg].forEach(function (svg) { svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H); });
    shellEls.glowPath.setAttribute('d', d);
    shellEls.edgePath.setAttribute('d', d);
  }

  var animUntil = 0, looping = false;
  function tick() {
    shape();
    if (performance.now() < animUntil) requestAnimationFrame(tick);
    else looping = false;
  }
  function kick(ms) {
    animUntil = Math.max(animUntil, performance.now() + (ms || 800));
    if (!looping) { looping = true; requestAnimationFrame(tick); }
  }
  kick();
  if (window.ResizeObserver) {
    var ro = new ResizeObserver(function () { kick(120); });
    ro.observe(body);
    if (container) ro.observe(container);
  }
  window.addEventListener('resize', function () { kick(120); });
  island.addEventListener('transitionrun', function () { kick(700); });
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(function () { kick(200); });

  // ── Elementos ─────────────────────────────────────────────────────────────
  var toggle = document.getElementById('cadipel_island_toggle');
  var form = document.getElementById('cadipel_bar_form');
  var input = form.elements.q;
  var micLink = document.getElementById('cadipel_island_mic');
  var bubble = document.getElementById('cadipel_island_bubble');
  var handle = document.getElementById('cadipel_island_handle');
  var messagesEl = document.getElementById('cadipel_panel_messages');
  var chipsEl = document.getElementById('cadipel_panel_chips');
  var fullBtn = document.getElementById('cadipel_panel_full');
  var newBtn = document.getElementById('cadipel_panel_new');
  var closeBtn = document.getElementById('cadipel_panel_close');
  var initialChips = Array.prototype.slice.call(chipsEl.children);
  var focusables = [input, micLink, newBtn, closeBtn].concat(initialChips);

  // ── Estado de la conversación (sessionStorage: sobrevive a la navegación entre páginas) ──
  var convo = [];            // [{ role, content, suggestions?: string[], action?: {target,label} }]
  var abortCtl = null;
  var navTimer = null;
  var streaming = false;

  function save() {
    try {
      sessionStorage.setItem(STORE_KEY, JSON.stringify({ convo: convo, open: island.classList.contains('is-open') && convo.length > 0 }));
    } catch (e) { /* storage bloqueado */ }
  }
  function load() {
    try {
      var raw = JSON.parse(sessionStorage.getItem(STORE_KEY) || 'null');
      if (raw && Array.isArray(raw.convo)) {
        convo = raw.convo.filter(function (m) {
          return m && (m.role === 'user' || m.role === 'assistant') && typeof m.content === 'string';
        });
        return !!raw.open;
      }
    } catch (e) { /* datos corruptos */ }
    return false;
  }

  // ── Render ────────────────────────────────────────────────────────────────
  function mdHtml(text) {
    return window.CadipelMd ? CadipelMd.render(text) : '<p>' + String(text).replace(/</g, '&lt;') + '</p>';
  }
  function el(tag, cls, html) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (html != null) n.innerHTML = html;
    return n;
  }
  function scrollMessages() { messagesEl.scrollTop = messagesEl.scrollHeight; }

  function actionButton(action) {
    var b = el('button', 'cadipel_msg_action');
    b.type = 'button';
    b.textContent = tr('goTo') + action.label + ' →';
    b.addEventListener('click', function () { runAction(action); });
    return b;
  }

  function botNode(m) {
    var wrap = document.createDocumentFragment();
    var bubbleEl = el('div', 'cadipel_msg cadipel_msg_bot' + (m.error ? ' cadipel_msg_error' : ''), mdHtml(m.content));
    wrap.appendChild(bubbleEl);
    if (m.action) wrap.appendChild(actionButton(m.action));
    return wrap;
  }

  function renderAll() {
    messagesEl.textContent = '';
    convo.forEach(function (m) {
      if (m.role === 'user') {
        var u = el('div', 'cadipel_msg cadipel_msg_user');
        u.textContent = m.content;
        messagesEl.appendChild(u);
      } else {
        messagesEl.appendChild(botNode(m));
      }
    });
    renderChips();
    scrollMessages();
  }

  function renderChips() {
    chipsEl.textContent = '';
    var last = convo[convo.length - 1];
    if (!convo.length) {
      initialChips.forEach(function (c) { chipsEl.appendChild(c); });
    } else if (!streaming && last && last.role === 'assistant' && last.suggestions && last.suggestions.length) {
      last.suggestions.forEach(function (text) {
        var c = el('button', 'cadipel_chip');
        c.type = 'button';
        c.textContent = text;
        chipsEl.appendChild(c);
      });
    }
    var open = island.classList.contains('is-open');
    Array.prototype.forEach.call(chipsEl.children, function (c) { c.tabIndex = open ? 0 : -1; });
  }

  // ── Respuesta (texto + sugerencias), mismo formato que el chat completo ──
  function splitReply(acc) {
    var idx = acc.indexOf('[[');
    var single = acc.search(/\[\s*(?:sugerencias?|suggestions?)\s*:/i);
    if (single !== -1 && (idx === -1 || single < idx)) idx = single;
    if (idx === -1) {
      return { text: acc.charAt(acc.length - 1) === '[' ? acc.slice(0, -1) : acc, suggestions: [] };
    }
    var m = /\[{1,2}\s*(?:sugerencias?|suggestions?)\s*:\s*([^\]]*)\]{1,2}/i.exec(acc.slice(idx));
    var suggestions = m ? m[1].split('|').map(function (s) { return s.trim(); }).filter(Boolean).slice(0, 3) : [];
    return { text: acc.slice(0, idx).trimEnd(), suggestions: suggestions };
  }

  // ── Acciones en el sitio ──────────────────────────────────────────────────
  function runAction(action) {
    var t = action && SITE_TARGETS[action.target];
    if (!t) return;
    clearTimeout(navTimer);
    // El panel se cierra para dejar ver el destino; la conversación queda guardada y vuelve al abrir la isla.
    setOpen(false);
    if (t.anchor) {
      var node = document.getElementById(t.anchor);
      if (node) node.scrollIntoView({ behavior: 'smooth', block: 'start' });
      else location.href = '?page=landing#' + t.anchor;
    } else {
      location.href = '?page=' + t.page;
    }
  }

  // ── Envío ─────────────────────────────────────────────────────────────────
  async function send(text) {
    text = (text || '').trim().slice(0, 500);
    if (!text) return;
    if (abortCtl) abortCtl.abort();
    clearTimeout(navTimer);
    setOpen(true, false);

    convo.push({ role: 'user', content: text });
    var userNode = el('div', 'cadipel_msg cadipel_msg_user');
    userNode.textContent = text;
    messagesEl.appendChild(userNode);
    var botBubble = el('div', 'cadipel_msg cadipel_msg_bot', '<span class="cadipel_typing"><i></i><i></i><i></i></span>');
    messagesEl.appendChild(botBubble);
    streaming = true;
    renderChips();
    scrollMessages();

    var reply = { role: 'assistant', content: '', suggestions: [] };
    abortCtl = new AbortController();
    var ctl = abortCtl;
    var fail = null;

    try {
      var history = convo.slice(-MAX_HISTORY).map(function (m) { return { role: m.role, content: m.content }; });
      var res = await fetch(CHAT_ORIGIN + '/api/ai_stream.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ messages: history, lang: lang(), site_actions: true }),
        signal: ctl.signal
      });
      if (!res.ok || !res.body) throw { status: res.status };

      var reader = res.body.getReader();
      var decoder = new TextDecoder();
      var acc = '', buf = '', done = false;
      while (!done) {
        var chunk = await reader.read();
        if (chunk.done) break;
        buf += decoder.decode(chunk.value, { stream: true });
        var lines = buf.split('\n');
        buf = lines.pop() || '';
        for (var i = 0; i < lines.length; i++) {
          if (lines[i].indexOf('data: ') !== 0) continue;
          var payload = lines[i].slice(6);
          if (payload === '[DONE]') { done = true; break; }
          var parsed;
          try { parsed = JSON.parse(payload); } catch (e) { continue; }
          if (parsed.error) throw { status: 0 };
          if (parsed.text) {
            acc += parsed.text;
            var s = splitReply(acc);
            reply.content = s.text;
            if (s.text) {
              botBubble.innerHTML = mdHtml(s.text);
              if (nearBottom()) scrollMessages();
            }
          }
          if (parsed.action && SITE_TARGETS[parsed.action.target]) {
            reply.action = { target: parsed.action.target, label: String(parsed.action.label || '') };
          }
        }
      }
      var final = splitReply(acc);
      reply.content = final.text;
      reply.suggestions = final.suggestions;
    } catch (err) {
      if (ctl.signal.aborted) return;
      fail = err && err.status === 429 ? tr('errRate') : tr('errGeneric');
    }

    if (ctl !== abortCtl) return;
    streaming = false;
    abortCtl = null;

    if (fail) {
      reply = { role: 'assistant', content: fail, error: true };
    } else if (!reply.content && reply.action) {
      reply.content = tr('going') + reply.action.label + '.';
    } else if (!reply.content) {
      reply = { role: 'assistant', content: tr('errGeneric'), error: true };
    }
    convo.push(reply);
    botBubble.replaceWith(botNode(reply));
    renderChips();
    scrollMessages();
    save();

    if (reply.action) {
      navTimer = setTimeout(function () { runAction(reply.action); }, NAV_DELAY_MS);
    }
  }
  function nearBottom() {
    return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight < 80;
  }

  function resetConversation() {
    if (abortCtl) abortCtl.abort();
    abortCtl = null;
    streaming = false;
    clearTimeout(navTimer);
    convo = [];
    save();
    renderAll();
    input.focus();
  }

  // ── Abrir / cerrar la isla ────────────────────────────────────────────────
  var closeTimer, bubbleTimer, bubbleHideTimer;

  function hideBubble() {
    clearTimeout(bubbleTimer);
    clearTimeout(bubbleHideTimer);
    bubble.classList.remove('is-visible');
  }
  function setOpen(on, focus) {
    clearTimeout(closeTimer);
    island.classList.toggle('is-open', on);
    toggle.setAttribute('aria-expanded', on ? 'true' : 'false');
    focusables.forEach(function (n) { n.tabIndex = on ? 0 : -1; });
    Array.prototype.forEach.call(chipsEl.children, function (c) { c.tabIndex = on ? 0 : -1; });
    if (on) {
      hideBubble();
      try { sessionStorage.setItem('cadipel_island_seen', '1'); } catch (e) {}
      if (focus) setTimeout(function () { input.focus(); }, 250);
    } else {
      clearTimeout(navTimer);
    }
    save();
  }
  function scheduleClose() {
    clearTimeout(closeTimer);
    closeTimer = setTimeout(function () {
      // Con una conversación en curso no se cierra sola: se cierra con la X, Esc o un clic afuera.
      if (convo.length === 0 && input.value.trim() === '' && !island.contains(document.activeElement)) setOpen(false);
    }, 900);
  }

  document.getElementById('cadipel_island_mini_search').addEventListener('click', function () { setOpen(true, true); });
  toggle.addEventListener('click', function () { setOpen(!island.classList.contains('is-open'), true); });
  bubble.addEventListener('click', function () { setOpen(true, true); });
  closeBtn.addEventListener('click', function () { setOpen(false); toggle.focus(); });
  newBtn.addEventListener('click', resetConversation);
  handle.addEventListener('click', function () { setOpen(true, true); });
  if (window.matchMedia('(hover: hover)').matches) {
    island.addEventListener('mouseenter', function () { setOpen(true, false); });
    island.addEventListener('mouseleave', scheduleClose);
    handle.addEventListener('mouseenter', function () { setOpen(true, false); });
  }
  island.addEventListener('focusout', function (e) {
    if (!island.contains(e.relatedTarget)) scheduleClose();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && island.classList.contains('is-open')) {
      setOpen(false);
      toggle.focus();
    }
  });
  document.addEventListener('click', function (e) {
    if (!island.classList.contains('is-open') || input.value.trim() !== '') return;
    // composedPath se calcula al despachar el clic: sigue incluyendo la isla aunque el botón pulsado
    // (chip, acción) ya se haya quitado del DOM al re-renderizar.
    var path = e.composedPath ? e.composedPath() : [];
    if (path.indexOf(island) !== -1 || path.indexOf(handle) !== -1) return;
    setOpen(false);
  });

  // ── Formulario, chips, chat completo ─────────────────────────────────────
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var q = input.value;
    input.value = '';
    send(q);
  });
  chipsEl.addEventListener('click', function (e) {
    var chip = e.target.closest('.cadipel_chip');
    if (chip) send(chip.textContent);
  });
  fullBtn.addEventListener('click', function () {
    var url = CHAT_ORIGIN + '/?lang=' + lang() + '&mode=text';
    if (convo.length) {
      var h = convo.filter(function (m) { return !m.error; }).slice(-12)
        .map(function (m) { return { role: m.role, content: m.content.slice(0, 1500) }; });
      url += '#h=' + encodeURIComponent(JSON.stringify(h));
    }
    window.open(url, '_blank', 'noopener');
  });

  // ── Ancla con scroll: el isla se esconde bajo la cabecera y queda una ruedita fina ──
  var DOCK_AT = 140, UNDOCK_AT = 60;
  var docked = false, ticking = false;
  function updateDock() {
    ticking = false;
    var y = window.pageYOffset || document.documentElement.scrollTop || 0;
    var next = docked ? y > UNDOCK_AT : y > DOCK_AT;
    if (next === docked) return;
    docked = next;
    island.classList.toggle('is-docked', docked);
    handle.tabIndex = docked ? 0 : -1;
    if (docked) {
      hideBubble();
      if (island.classList.contains('is-open') && convo.length === 0 && input.value.trim() === '' && !island.contains(document.activeElement)) {
        setOpen(false);
      }
    }
  }
  window.addEventListener('scroll', function () {
    if (!ticking) { ticking = true; requestAnimationFrame(updateDock); }
  }, { passive: true });

  // ── Arranque ──────────────────────────────────────────────────────────────
  var restoredOpen = load();
  renderAll();
  updateDock();
  requestAnimationFrame(function () { island.classList.add('is-animated'); });
  if (restoredOpen && convo.length) setOpen(true, false);

  var seen = false;
  try { seen = sessionStorage.getItem('cadipel_island_seen') === '1'; } catch (e) {}
  if (!seen && !restoredOpen) {
    bubbleTimer = setTimeout(function () {
      if (docked) return;
      bubble.classList.add('is-visible');
      bubbleHideTimer = setTimeout(hideBubble, 9000);
    }, 1200);
  }
})();
