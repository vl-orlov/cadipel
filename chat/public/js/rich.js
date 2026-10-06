/**
 * Bloques enriquecidos de las respuestas del asistente (chat completo). El modelo marca bloques con
 * [[cards: clave | clave]], [[chips: texto | texto]], [[links: clave | clave]] y [[sugerencias: …]];
 * acá se separan del texto, y el chat los dibuja (tarjetas con foto, chips, panel lateral).
 * Las claves son una lista cerrada — mantener en sintonía con chat/src/rich_blocks.php.
 */
(function () {
  'use strict';

  const SITE = 'https://www.cadipel.com.ar/';
  const page = (p) => SITE + '?page=' + p;

  /** Soluciones y proyectos: { es, en, sub, image, url } */
  const CARDS = {
    ingenieria_desarrollo: { es: 'Ingeniería y desarrollo Hard & Soft', en: 'Hardware & Software Engineering', image: 'ingenieria_desarrollo', url: page('ingenieria_desarrollo') },
    fin_tech: { es: 'Fin-Tech: billeteras electrónicas', en: 'Fin-Tech: e-wallets', image: 'fin_tech', url: page('fin_tech') },
    soluciones_agro: { es: 'Soluciones para el agro', en: 'Agro solutions', image: 'soluciones_agro', url: page('soluciones_agro') },
    automatizacion_industrial: { es: 'Automatización industrial', en: 'Industrial automation', image: 'automatizacion_industrial', url: page('automatizacion_industrial') },
    soluciones_integrales: { es: 'Soluciones para consorcios', en: 'Building management solutions', image: 'soluciones_integrales', url: page('soluciones_integrales') },
    soluciones_industria: { es: 'Industria automotriz', en: 'Automotive industry', image: 'soluciones_industria', url: page('soluciones_industria') },
    seguridad_personal: { es: 'Seguridad y control de personal', en: 'Personnel security & control', image: 'seguridad_personal', url: page('seguridad_personal') },
    sistemas_especiales: { es: 'Sistemas especiales UV-C', en: 'UV-C special systems', image: 'sistemas_especiales', url: page('sistemas_especiales') },
    proj_envasadora: { es: 'Envasadora y blisteadora', en: 'Packaging & blister machine', sub: { es: 'Industria / Automatización', en: 'Industry / Automation' }, image: 'proj_envasadora', url: page('casos_de_exito') },
    proj_led: { es: 'LED A-GIRO', en: 'LED A-GIRO', sub: { es: 'Electrónica / Producto', en: 'Electronics / Product' }, image: 'proj_led', url: page('casos_de_exito') },
    proj_qrpass: { es: 'QR-Pass dinámico', en: 'Dynamic QR-Pass', sub: { es: 'Seguridad / Accesos', en: 'Security / Access' }, image: 'proj_qrpass', url: page('casos_de_exito') },
  };

  /** Enlaces al sitio para el panel "Enlaces relacionados": { es, en, url, icon } */
  const LINKS = {
    nosotros: { es: 'Sobre nosotros', en: 'About us', url: page('nosotros'), icon: 'doc' },
    soluciones: { es: 'Nuestras soluciones', en: 'Our solutions', url: SITE + '#soluciones', icon: 'cube' },
    casos_de_exito: { es: 'Casos de éxito', en: 'Success cases', url: page('casos_de_exito'), icon: 'chart' },
    companias_asociadas: { es: 'Compañías asociadas', en: 'Associated companies', url: page('companias_asociadas'), icon: 'building' },
    lo_que_hacemos: { es: 'Lo que hacemos', en: 'What we do', url: page('lo_que_hacemos'), icon: 'cube' },
    contacto: { es: 'Contacto', en: 'Contact', url: SITE + '#contacto', icon: 'chat' },
  };
  Object.keys(CARDS).forEach((k) => {
    if (!LINKS[k]) LINKS[k] = { es: CARDS[k].es, en: CARDS[k].en, url: CARDS[k].url, icon: k.indexOf('proj_') === 0 ? 'chart' : 'cube' };
  });
  const DEFAULT_LINKS = ['nosotros', 'soluciones', 'casos_de_exito', 'companias_asociadas'];

  const BLOCK_RE = /\[\[\s*(cards|chips|links|sugerencias?|suggestions?)\s*:\s*([^\]]*?)\s*\]\]/gi;
  const split = (s) => s.split('|').map((x) => x.trim()).filter(Boolean);

  /**
   * Texto crudo de la respuesta → { segments, suggestions, links }.
   * segments: [{ type: 'md', text } | { type: 'cards', keys } | { type: 'chips', items }] en el orden de aparición.
   * Un bloque a medio escribir (streaming) se corta y no se muestra.
   */
  function parse(raw) {
    let src = String(raw || '');
    const open = src.lastIndexOf('[[');
    if (open !== -1 && src.indexOf(']]', open) === -1) src = src.slice(0, open);
    else if (src.endsWith('[')) src = src.slice(0, -1);

    const segments = [];
    let suggestions = [];
    let links = [];
    let last = 0;
    let m;
    BLOCK_RE.lastIndex = 0;
    while ((m = BLOCK_RE.exec(src))) {
      const before = src.slice(last, m.index);
      if (before.trim()) segments.push({ type: 'md', text: before.trim() });
      last = m.index + m[0].length;
      const kind = m[1].toLowerCase();
      const items = split(m[2]);
      if (kind === 'cards') {
        const keys = items.filter((k) => CARDS[k]);
        if (keys.length) segments.push({ type: 'cards', keys: keys.slice(0, 4) });
      } else if (kind === 'chips') {
        if (items.length) segments.push({ type: 'chips', items: items.slice(0, 8) });
      } else if (kind === 'links') {
        links = items.filter((k) => LINKS[k]).slice(0, 4);
      } else {
        suggestions = items.slice(0, 4);
      }
    }
    const tail = src.slice(last);
    if (tail.trim()) segments.push({ type: 'md', text: tail.trim() });

    const text = segments.filter((s) => s.type === 'md').map((s) => s.text).join('\n\n');
    return { segments, suggestions, links, text };
  }

  window.CadipelRich = { parse, CARDS, LINKS, DEFAULT_LINKS };
})();
