/**
 * Lip-sync por amplitud real de audio (AnalyserNode), sin SDK de fonemas/visemas.
 * Puerto del mecanismo de mozoLipSync.ts (mi-oshka/CamIA) — solo la rama de audio real,
 * sin el fallback simulado por texto (acá siempre reproducimos audio real).
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const listeners = new Set();
  let sharedCtx = null;
  let rafId = 0;
  let activeAnalyser = null;
  let timeBuffer = null;
  let freqBuffer = null;
  let smoothedLevel = 0;
  let smoothedSpectrum = { bassRatio: 0.33, trebleRatio: 0.33 };
  let levelGain = 1;
  let calibrating = false;
  let calSamples = [];
  let engineActive = false;

  const CALIBRATION_SAMPLES = 12;
  const PLAYBACK_GAIN = 1.6;

  function getSharedAudioContext() {
    if (!sharedCtx) {
      const Ctx = window.AudioContext || window.webkitAudioContext;
      sharedCtx = new Ctx();
    }
    if (sharedCtx.state === 'suspended') {
      sharedCtx.resume().catch(() => {});
    }
    return sharedCtx;
  }

  function readRawRmsUnscaled() {
    if (!activeAnalyser || !timeBuffer) return 0;
    activeAnalyser.getByteTimeDomainData(timeBuffer);
    let sum = 0;
    for (let i = 0; i < timeBuffer.length; i++) {
      const v = (timeBuffer[i] - 128) / 128;
      sum += v * v;
    }
    const rms = Math.sqrt(sum / timeBuffer.length);
    return Math.min(1, Math.pow(rms * 3.2, 0.88));
  }

  function readRawRms() {
    return Math.min(1, readRawRmsUnscaled() * levelGain);
  }

  function bandAvg(buffer, from, to) {
    let sum = 0;
    for (let i = from; i < to; i++) sum += buffer[i] || 0;
    return sum / Math.max(1, to - from) / 255;
  }

  function readSpectrum() {
    if (!activeAnalyser || !freqBuffer) return smoothedSpectrum;
    activeAnalyser.getByteFrequencyData(freqBuffer);
    const bass = bandAvg(freqBuffer, 2, 8);
    const mid = bandAvg(freqBuffer, 8, 18);
    const treble = bandAvg(freqBuffer, 18, 32);
    const sum = bass + mid + treble || 1;
    return { bassRatio: bass / sum, trebleRatio: treble / sum };
  }

  function smoothLevel(target) {
    const rising = target > smoothedLevel;
    const k = rising ? 0.55 : 0.22;
    return smoothedLevel + (target - smoothedLevel) * k;
  }

  function calibrate(unscaledRaw) {
    if (!calibrating) return;
    calSamples.push(unscaledRaw);
    if (calSamples.length < CALIBRATION_SAMPLES) return;
    const sorted = calSamples.slice().sort((a, b) => a - b);
    const median = sorted[Math.floor(sorted.length / 2)] || 0.15;
    if (median > 0.02) {
      levelGain = Math.min(1.5, Math.max(0.65, 0.38 / median));
    }
    calibrating = false;
  }

  function emit(state) {
    smoothedLevel = state.level;
    smoothedSpectrum = state.spectrum;
    for (const fn of listeners) fn(state);
  }

  function tick() {
    if (activeAnalyser) calibrate(readRawRmsUnscaled());
    const rawLevel = activeAnalyser ? readRawRms() : 0;
    const level = smoothLevel(rawLevel);
    const rawSpectrum = readSpectrum();
    const spectrum = {
      bassRatio: smoothedSpectrum.bassRatio + (rawSpectrum.bassRatio - smoothedSpectrum.bassRatio) * 0.14,
      trebleRatio: smoothedSpectrum.trebleRatio + (rawSpectrum.trebleRatio - smoothedSpectrum.trebleRatio) * 0.14,
    };
    emit({ level, spectrum });
    rafId = requestAnimationFrame(tick);
  }

  function stopLoop() {
    engineActive = false;
    if (rafId) {
      cancelAnimationFrame(rafId);
      rafId = 0;
    }
    activeAnalyser = null;
    timeBuffer = null;
    freqBuffer = null;
    smoothedLevel = 0;
    levelGain = 1;
    calibrating = false;
    calSamples = [];
    emit({ level: 0, spectrum: { bassRatio: 0.33, trebleRatio: 0.33 } });
  }

  function subscribeMouthLevel(listener) {
    listeners.add(listener);
    listener({ level: smoothedLevel, spectrum: smoothedSpectrum });
    return () => listeners.delete(listener);
  }

  /**
   * Conecta un MediaElementAudioSourceNode a un analyser propio (compresor + gain
   * antes de destination). Puerto de connectThroughAnalyser (camiaLipSync.ts).
   */
  function connectThroughAnalyser(source, ctx) {
    const analyser = ctx.createAnalyser();
    analyser.fftSize = 512;
    analyser.smoothingTimeConstant = 0.28;

    const compressor = ctx.createDynamicsCompressor();
    compressor.threshold.value = -24;
    compressor.knee.value = 10;
    compressor.ratio.value = 8;
    compressor.attack.value = 0.002;
    compressor.release.value = 0.15;

    const gain = ctx.createGain();
    gain.gain.value = PLAYBACK_GAIN;

    source.connect(analyser);
    analyser.connect(compressor);
    compressor.connect(gain);
    gain.connect(ctx.destination);
    return analyser;
  }

  /**
   * Engancha el analizador a un <audio> nuevo — cada chunk de TTS crea su propio
   * elemento (assistant-tts.js playPayload: new Audio() por chunk), así que acá
   * SIEMPRE creamos un MediaElementAudioSourceNode fresco (createMediaElementSource
   * solo puede llamarse una vez por elemento). Puerto de tapMozoHtmlAudioElement
   * (camiaLipSync.ts) — no de la variante con caché (esa es para un <audio> único
   * reutilizado entre chunks, que acá no existe).
   */
  function tapPlaybackElement(audioEl) {
    try {
      const ctx = getSharedAudioContext();
      engineActive = true;
      calibrating = true;
      calSamples = [];
      levelGain = 1;
      smoothedLevel = 0;

      const source = ctx.createMediaElementSource(audioEl);
      const analyser = connectThroughAnalyser(source, ctx);

      activeAnalyser = analyser;
      timeBuffer = new Uint8Array(activeAnalyser.fftSize);
      freqBuffer = new Uint8Array(activeAnalyser.frequencyBinCount);

      if (rafId) cancelAnimationFrame(rafId);
      rafId = requestAnimationFrame(tick);
    } catch {
      // AudioContext no disponible, o el elemento ya tiene un source (no debería
      // pasar: cada chunk es un <audio> nuevo) — el avatar cae a estado "idle"
    }
  }

  function stop() {
    stopLoop();
  }

  window.CadipelAssistant.lipsync = {
    getSharedAudioContext,
    tapPlaybackElement,
    subscribeMouthLevel,
    stop,
    isActive: () => engineActive,
  };
})();
