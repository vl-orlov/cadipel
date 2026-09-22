/**
 * Avatar del asistente Cadipel — visual portado de SecretaryAvatar.tsx (pri-bridge/ValerIA):
 * mismos assets (base + cejas/ojos por lado + 6 formas de boca en imagen), mismo layout
 * (secAvatarLayout.ts, canvas 512×512). El motor de lip-sync (nivel de audio real vía
 * AnalyserNode, con hysteresis y calibración) es el propio de Cadipel — ver assistant-lipsync.js —
 * y es más sofisticado que el de pri-bridge (que solo usa nivel/onda simulada), así que se
 * mantiene: acá solo cambia la capa de dibujo (imágenes en vez de paths SVG inline).
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const ASSET_BASE = '/img/assistant_avatar';

  const BLINK_MIN_MS = 5800;
  const BLINK_MAX_MS = 9500;
  const BLINK_SPEAKING_MIN_MS = 5200;
  const BLINK_SPEAKING_MAX_MS = 8800;
  const BLINK_DURATION_MS = 160;

  // ── Layout (fracciones del tamaño del avatar, canvas 512×512) — secAvatarLayout.ts ──
  const BROW_LEFT_LAYOUT = { top: 0.354, left: 0.379, width: 0.094 };
  const BROW_RIGHT_LAYOUT = { top: 0.354, left: 0.531, width: 0.090 };
  const EYE_LEFT_LAYOUT = { top: 0.379, left: 0.387, width: 0.084 };
  const EYE_RIGHT_LAYOUT = { top: 0.379, left: 0.539, width: 0.084 };
  const MOUTH_LAYOUT = { top: 0.527, left: 0.465, width: 0.080, height: 0.043 };

  // ── Formas de boca disponibles — sec_mouth_*.svg (pri-bridge) ──
  const MOUTH_SHAPES = ['neutral', 'smile', 'open_small', 'open_mid', 'open_wide', 'o'];

  // Nivel de audio (vocabulario del stabilizer de mozoAvatarMimic.ts) → forma de boca disponible.
  const MOUTH_SHAPE_TO_DESIGNER = {
    closed: 'neutral',
    smile: 'smile',
    soft: 'open_small',
    ah: 'open_mid',
    open: 'open_wide',
  };

  // ── Nivel de audio → forma de boca, con hysteresis — mozoAvatarMimic.ts ──
  const NEIGHBORS = {
    closed: ['closed', 'smile', 'soft'],
    smile: ['closed', 'smile', 'soft'],
    soft: ['closed', 'smile', 'soft', 'ah'],
    ah: ['soft', 'ah', 'open'],
    open: ['ah', 'open'],
  };
  const HOLD_MS = 130;
  const QUICK_HOLD_MS = 70;
  const BREATHING_LOW_MS = 420;
  const BREATHING_LEVEL = 0.05;

  function mouthTargetFromAudio(level) {
    if (level < 0.05) return 'closed';
    if (level < 0.11) return 'smile';
    if (level < 0.24) return 'soft';
    if (level < 0.5) return 'ah';
    return 'open';
  }

  function createMouthShapeStabilizer() {
    let displayed = 'closed';
    let candidate = 'closed';
    let candidateSince = 0;
    let lowLevelSince = 0;
    const VOICE_GAIN = 1.04; // calibrado para la voz femenina única del asistente

    return {
      update(level) {
        const now = performance.now();
        const adjustedLevel = Math.min(1, level * VOICE_GAIN);

        if (adjustedLevel < BREATHING_LEVEL) {
          if (!lowLevelSince) lowLevelSince = now;
          if (now - lowLevelSince >= BREATHING_LOW_MS) {
            const breath = adjustedLevel < 0.025 ? 'closed' : 'smile';
            candidate = breath;
            candidateSince = now;
            displayed = breath;
            return displayed;
          }
        } else {
          lowLevelSince = 0;
        }

        const target = mouthTargetFromAudio(adjustedLevel);
        if (target !== candidate) {
          candidate = target;
          candidateSince = now;
        }

        const held = now - candidateSince;
        const neighbor = (NEIGHBORS[displayed] || []).includes(target);
        const holdNeeded = neighbor ? QUICK_HOLD_MS : HOLD_MS;
        if (target !== displayed && held >= holdNeeded) displayed = target;

        return displayed;
      },
      reset() {
        displayed = 'closed';
        candidate = 'closed';
        candidateSince = 0;
        lowLevelSince = 0;
      },
    };
  }

  function el(tag, className) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    return node;
  }

  function overlayStyle(node, layout, zIndex) {
    node.style.position = 'absolute';
    node.style.top = `${layout.top * 100}%`;
    node.style.left = `${layout.left * 100}%`;
    node.style.width = `${layout.width * 100}%`;
    node.style.height = layout.height != null ? `${layout.height * 100}%` : 'auto';
    node.style.display = 'block';
    node.style.pointerEvents = 'none';
    node.style.zIndex = String(zIndex);
  }

  function create(container, opts) {
    const size = (opts && opts.size) || 96;

    const root = el('div', 'cadipel_avatar');
    root.style.width = size + 'px';
    root.style.height = size + 'px';

    // Rings (solo visibles al hablar)
    const ring1 = el('span', 'cadipel_avatar_ring cadipel_avatar_ring--1');
    const ring2 = el('span', 'cadipel_avatar_ring cadipel_avatar_ring--2');
    const ring3 = el('span', 'cadipel_avatar_ring cadipel_avatar_ring--3');
    root.appendChild(ring1);
    root.appendChild(ring2);
    root.appendChild(ring3);

    const head = el('div', 'cadipel_avatar_head');
    root.appendChild(head);

    const base = el('img', 'cadipel_avatar_layer cadipel_avatar_layer--base');
    base.src = `${ASSET_BASE}/avatar_base.png`;
    base.alt = '';
    base.draggable = false;
    head.appendChild(base);

    const browLeft = el('img', 'cadipel_avatar_brow');
    browLeft.alt = '';
    browLeft.draggable = false;
    overlayStyle(browLeft, BROW_LEFT_LAYOUT, 3);
    head.appendChild(browLeft);

    const browRight = el('img', 'cadipel_avatar_brow');
    browRight.alt = '';
    browRight.draggable = false;
    overlayStyle(browRight, BROW_RIGHT_LAYOUT, 3);
    head.appendChild(browRight);

    function setBrowsRaised(raised) {
      browLeft.src = `${ASSET_BASE}/avatar_brow_left${raised ? '_raised' : ''}.svg`;
      browRight.src = `${ASSET_BASE}/avatar_brow_right${raised ? '_raised' : ''}.svg`;
    }
    setBrowsRaised(false);

    function mkEye(layout, side, closed) {
      const img = el('img', 'cadipel_avatar_eye');
      img.src = `${ASSET_BASE}/avatar_eye_${side}${closed ? '_closed' : ''}.svg`;
      img.alt = '';
      img.draggable = false;
      overlayStyle(img, layout, 4);
      img.style.transition = 'opacity 0.07s';
      img.style.opacity = closed ? '0' : '1';
      head.appendChild(img);
      return img;
    }
    const eyeLeftOpen = mkEye(EYE_LEFT_LAYOUT, 'left', false);
    const eyeLeftClosed = mkEye(EYE_LEFT_LAYOUT, 'left', true);
    const eyeRightOpen = mkEye(EYE_RIGHT_LAYOUT, 'right', false);
    const eyeRightClosed = mkEye(EYE_RIGHT_LAYOUT, 'right', true);

    const mouthWrap = el('div', 'cadipel_avatar_layer--mouth');
    overlayStyle(mouthWrap, MOUTH_LAYOUT, 5);
    head.appendChild(mouthWrap);

    const mouthImgs = {};
    for (const shape of MOUTH_SHAPES) {
      const img = el('img', 'cadipel_avatar_mouth_shape');
      img.src = `${ASSET_BASE}/avatar_mouth_${shape}.svg`;
      img.alt = '';
      img.draggable = false;
      img.style.position = 'absolute';
      img.style.inset = '0';
      img.style.width = '100%';
      img.style.height = '100%';
      img.style.objectFit = 'contain';
      img.style.objectPosition = 'center center';
      img.style.opacity = shape === 'neutral' ? '1' : '0';
      img.style.transition = 'opacity 0.07s ease';
      mouthWrap.appendChild(img);
      mouthImgs[shape] = img;
    }

    container.appendChild(root);

    let activity = 'idle';
    let blinking = false;
    let blinkTimer = null;
    const stabilizer = createMouthShapeStabilizer();
    let activeShape = 'neutral';

    function setMouthShape(shape) {
      if (shape === activeShape) return;
      activeShape = shape;
      for (const key in mouthImgs) {
        mouthImgs[key].style.opacity = key === shape ? '1' : '0';
      }
    }

    function applyBlink() {
      eyeLeftOpen.style.opacity = blinking ? '0' : '1';
      eyeRightOpen.style.opacity = blinking ? '0' : '1';
      eyeLeftClosed.style.opacity = blinking ? '1' : '0';
      eyeRightClosed.style.opacity = blinking ? '1' : '0';
    }

    function scheduleBlink() {
      clearTimeout(blinkTimer);
      const speaking = activity === 'speaking';
      const min = speaking ? BLINK_SPEAKING_MIN_MS : BLINK_MIN_MS;
      const max = speaking ? BLINK_SPEAKING_MAX_MS : BLINK_MAX_MS;
      const delay = min + Math.random() * (max - min);
      const double = activity === 'idle' && Math.random() < 0.12;
      blinkTimer = setTimeout(() => {
        blinking = true;
        applyBlink();
        setTimeout(() => {
          blinking = false;
          applyBlink();
          if (double) {
            setTimeout(() => {
              blinking = true;
              applyBlink();
              setTimeout(() => { blinking = false; applyBlink(); scheduleBlink(); }, BLINK_DURATION_MS);
            }, 100);
          } else {
            scheduleBlink();
          }
        }, BLINK_DURATION_MS);
      }, delay);
    }
    scheduleBlink();

    function setActivity(next) {
      activity = next;
      root.setAttribute('data-activity', activity);
      setBrowsRaised(activity === 'listening' || activity === 'thinking');
      if (activity !== 'speaking') {
        stabilizer.reset();
        setMouthShape('neutral');
      }
      scheduleBlink();
    }

    function setMouthLevel(level) {
      if (activity !== 'speaking') return;
      const shape = stabilizer.update(Math.max(0, Math.min(1, level)));
      setMouthShape(MOUTH_SHAPE_TO_DESIGNER[shape] || 'neutral');
    }

    function destroy() {
      clearTimeout(blinkTimer);
      container.removeChild(root);
    }

    setActivity('idle');

    return { setActivity, setMouthLevel, destroy, el: root };
  }

  window.CadipelAssistant.avatar = { create };
})();
