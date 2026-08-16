/**
 * Gesto de "mantener presionado para hablar" — puerto literal de useCamiaMicHold
 * (CamiaVoiceHold.tsx, mi-oshka/CamIA): mismas constantes de umbral, misma máquina
 * de estados (arm → hold → lock/cancel → finalize), mismo haptic feedback.
 *
 * Modo 'dictation' (mic de texto): soporta swipe-izquierda para cancelar y
 * swipe-arriba para bloquear (manos libres). Modo 'conversation' (orbe de voz):
 * solo mantener-para-hablar, sin lock/cancel — igual que en CamiaVoiceHold.tsx,
 * donde applyMicGesture/finalizeMicHold cortan la rama de gestos si el modo es
 * 'conversation'.
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const MIC_CANCEL_SLIDE_PX = 72;
  const MIC_LOCK_SLIDE_PX = 48;
  const MIC_LOCK_RELEASE_PX = 34;
  const MIC_LOCK_MIN_HOLD_MS = 120;
  const MIC_LOCK_PROGRESS_RELEASE = 0.4;
  const MIC_HOLD_ARM_MS = 120;
  const MIC_MIN_HOLD_TO_SEND_MS = 450;
  const MIC_GESTURE_SLOP_PX = 14;
  const MIC_RELEASE_NEAR_PX = 56;
  const CANCEL_REPLAY_MS = 1650;

  function formatRecordTime(startedAt) {
    if (!startedAt) return '0:00,00';
    const elapsed = Date.now() - startedAt;
    const sec = Math.floor(elapsed / 1000);
    const cs = Math.floor((elapsed % 1000) / 10);
    const m = Math.floor(sec / 60);
    const s = sec % 60;
    return `${m}:${s.toString().padStart(2, '0')},${cs.toString().padStart(2, '0')}`;
  }

  function createWaveBars(container, count) {
    count = count || 12;
    container.innerHTML = '';
    container.classList.add('cadipel_wave_bars', 'cadipel_wave_bars--live');
    container.setAttribute('aria-hidden', 'true');
    const bars = [];
    for (let i = 0; i < count; i++) {
      const span = document.createElement('span');
      span.style.height = '3px';
      container.appendChild(span);
      bars.push(span);
    }
    return {
      update(levels) {
        for (let i = 0; i < count; i++) {
          const level = levels && levels[i] != null ? levels[i] : 0.12;
          bars[i].style.height = Math.round(3 + level * 24) + 'px';
        }
      },
    };
  }

  function micEnergy(levels) {
    if (!levels || !levels.length) return 0;
    let peak = 0;
    let sum = 0;
    for (const l of levels) {
      if (l > peak) peak = l;
      sum += l;
    }
    const avg = sum / levels.length;
    return Math.min(1, peak * 0.75 + avg * 0.25);
  }

  /**
   * @param {{
   *   isRecording: () => boolean,
   *   isSending: () => boolean,
   *   isTtsPlaying: () => boolean,
   *   getRecordingStartedAt: () => number|null,
   *   startRecording: (mode: string) => Promise<void>|void,
   *   finishRecording: () => void,
   *   cancelRecording: () => void,
   *   stopSpeaking: () => void,
   *   voiceBarEl: HTMLElement|null,
   *   textBarEl: HTMLElement|null,
   * }} cfg
   */
  function create(cfg) {
    const listeners = new Set();
    function onChange(cb) {
      listeners.add(cb);
      return () => listeners.delete(cb);
    }

    const state = {
      holding: false,
      voiceMicHolding: false,
      micLocked: false,
      micCancelArmed: false,
      cancelProgress: 0,
      lockProgress: 0,
      cancelReplay: false,
      recordTimeLocked: '0:00,00',
    };

    let micHoldMode = 'dictation';

    function snapshot() {
      const voiceOrbLive = state.voiceMicHolding || (cfg.isRecording() && micHoldMode === 'conversation');
      const startedAt = cfg.getRecordingStartedAt();
      const lockedSendReady = Boolean(startedAt && Date.now() - startedAt >= MIC_MIN_HOLD_TO_SEND_MS);
      return Object.assign({}, state, { voiceOrbLive, lockedSendReady, mode: micHoldMode });
    }

    function notify() {
      const snap = snapshot();
      for (const fn of listeners) fn(snap);
    }

    let micHoldActive = false;
    let micHoldStartX = 0;
    let micHoldStartY = 0;
    let micHoldStartedAt = 0;
    let micLockedFlag = false;
    let micCancelArmedFlag = false;
    let cancelProgressVal = 0;
    let lockProgressVal = 0;
    let cancelVibrated = false;
    let micArmTimer = null;
    let cancelReplayTimer = null;
    let pointerId = null;
    let captureEl = null;
    let capturePointerId = null;
    let onMoveHandler = null;
    let onUpHandler = null;
    let timerInterval = null;

    function startTimer() {
      stopTimerInternal(false);
      const tick = () => {
        const startedAt = cfg.getRecordingStartedAt() || micHoldStartedAt;
        state.recordTimeLocked = formatRecordTime(startedAt);
        notify();
      };
      tick();
      timerInterval = window.setInterval(tick, 50);
    }
    function stopTimerInternal(reset) {
      if (timerInterval) {
        window.clearInterval(timerInterval);
        timerInterval = null;
      }
      if (reset) state.recordTimeLocked = '0:00,00';
    }
    function stopTimer() {
      stopTimerInternal(true);
    }

    function clearMicArmTimer() {
      if (micArmTimer) {
        clearTimeout(micArmTimer);
        micArmTimer = null;
      }
    }
    function clearCancelReplayTimer() {
      if (cancelReplayTimer) {
        clearTimeout(cancelReplayTimer);
        cancelReplayTimer = null;
      }
    }
    function startCancelReplay() {
      clearCancelReplayTimer();
      state.cancelReplay = true;
      cancelReplayTimer = setTimeout(() => {
        state.cancelReplay = false;
        cancelReplayTimer = null;
        notify();
      }, CANCEL_REPLAY_MS);
    }

    function releasePointerCapture() {
      if (capturePointerId != null && captureEl && captureEl.hasPointerCapture && captureEl.hasPointerCapture(capturePointerId)) {
        try { captureEl.releasePointerCapture(capturePointerId); } catch { /* ignore */ }
      }
      capturePointerId = null;
      captureEl = null;
    }

    function clearWindowListeners() {
      clearMicArmTimer();
      releasePointerCapture();
      state.voiceMicHolding = false;
      if (onMoveHandler) window.removeEventListener('pointermove', onMoveHandler);
      if (onUpHandler) {
        window.removeEventListener('pointerup', onUpHandler);
        window.removeEventListener('pointercancel', onUpHandler);
      }
      onMoveHandler = null;
      onUpHandler = null;
      pointerId = null;
    }

    function shouldLockGesture(slideUp, slideLeft, onRelease) {
      const threshold = onRelease ? MIC_LOCK_RELEASE_PX : MIC_LOCK_SLIDE_PX;
      const heldMs = Date.now() - micHoldStartedAt;
      const verticalIntent = slideUp > slideLeft - 10;
      if (!verticalIntent || slideUp < threshold) return false;
      if (!onRelease && heldMs < MIC_LOCK_MIN_HOLD_MS) return false;
      return true;
    }

    function applyMicGesture(clientX, clientY) {
      if (micHoldMode === 'conversation') return;
      if (!micHoldActive || micLockedFlag) return;

      const slideLeft = micHoldStartX - clientX;
      const slideUp = micHoldStartY - clientY;

      if (slideLeft > slideUp && slideLeft > MIC_GESTURE_SLOP_PX) {
        const cp = Math.min(1, slideLeft / MIC_CANCEL_SLIDE_PX);
        cancelProgressVal = cp;
        state.cancelProgress = cp;
        lockProgressVal = 0;
        state.lockProgress = 0;

        const armed = cp >= 0.96;
        if (armed !== micCancelArmedFlag) {
          micCancelArmedFlag = armed;
          state.micCancelArmed = armed;
          if (armed && !cancelVibrated) {
            cancelVibrated = true;
            try { window.CadipelAssistant.micFeedback.micFeedbackCancelArmed(); } catch { /* ignore */ }
          }
        }
        notify();
        return;
      }

      if (cancelProgressVal > 0) {
        cancelProgressVal = 0;
        state.cancelProgress = 0;
        micCancelArmedFlag = false;
        state.micCancelArmed = false;
        cancelVibrated = false;
      }

      const progress = Math.min(1, Math.max(0, slideUp / MIC_LOCK_SLIDE_PX));
      lockProgressVal = progress;
      state.lockProgress = progress;

      if (shouldLockGesture(slideUp, slideLeft, false)) {
        micLockedFlag = true;
        state.micLocked = true;
        lockProgressVal = 1;
        state.lockProgress = 1;
        try { window.CadipelAssistant.micFeedback.hapticMic(12); } catch { /* ignore */ }
      }
      notify();
    }

    function finalizeMicHold(clientX, clientY) {
      if (!micHoldActive) return;

      const heldMs = Date.now() - micHoldStartedAt;
      const recording = cfg.isRecording();
      const isConversation = micHoldMode === 'conversation';

      micHoldActive = false;
      state.holding = false;
      clearWindowListeners();

      micCancelArmedFlag = false;
      state.micCancelArmed = false;
      lockProgressVal = 0;
      state.lockProgress = 0;
      cancelProgressVal = 0;
      state.cancelProgress = 0;
      cancelVibrated = false;

      if (isConversation) {
        micLockedFlag = false;
        state.micLocked = false;

        if (!recording || heldMs < MIC_HOLD_ARM_MS || heldMs < MIC_MIN_HOLD_TO_SEND_MS) {
          stopTimer();
          notify();
          cfg.cancelRecording();
          return;
        }
        stopTimer();
        notify();
        cfg.finishRecording();
        return;
      }

      const slideLeft = micHoldStartX - clientX;
      const slideUp = micHoldStartY - clientY;
      const releaseDist = Math.hypot(clientX - micHoldStartX, clientY - micHoldStartY);
      const fingerNearStart = releaseDist <= MIC_RELEASE_NEAR_PX;

      let locked = micLockedFlag;
      if (!locked && cancelProgressVal < 0.5 && (
        lockProgressVal >= MIC_LOCK_PROGRESS_RELEASE || shouldLockGesture(slideUp, slideLeft, true)
      )) {
        micLockedFlag = true;
        locked = true;
      }
      state.micLocked = locked;

      let cancel = !locked && (
        micCancelArmedFlag || cancelProgressVal >= 0.88 || (slideLeft >= MIC_CANCEL_SLIDE_PX && slideLeft > slideUp)
      );
      if (!locked && fingerNearStart && cancelProgressVal < 0.5) cancel = false;

      if (locked) {
        notify();
        if (!recording) void cfg.startRecording(micHoldMode);
        return;
      }

      if (!recording || heldMs < MIC_HOLD_ARM_MS) {
        stopTimer();
        notify();
        cfg.cancelRecording();
        return;
      }

      if (cancel) {
        stopTimer();
        cfg.cancelRecording();
        startCancelReplay();
        notify();
        return;
      }

      if (heldMs < MIC_MIN_HOLD_TO_SEND_MS) {
        stopTimer();
        notify();
        cfg.cancelRecording();
        return;
      }

      stopTimer();
      notify();
      cfg.finishRecording();
    }

    function handlePointerDown(e, mode) {
      mode = mode || 'dictation';
      if (!cfg.isRecording()) {
        if (cfg.isSending() && !(mode === 'conversation' && cfg.isTtsPlaying())) return;
      }
      if (cfg.isTtsPlaying() && !cfg.isRecording()) {
        if (mode === 'conversation') cfg.stopSpeaking();
        else return;
      }

      e.preventDefault();
      try { window.CadipelAssistant.micFeedback.micFeedbackPress(); } catch { /* ignore */ }

      const barEl = mode === 'conversation' ? cfg.voiceBarEl : cfg.textBarEl;
      const target = e.currentTarget;

      clearWindowListeners();
      if (mode === 'conversation') state.voiceMicHolding = true;
      state.holding = true;
      micHoldStartedAt = Date.now();
      micHoldActive = true;
      micHoldStartX = e.clientX;
      micHoldStartY = e.clientY;
      micHoldMode = mode;

      if (barEl && barEl.setPointerCapture) {
        try {
          barEl.setPointerCapture(e.pointerId);
          capturePointerId = e.pointerId;
          captureEl = barEl;
        } catch { /* ignore */ }
      }
      if (target && target.setPointerCapture) {
        try { target.setPointerCapture(e.pointerId); } catch { /* ignore */ }
      }

      micCancelArmedFlag = false;
      micLockedFlag = false;
      state.micCancelArmed = false;
      state.micLocked = false;
      state.lockProgress = 0;
      state.cancelProgress = 0;
      lockProgressVal = 0;
      cancelProgressVal = 0;
      cancelVibrated = false;

      pointerId = e.pointerId;

      onMoveHandler = (ev) => {
        if (ev.pointerId !== pointerId) return;
        ev.preventDefault();
        applyMicGesture(ev.clientX, ev.clientY);
      };
      onUpHandler = (ev) => {
        if (ev.pointerId !== pointerId) return;
        ev.preventDefault();
        finalizeMicHold(ev.clientX, ev.clientY);
      };
      window.addEventListener('pointermove', onMoveHandler, { passive: false });
      window.addEventListener('pointerup', onUpHandler, { passive: false });
      window.addEventListener('pointercancel', onUpHandler, { passive: false });

      startTimer();
      notify();

      clearMicArmTimer();
      micArmTimer = setTimeout(() => {
        micArmTimer = null;
        if (!micHoldActive) return;
        void cfg.startRecording(micHoldMode);
      }, MIC_HOLD_ARM_MS);
    }

    function handleLockedSend() {
      const startedAt = cfg.getRecordingStartedAt();
      if (!startedAt) return;
      if (Date.now() - startedAt < MIC_MIN_HOLD_TO_SEND_MS) return;
      micLockedFlag = false;
      state.micLocked = false;
      stopTimer();
      notify();
      cfg.finishRecording();
    }

    function handleLockedCancel() {
      micLockedFlag = false;
      state.micLocked = false;
      stopTimer();
      notify();
      cfg.cancelRecording();
    }

    function destroy() {
      clearWindowListeners();
      clearCancelReplayTimer();
      stopTimer();
      listeners.clear();
    }

    return {
      onChange,
      handlePointerDown,
      handleLockedSend,
      handleLockedCancel,
      destroy,
      getSnapshot: snapshot,
    };
  }

  window.CadipelAssistant.voiceHold = { create, formatRecordTime, createWaveBars, micEnergy };
})();
