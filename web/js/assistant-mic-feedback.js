/**
 * Haptics para el gesto de mantener presionado el micrófono — Vibration API (Android)
 * + truco del "switch" de iOS Safari. Puerto literal de micFeedback.ts (mi-oshka/CamIA).
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  function prefersReducedFeedback() {
    try {
      return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    } catch {
      return false;
    }
  }

  function canVibrate() {
    return typeof navigator.vibrate === 'function';
  }

  function isIosWebKit() {
    if (typeof navigator === 'undefined') return false;
    const ua = navigator.userAgent;
    return /iPhone|iPad|iPod/i.test(ua)
      || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
  }

  let iosHapticSwitch = null;
  let iosHapticLabel = null;

  function ensureIosHapticNode() {
    if (typeof document === 'undefined') return null;
    if (iosHapticSwitch) return iosHapticSwitch;

    const label = document.createElement('label');
    label.setAttribute('aria-hidden', 'true');
    Object.assign(label.style, {
      position: 'fixed',
      bottom: '0',
      left: '0',
      width: '1px',
      height: '1px',
      opacity: '0',
      overflow: 'hidden',
      pointerEvents: 'none',
      zIndex: '-1',
    });

    const input = document.createElement('input');
    input.type = 'checkbox';
    input.setAttribute('switch', '');
    input.tabIndex = -1;
    input.addEventListener('change', () => {
      input.checked = false;
    });

    label.appendChild(input);
    document.body.appendChild(label);
    iosHapticSwitch = input;
    iosHapticLabel = label;
    return input;
  }

  function iosHapticTick() {
    if (!isIosWebKit()) return false;
    const input = ensureIosHapticNode();
    if (!input || !iosHapticLabel) return false;
    try {
      input.checked = !input.checked;
      iosHapticLabel.click();
      return true;
    } catch {
      return false;
    }
  }

  function hapticMic(pattern) {
    if (prefersReducedFeedback()) return;

    if (canVibrate()) {
      try {
        navigator.vibrate(pattern);
        return;
      } catch { /* fall through to iOS */ }
    }

    if (!isIosWebKit()) return;

    iosHapticTick();

    if (!Array.isArray(pattern) || pattern.length < 2) return;

    let delay = 0;
    for (let i = 1; i < pattern.length; i += 2) {
      delay += pattern[i] ?? 0;
      window.setTimeout(() => iosHapticTick(), delay);
    }
  }

  function micFeedbackPress() {
    hapticMic(10);
  }

  function micFeedbackRecordStart() {
    hapticMic(14);
  }

  function micFeedbackRecordCancel() {
    hapticMic([14, 48, 16]);
  }

  function micFeedbackCancelArmed() {
    hapticMic(8);
  }

  window.CadipelAssistant.micFeedback = {
    hapticMic,
    micFeedbackPress,
    micFeedbackRecordStart,
    micFeedbackRecordCancel,
    micFeedbackCancelArmed,
  };
})();
