<?php

/**
 * Bloques enriquecidos que el asistente puede insertar en sus respuestas del chat completo
 * (tarjetas con foto, chips, enlaces del panel lateral). Claves = chat/public/js/rich.js — mantener en sintonía.
 */
function cadipel_rich_prompt(): string
{
    return <<<TXT
        Formato enriquecido (el chat lo dibuja con fotos y botones). Usá estos bloques cuando ayuden de
        verdad (por ejemplo al presentar la empresa o varias soluciones o proyectos), no en cada respuesta:
        - Estructura larga: encabezados cortos con "## ", párrafos breves y listas.
        - [[cards: clave | clave | clave]] — tarjetas con foto (2 a 4 por bloque), ponelas justo después del
          texto al que se refieren. Claves válidas: ingenieria_desarrollo, fin_tech, soluciones_agro,
          automatizacion_industrial, soluciones_integrales, soluciones_industria, seguridad_personal,
          sistemas_especiales (las 8 soluciones) y proj_envasadora, proj_led, proj_qrpass (los 3 proyectos
          de ejemplo).
        - [[chips: Energía | Industria | Agroindustria]] — etiquetas cortas (por ejemplo sectores). Solo
          sectores que aparezcan en la información de Cadipel.
        - [[links: clave | clave | clave | clave]] — hasta 4 enlaces para el panel lateral, relacionados
          con la respuesta. Claves válidas: nosotros, soluciones, casos_de_exito, companias_asociadas,
          lo_que_hacemos, contacto y las claves de las tarjetas.
        Cuando pidan una visión general de la empresa, de lo que hacen, de las industrias o de los
        casos de éxito, armá la respuesta con 2 a 3 secciones, cada una con un encabezado "## ", 1 a 2
        frases y su bloque: las soluciones con [[cards: …]] (las 3 o 4 más relevantes), los sectores con
        [[chips: …]] y los proyectos con [[cards: proj_…]]. Siempre incluí además un bloque [[links: …]].
        En preguntas simples o de seguimiento respondé breve, sin encabezados ni bloques.
        Cada bloque va en su propia línea, con el formato exacto. Ponelos antes de la línea final de
        sugerencias. No inventes claves.
        TXT;
}
