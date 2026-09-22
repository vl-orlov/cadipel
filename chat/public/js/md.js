/**
 * Markdown mínimo y seguro para las respuestas del asistente: **negrita**, *cursiva*, `código`,
 * [texto](https://url), URLs sueltas, listas (- / * / 1.) y párrafos. Todo el texto se escapa
 * antes de aplicar el formato, y solo se aceptan enlaces http(s).
 */
(function () {
  'use strict';

  function esc(s) {
    return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function inline(text) {
    let s = esc(text).replace(/\u0000/g, '');
    const stash = [];
    const keep = (html) => { stash.push(html); return `\u0000${stash.length - 1}\u0000`; };

    s = s.replace(/`([^`\n]+)`/g, (_, c) => keep(`<code>${c}</code>`));
    s = s.replace(/\[([^\]\n]+)\]\((https?:\/\/[^\s)]+)\)/g, (_, label, url) =>
      keep(`<a href="${url}" target="_blank" rel="noopener noreferrer">${label}</a>`));
    s = s.replace(/(^|[\s(*])(https?:\/\/[^\s<)*]+[^\s<)*.,;:!?])/g, (_, pre, url) =>
      pre + keep(`<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`));
    s = s.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
    s = s.replace(/(^|[^*\w])\*([^*\n]+)\*(?!\w)/g, '$1<em>$2</em>');

    return s.replace(/\u0000(\d+)\u0000/g, (_, i) => stash[Number(i)]);
  }

  function render(text) {
    const lines = String(text || '').replace(/\r/g, '').split('\n');
    const out = [];
    let list = null;   // 'ul' | 'ol'
    let para = [];

    const flushPara = () => {
      if (para.length) out.push(`<p>${para.map(inline).join('<br>')}</p>`);
      para = [];
    };
    const closeList = () => {
      if (list) out.push(`</${list}>`);
      list = null;
    };

    for (const line of lines) {
      const ul = /^\s*[-*•]\s+(.*)$/.exec(line);
      const ol = /^\s*\d+[.)]\s+(.*)$/.exec(line);
      if (ul || ol) {
        flushPara();
        const kind = ul ? 'ul' : 'ol';
        if (list !== kind) { closeList(); out.push(`<${kind}>`); list = kind; }
        out.push(`<li>${inline((ul || ol)[1])}</li>`);
      } else if (line.trim() === '') {
        flushPara();
        closeList();
      } else {
        closeList();
        para.push(line);
      }
    }
    flushPara();
    closeList();
    return out.join('');
  }

  window.CadipelMd = { render, esc };
})();
