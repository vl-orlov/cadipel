/**
 * Conversaciones del chat en localStorage (sin backend). Cada conversación:
 * { id, title, updated, messages: [{ role: 'user'|'assistant', content, suggestions?: string[] }] }
 */
(function () {
  'use strict';

  const KEY = 'cadipel_chat_v2';
  const MAX_CONVS = 30;

  let state = { activeId: null, convs: [] };

  function validConv(c) {
    return c && typeof c.id === 'string' && Array.isArray(c.messages)
      && c.messages.every((m) => m && (m.role === 'user' || m.role === 'assistant') && typeof m.content === 'string');
  }

  function load() {
    try {
      const raw = JSON.parse(localStorage.getItem(KEY) || 'null');
      if (raw && Array.isArray(raw.convs)) {
        state = { activeId: raw.activeId || null, convs: raw.convs.filter(validConv).slice(0, MAX_CONVS) };
      }
    } catch { /* storage bloqueado o corrupto: empezamos de cero */ }
    if (!getActive()) state.activeId = null;
  }

  /** Relee el storage (otra pestaña pudo haber guardado) conservando la conversación activa de esta. */
  function reload() {
    const keep = state.activeId;
    load();
    if (keep && state.convs.some((c) => c.id === keep)) state.activeId = keep;
    else if (!state.activeId && state.convs[0]) state.activeId = state.convs[0].id;
  }

  const pendingDelete = new Set();

  /**
   * Une con lo que haya guardado otra pestaña. Si esta pestaña reescribiera el storage entero
   * al terminar un streaming, borraría chats creados al lado.
   */
  function save() {
    let disk = [];
    try {
      const raw = JSON.parse(localStorage.getItem(KEY) || 'null');
      if (raw && Array.isArray(raw.convs)) disk = raw.convs.filter(validConv);
    } catch { /* seguimos con la memoria */ }

    const byId = new Map();
    for (const c of disk) {
      if (!pendingDelete.has(c.id)) byId.set(c.id, c);
    }
    for (const c of state.convs) {
      const other = byId.get(c.id);
      if (!other || c.updated >= other.updated) byId.set(c.id, c);
    }
    for (const id of pendingDelete) byId.delete(id);

    const convs = [...byId.values()].sort((a, b) => b.updated - a.updated).slice(0, MAX_CONVS);
    try {
      localStorage.setItem(KEY, JSON.stringify({ activeId: state.activeId, convs }));
    } catch {
      return;
    }
    pendingDelete.clear();

    const memIds = new Set(state.convs.map((c) => c.id));
    let adopted = false;
    for (const c of convs) {
      if (!memIds.has(c.id)) {
        state.convs.push(c);
        adopted = true;
      }
    }
    if (adopted) state.convs.sort((a, b) => b.updated - a.updated);
  }

  function uid() {
    return Date.now().toString(36) + Math.random().toString(36).slice(2, 7);
  }

  function getActive() {
    return state.convs.find((c) => c.id === state.activeId) || null;
  }

  /** Conversación vacía nueva (se reutiliza si ya hay una vacía al frente). */
  function create() {
    const empty = state.convs.find((c) => c.messages.length === 0);
    if (empty) {
      state.activeId = empty.id;
      save();
      return empty;
    }
    const conv = { id: uid(), title: '', updated: Date.now(), messages: [] };
    state.convs.unshift(conv);
    state.convs = state.convs.slice(0, MAX_CONVS);
    state.activeId = conv.id;
    save();
    return conv;
  }

  function setActive(id) {
    if (state.convs.some((c) => c.id === id)) {
      state.activeId = id;
      save();
    }
  }

  function remove(id) {
    pendingDelete.add(id);
    state.convs = state.convs.filter((c) => c.id !== id);
    if (state.activeId === id) state.activeId = state.convs[0] ? state.convs[0].id : null;
    save();
  }

  function touch(conv) {
    conv.updated = Date.now();
    if (!conv.title) {
      const first = conv.messages.find((m) => m.role === 'user');
      if (first) conv.title = first.content.replace(/\s+/g, ' ').trim().slice(0, 48);
    }
    state.convs.sort((a, b) => b.updated - a.updated);
    save();
  }

  load();

  window.CadipelStore = {
    all: () => state.convs.slice(),
    active: getActive,
    create,
    setActive,
    remove,
    touch,
    save,
    reload,
  };
})();
