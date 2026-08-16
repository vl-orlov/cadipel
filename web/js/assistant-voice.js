/**
 * Grabación de voz: mantener presionado el micrófono, ver niveles en vivo,
 * soltar para transcribir. Puerto de la grabación/niveles de useMozoChat.ts
 * (MediaRecorder + AnalyserNode) y del endpoint de transcripción.
 */
(function () {
  'use strict';

  window.CadipelAssistant = window.CadipelAssistant || {};

  const MIC_BAR_COUNT = 12;
  const MIC_IDLE = 0.12;
  const MIC_NOISE_GATE = 0.014;
  const MIC_LOUD_REF = 0.085;
  const MIN_BLOB_BYTES = 512;
  // Hold gesture allows sending from 450ms after pointerdown, but recording itself
  // only starts ~120ms in (assistant-voice-hold.js MIC_HOLD_ARM_MS) — keep this
  // comfortably below that ~330ms floor so a minimal valid hold isn't dropped.
  const MIN_RECORD_MS = 250;

  let stream = null;
  let recorder = null;
  let chunks = [];
  let recStartedAt = 0;
  let recStartedAtWall = null;
  let analyserCtx = null;
  let analyser = null;
  let timeBuffer = null;
  let micRafId = 0;
  const levelListeners = new Set();

  function idleLevels() {
    return Array(MIC_BAR_COUNT).fill(MIC_IDLE);
  }

  function rmsToEnergy(rms) {
    if (rms <= MIC_NOISE_GATE) return 0;
    const t = (rms - MIC_NOISE_GATE) / (MIC_LOUD_REF - MIC_NOISE_GATE);
    return Math.max(0, Math.min(1, t));
  }

  function readMicLevels() {
    if (!analyser || !timeBuffer) return idleLevels();
    analyser.getByteTimeDomainData(timeBuffer);
    let sum = 0;
    for (let i = 0; i < timeBuffer.length; i++) {
      const v = (timeBuffer[i] - 128) / 128;
      sum += v * v;
    }
    const rms = Math.sqrt(sum / timeBuffer.length);
    const energy = rmsToEnergy(rms);
    const now = performance.now() / 1000;
    return Array.from({ length: MIC_BAR_COUNT }, (_, i) => {
      const wobble = (Math.sin(now * 9 + i * 0.8) + 1) / 2;
      return MIC_IDLE + energy * (0.5 + wobble * 0.5);
    });
  }

  function emitLevels(levels) {
    for (const fn of levelListeners) fn(levels);
  }

  function micTick() {
    emitLevels(readMicLevels());
    micRafId = requestAnimationFrame(micTick);
  }

  function onLevels(cb) {
    levelListeners.add(cb);
    return () => levelListeners.delete(cb);
  }

  async function requestMic() {
    if (stream) return stream;
    stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    return stream;
  }

  function pickMimeType() {
    const candidates = ['audio/webm;codecs=opus', 'audio/webm', 'audio/mp4', 'audio/ogg'];
    for (const c of candidates) {
      if (window.MediaRecorder && MediaRecorder.isTypeSupported && MediaRecorder.isTypeSupported(c)) {
        return c;
      }
    }
    return '';
  }

  async function startRecording() {
    await requestMic();

    const Ctx = window.AudioContext || window.webkitAudioContext;
    analyserCtx = new Ctx();
    const source = analyserCtx.createMediaStreamSource(stream);
    analyser = analyserCtx.createAnalyser();
    analyser.fftSize = 512;
    timeBuffer = new Uint8Array(analyser.fftSize);
    source.connect(analyser);

    if (micRafId) cancelAnimationFrame(micRafId);
    micRafId = requestAnimationFrame(micTick);

    const mimeType = pickMimeType();
    chunks = [];
    recorder = mimeType ? new MediaRecorder(stream, { mimeType }) : new MediaRecorder(stream);
    recorder.ondataavailable = (e) => {
      if (e.data && e.data.size > 0) chunks.push(e.data);
    };
    recStartedAt = performance.now();
    recStartedAtWall = Date.now();
    recorder.start();
  }

  /** Date.now()-based timestamp of the current recording, or null if not recording. */
  function getRecordingStartedAt() {
    return isRecording() ? recStartedAtWall : null;
  }

  function stopAnalyser() {
    if (micRafId) cancelAnimationFrame(micRafId);
    micRafId = 0;
    if (analyserCtx) {
      analyserCtx.close().catch(() => {});
    }
    analyserCtx = null;
    analyser = null;
    timeBuffer = null;
    recStartedAtWall = null;
    emitLevels(idleLevels());
  }

  /** Detiene la grabación y devuelve { blob, mimeType } o null si fue demasiado corta/silenciosa. */
  function stopRecording() {
    return new Promise((resolve) => {
      if (!recorder || recorder.state === 'inactive') {
        stopAnalyser();
        resolve(null);
        return;
      }
      const elapsed = performance.now() - recStartedAt;
      const mimeType = recorder.mimeType || 'audio/webm';
      recorder.onstop = () => {
        stopAnalyser();
        const blob = new Blob(chunks, { type: mimeType });
        chunks = [];
        if (elapsed < MIN_RECORD_MS || blob.size < MIN_BLOB_BYTES) {
          resolve(null);
          return;
        }
        resolve({ blob, mimeType });
      };
      recorder.stop();
    });
  }

  function cancelRecording() {
    if (recorder && recorder.state !== 'inactive') {
      recorder.onstop = null;
      recorder.stop();
    }
    chunks = [];
    stopAnalyser();
  }

  function isRecording() {
    return !!recorder && recorder.state === 'recording';
  }

  function blobToBase64(blob) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onloadend = () => {
        const result = String(reader.result || '');
        resolve(result.split(',')[1] || '');
      };
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    });
  }

  async function transcribe(recording, lang) {
    if (!recording) return '';
    const base64 = await blobToBase64(recording.blob);
    const res = await fetch('/api/ai_transcribe.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ audio: base64, audioMime: recording.mimeType, lang: lang || 'es' }),
    });
    if (!res.ok) return '';
    const data = await res.json();
    return (data && data.transcript) || '';
  }

  window.CadipelAssistant.voice = {
    requestMic,
    startRecording,
    stopRecording,
    cancelRecording,
    isRecording,
    getRecordingStartedAt,
    transcribe,
    onLevels,
    idleLevels,
  };
})();
