/**
 * Avatar del asistente Cadipel — puerto literal de MozoAvatar.tsx (mi-oshka/CamIA):
 * mismos assets ilustrados (capas base/cejas/ojos + boca vectorial), mismo esquema
 * de colores (#F26545), mismo blink loop, mismo mapeo nivel→forma de boca
 * (mouthTargetFromAudio + stabilizer con hysteresis, de mozoAvatarMimic.ts).
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

  // ── Layout (fracciones del tamaño del avatar) — camiaAvatarLayout.ts (GIRL) ──
  const EYES_LAYOUT = { top: 0.352, left: 0.367, width: 0.243 };
  const BROWS_LAYOUT = { top: 0.336, left: 0.361, width: 0.218 };

  // ── Boca vectorial — mozoMouthSvg.ts (5 paths por forma, mismos colores) ──
  const ACX = 142;
  const ACY = 154;
  function mkT(cx, cy, s) {
    const tx = (ACX - cx * s).toFixed(2);
    const ty = (ACY - cy * s).toFixed(2);
    return `translate(${tx} ${ty}) scale(${s})`;
  }

  const SVG_MOUTH_SHAPES = {
    neutral: {
      paths: [
        { d: 'M17 10.5L0 18L21 20L39.5 14.5L59 0L35.5 4.5L17 10.5Z', fill: '#460D02' },
        { d: 'M19.5 20L28.5153 15.5H38.4999L19.5 20Z', fill: '#FBF5ED' },
        { d: 'M39.5 4.5L26.5 10L12.5 12L39.5 4.5Z', fill: '#FBF5ED' },
        { d: 'M59 0C54.5 12.5001 45.5 20.9999 31.5 25.4999C17.5 29.9999 6 23.5001 0 18C9 18 17 14.9999 27.5 11.9999C38 8.99988 49 6.50012 59 0Z', fill: '#EF5C47' },
        { d: 'M15 5.49995C11 7.21429 5.5 14.5 0 17.9999C11.5 16.9999 17 13.5 19 13C21 12.5 24 12.5 26.5 12C29 11.5 34.5178 8.56635 36.5 8C40 7 53 6 59 0C50.5 1.5 32.3 -0.2 29.5 1C26.7 2.2 24.6667 4.16668 23.5 5C22 4.66667 18.0318 4.20057 15 5.49995Z', fill: '#F76F53' },
      ],
      transform: mkT(29.5, 13.5, 0.35),
    },
    pressed: {
      paths: [
        { d: 'M19.5 10L0 15.5L19.5 15L42 9L59 0L39.5 4.5L19.5 10Z', fill: '#460D02' },
        { d: 'M19.9999 15L29 11L39 9.5L19.9999 15Z', fill: '#FBF5ED' },
        { d: 'M41 4.5L28 10L14 12L41 4.5Z', fill: '#FBF5ED' },
        { d: 'M59 0C46.5 10 39.5 12 30 14.5C20.5 17 13 17 0 15.5C10 14 17.5 14.5 28 11.5C38.5 8.5 43 6.5 59 0Z', fill: '#EF5C47' },
        { d: 'M17 6.99995C13 8.71429 7.5 13.5 0 15.5C12 15 17.5 13.5 20.5 13C22.5335 12.6611 25 13 28.5 12C32 11 36.0178 8.56635 38 8C41.5 7 52 5 59 0C50.5 2.5 34.3 1.3 31.5 2.5C28.7 3.7 26.6667 5.66668 25.5 6.5C24 6.16667 20.0318 5.70057 17 6.99995Z', fill: '#F76F53' },
      ],
      transform: mkT(29.5, 8.5, 0.35),
    },
    open_small: {
      paths: [
        { d: 'M17 10.212L0 22.212L24.5 26.712L40.5 19.712L59 4.21204C48.8888 6.70955 43.1891 6.56311 33 5.21204L17 10.212Z', fill: '#460D02' },
        { d: 'M41 5.21204C41 7.21204 37 11.6211 27.5 14.212C18 16.8029 13 15.712 12 12.212L41 5.21204Z', fill: '#FBF5ED' },
        { d: 'M14.1386 25.2954C8.5 18.212 18 19.712 29 16.212C40 12.712 46 7.21204 44.6386 16.7954L14.1386 25.2954Z', fill: '#FBF5ED' },
        { d: 'M59 4.21204C52.5 14.712 46.5 26.712 32.5 31.212C18.5 35.712 9 27.212 0 22.212C13 19.212 17 21.212 27.5 18.212C38 15.212 43.5 10.712 59 4.21204Z', fill: '#EF5C47' },
        { d: 'M13.5 5.21192C9.5 6.92627 5.5 18.7119 0 22.2119C11.5 21.2118 16 13.2121 18 12.7121C20 12.2121 22.5 12.7119 25 12.2119C27.5 11.7119 30.0178 9.27847 32 8.71212C35.5 7.71212 48.5 11.2121 59 4.21192C50.5 5.71192 35 -1.28796 30 0.212115C27.0822 1.08751 23.6667 4.37861 22.5 5.21192C21 4.87859 16.5318 3.91255 13.5 5.21192Z', fill: '#F76F53' },
      ],
      transform: mkT(29.5, 16.5, 0.35),
    },
    open_mid: {
      paths: [
        { d: 'M17 10.0059L0 22.0059L21.5 28.0059L42 23.5059L59 4.00586L36.5 5.50586L17 10.0059Z', fill: '#460D02' },
        { d: 'M40.5 5.00586C40.5 8.50586 38 12.5059 27 15.5059C16 18.5059 11 17.0059 10 13.5059L40.5 5.00586Z', fill: '#FBF5ED' },
        { d: 'M14 29.1144C7.00002 21.0059 18.5 23.6144 29.5 20.6144C40.5 17.6144 47 12.0059 44.5 20.6144L14 29.1144Z', fill: '#FBF5ED' },
        { d: 'M59 4.00586C51.5 14.2938 53 30.5059 33.5 35.5059C14 40.5059 11 26.2938 0 22.0059C10.5 19.7938 19 26.5059 29.5 23.5059C40 20.5059 47.5 10.7938 59 4.00586Z', fill: '#EF5C47' },
        { d: 'M13.5 5.21777C9.5 6.93212 5.5 18.5059 0 22.0058C11.5 21.0058 16 13.218 18 12.718C20 12.218 22.5 12.7178 25 12.2178C27.5 11.7178 30.0178 9.28432 32 8.71797C35.5 7.71797 48.5 11.0061 59 4.00586C50.5 5.50586 35 -1.28211 30 0.217966C27.0822 1.09336 23.6667 4.38446 22.5 5.21777C21 4.88444 16.5318 3.9184 13.5 5.21777Z', fill: '#F76F53' },
      ],
      transform: mkT(29.5, 18.5, 0.35),
    },
    open_wide: {
      paths: [
        { d: 'M9.5 10.7524C7.12266 17.8521 5.06757 21.5778 0 27.7524C9.16829 30.2129 11.7471 36.1392 18.5 39.7524L38.5 35.7524C39.9028 25.9117 42.2852 20.9024 49 12.7524C39.0941 12.3725 35.0124 9.8642 27.5 5.75244L9.5 10.7524Z', fill: '#460D02' },
        { d: 'M35 6.25244C35 8.25244 32.064 13.2721 21.5 15.2524C10.936 17.2328 6.5 15.7524 6 13.2524L35 6.25244Z', fill: '#FBF5ED' },
        { d: 'M17.0001 38.7524C5.5001 27.2524 16.0381 35.0282 27.0381 31.5282C38.0381 28.0282 41.5 21.2524 36 34.2524L17.0001 38.7524Z', fill: '#FBF5ED' },
        { d: 'M49 12.7524C43.5 21.7524 50.5 40.7524 31 45.7524C11.5 50.7524 8.5 34.2524 0 27.7524C10.5 25.5404 16.5 37.7524 27 34.7524C37.5 31.7524 37.5 19.5404 49 12.7524Z', fill: '#EF5C47' },
        { d: 'M8.5 4.25257C2.5 6.82409 2.5 15.2526 0 27.7526C8 21.7525 10.5 13.0025 13.5 12.2525C15.5 11.7525 17 12.7525 19.5 12.2525C22 11.7525 23.5178 10.3189 25.5 9.75251C29 8.75251 38 15.7525 49 12.7525C41.5 9.75249 31 -1.84761 24 0.252581C21.0822 1.128 19.5 3.75258 17.5 4.25257C15.5 4.75257 11.5318 2.9532 8.5 4.25257Z', fill: '#F76F53' },
      ],
      transform: mkT(24.5, 23.5, 0.35),
    },
    o_sound: {
      paths: [
        { d: 'M6.75745 10.6663L0.257446 25.1663L15.2574 32.6663L26.7574 28.6663L36.2574 13.6663L21.2574 6.66626L6.75745 10.6663Z', fill: '#460D02' },
        { d: 'M28.2574 7.66626C28.2574 7.66626 24.2574 13.6663 16.2574 15.1663C8.25745 16.6663 2.25745 13.6663 2.25745 13.6663L28.2574 7.66626Z', fill: '#FBF5ED' },
        { d: 'M11.2575 33.6663L20.2728 29.1663H30.2574L11.2575 33.6663Z', fill: '#FBF5ED' },
        { d: 'M36.2574 13.6663C33.2574 23.1663 38.7574 35.3201 23.7574 39.1663C8.75745 43.0124 7.25745 32.6663 0.257446 25.1663C11.2574 21.1663 15.7574 27.6663 19.2574 26.6663C22.7574 25.6663 24.2574 17.1663 36.2574 13.6663Z', fill: '#EF5C47' },
        { d: 'M4.75741 3.66605C1.25739 5.16611 -0.742563 13.666 0.257411 25.166C9.75745 22.1662 8.25743 13.916 11.2574 13.166C13.2574 12.666 13.5528 13.407 14.7574 13.166C16.2574 12.866 16.2752 12.2326 18.2574 11.6662C22.9524 10.3248 25.2574 18.6662 36.2574 13.6662C30.7574 7.6663 22.2574 -1.33411 17.2574 0.166032C14.3396 1.04146 13.2574 4.16603 11.2574 4.66603C9.25743 5.16603 7.78918 2.36668 4.75741 3.66605Z', fill: '#F76F53' },
      ],
      transform: mkT(18, 20, 0.44),
    },
    puckered: {
      paths: [
        { d: 'M9 11.5168L0 21.5168L14.5 24.5168L26 21.5168L35.5 10.0168L21.5 7.51685L9 11.5168Z', fill: '#460D02' },
        { d: 'M30 7.51685L17 13.0168L3 15.0168L30 7.51685Z', fill: '#FBF5ED' },
        { d: 'M8.49996 24.0168L17.5153 19.5168H27.4999L8.49996 24.0168Z', fill: '#FBF5ED' },
        { d: 'M35.5 10.0168C33.7426 19.8506 32.5 28.0168 22.5 31.0168C12.5 34.0168 7 29.0168 0 21.5168C10.7426 16.8506 14.5 18.5168 18 17.5168C21.5 16.5168 23.2426 13.8506 35.5 10.0168Z', fill: '#EF5C47' },
        { d: 'M6.00002 4.01688C2.5 5.51694 1.74243 10.0171 0 21.5169C9.50004 18.5171 10 16.2669 13 15.5169C15 15.0169 15.7954 15.2578 17 15.0169C18.2046 14.776 18.0178 14.0832 20 13.5169C24.6949 12.1755 26.2424 14.3508 35.5 10.0169C28.7424 3.85082 22.5 -1.01714 18.5002 0.182899C15.5823 1.0583 15.0002 4.01677 13.0002 4.51677C11.0002 5.01676 9.03179 2.7175 6.00002 4.01688Z', fill: '#F76F53' },
      ],
      transform: mkT(17.75, 16, 0.40),
    },
    smile: {
      paths: [
        { d: 'M25.5 12L0 17L23 30.5L54.5 21.5L68.5 0L52 5L25.5 12Z', fill: '#460D02' },
        { d: 'M64.5 1.5C65 6 48.5 17.3827 35 21C21.5 24.5 11.5 21 10.5 16C26.1667 11.5 58.9 2.3 64.5 1.5Z', fill: '#FBF5ED' },
        { d: 'M29 29.5L38.0153 25H47.9999L29 29.5Z', fill: '#FBF5ED' },
        { d: 'M68.5 0C64 13.5 60 30.5 40.5 35.5C21 40.5 14 28.5 0 17C11.5 23 26 26.5 36.5 23.5C47 20.5 60 12.5 68.5 0Z', fill: '#EF5C47' },
        { d: 'M22.5 7.5C17.5 9 10 15 0 17C18 18.5 25 14.5 27 14C29 13.5 30.5 14 33 13.5C35.5 13 38.0178 11.0663 40 10.5C43.5 9.5 51.5 9.78783 68.5 0C57 1.78784 42.5 1.49992 37.5 3C34.5822 3.87539 32.5 6.5 30.5 7C28.5 7.5 25.4131 6.62606 22.5 7.5Z', fill: '#F76F53' },
      ],
      transform: mkT(34, 18.5, 0.38),
    },
  };

  const MOUTH_SHAPE_TO_DESIGNER = {
    closed: 'neutral',
    m: 'pressed',
    f: 'open_small',
    soft: 'open_small',
    ah: 'open_mid',
    open: 'open_wide',
    wide: 'open_wide',
    o: 'o_sound',
    narrow: 'puckered',
    smile: 'smile',
  };

  // ── Nivel de audio → forma de boca, con hysteresis — mozoAvatarMimic.ts ──
  const SPEECH_SHAPES = ['closed', 'smile', 'soft', 'ah', 'open'];
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
    const genderGain = 1.04; // avatar femenino (voz 'f' por defecto)

    return {
      update(level) {
        const now = performance.now();
        const adjustedLevel = Math.min(1, level * genderGain);

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

  /**
   * Puerto literal de overlayStyle (CamiaAvatar.tsx): posiciona ojos/cejas por
   * inline style únicamente, SIN la clase compartida .cadipel_avatar_layer — esa
   * clase trae inset:0/width:100%/height:100%/object-fit:cover (pensada para
   * base y boca), y si se aplica también a ojos/cejas gana sobre top/left/width
   * inline (bottom/right/object-fit nunca se sobreescriben), rompiendo el recorte.
   */
  function overlayStyle(node, layout, zIndex) {
    node.style.position = 'absolute';
    node.style.top = `${layout.top * 100}%`;
    node.style.left = `${layout.left * 100}%`;
    node.style.width = `${layout.width * 100}%`;
    node.style.height = 'auto';
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

    const brows = el('img', 'cadipel_avatar_brows');
    brows.src = `${ASSET_BASE}/avatar_brows.svg`;
    brows.alt = '';
    brows.draggable = false;
    overlayStyle(brows, BROWS_LAYOUT, 3);
    head.appendChild(brows);

    const eyesOpen = el('img', 'cadipel_avatar_eyes');
    eyesOpen.src = `${ASSET_BASE}/avatar_eyes_open.svg`;
    eyesOpen.alt = '';
    eyesOpen.draggable = false;
    overlayStyle(eyesOpen, EYES_LAYOUT, 4);
    eyesOpen.style.transition = 'opacity 0.07s';
    head.appendChild(eyesOpen);

    const eyesClosed = el('img', 'cadipel_avatar_eyes');
    eyesClosed.src = `${ASSET_BASE}/avatar_eyes_closed.svg`;
    eyesClosed.alt = '';
    eyesClosed.draggable = false;
    overlayStyle(eyesClosed, EYES_LAYOUT, 4);
    eyesClosed.style.opacity = '0';
    eyesClosed.style.transition = 'opacity 0.07s';
    head.appendChild(eyesClosed);

    const NS = 'http://www.w3.org/2000/svg';
    const mouthSvg = document.createElementNS(NS, 'svg');
    mouthSvg.setAttribute('class', 'cadipel_avatar_layer cadipel_avatar_layer--mouth');
    mouthSvg.setAttribute('viewBox', '0 0 271 271');
    mouthSvg.style.position = 'absolute';
    mouthSvg.style.inset = '0';
    mouthSvg.style.width = '100%';
    mouthSvg.style.height = '100%';
    mouthSvg.style.zIndex = '5';
    const mouthGroups = {};
    for (const key in SVG_MOUTH_SHAPES) {
      const def = SVG_MOUTH_SHAPES[key];
      const g = document.createElementNS(NS, 'g');
      g.setAttribute('transform', def.transform);
      g.style.opacity = key === 'neutral' ? '1' : '0';
      g.style.transition = 'opacity 0.07s ease';
      for (const p of def.paths) {
        const path = document.createElementNS(NS, 'path');
        path.setAttribute('d', p.d);
        path.setAttribute('fill', p.fill);
        g.appendChild(path);
      }
      mouthSvg.appendChild(g);
      mouthGroups[key] = g;
    }
    head.appendChild(mouthSvg);

    container.appendChild(root);

    let activity = 'idle';
    let blinking = false;
    let blinkTimer = null;
    const stabilizer = createMouthShapeStabilizer();
    let activeDesignerKey = 'neutral';

    function setMouthShape(designerKey) {
      if (designerKey === activeDesignerKey) return;
      activeDesignerKey = designerKey;
      for (const key in mouthGroups) {
        mouthGroups[key].style.opacity = key === designerKey ? '1' : '0';
      }
    }

    function applyBlink() {
      eyesOpen.style.opacity = blinking ? '0' : '1';
      eyesClosed.style.opacity = blinking ? '1' : '0';
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
      brows.src = (activity === 'listening' || activity === 'thinking')
        ? `${ASSET_BASE}/avatar_brows_raised.svg`
        : `${ASSET_BASE}/avatar_brows.svg`;
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
