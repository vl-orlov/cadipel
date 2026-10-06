<?php

/**
 * Acciones que el asistente puede ejecutar en el sitio (www.cadipel.com.ar) desde el panel del landing:
 * llevar al visitante a una página o sección. El servidor solo envía la clave; el landing la traduce
 * a una URL con su propia lista cerrada (web/js/assistant-island.js, SITE_TARGETS) — mantener ambas en sintonía.
 */

/** @return array<string, array{es: string, en: string, desc: string}> */
function cadipel_site_targets(): array
{
    return [
        'soluciones' => [
            'es' => 'Nuestras soluciones', 'en' => 'Our solutions',
            'desc' => 'sección de la página principal con las 8 unidades de negocio (vista general de todas las soluciones)',
        ],
        'nosotros' => [
            'es' => 'Nosotros', 'en' => 'About us',
            'desc' => 'página sobre la empresa, su historia y su equipo',
        ],
        'companias_asociadas' => [
            'es' => 'Compañías asociadas', 'en' => 'Associated companies',
            'desc' => 'página con las compañías del grupo y aliados (por ejemplo ASSISI SRL)',
        ],
        'lo_que_hacemos' => [
            'es' => 'Lo que hacemos', 'en' => 'What we do',
            'desc' => 'página con los servicios y la forma de trabajo (de la idea al producto, del prototipo a la producción)',
        ],
        'casos_de_exito' => [
            'es' => 'Casos de éxito', 'en' => 'Success cases',
            'desc' => 'página con proyectos y clientes que confiaron en Cadipel',
        ],
        'contacto' => [
            'es' => 'Contacto', 'en' => 'Contact',
            'desc' => 'formulario y datos de contacto al pie del sitio (para hablar con el equipo o pedir presupuesto)',
        ],
        'ingenieria_desarrollo' => [
            'es' => 'Ingeniería y Desarrollo Hard & Soft', 'en' => 'Hardware & Software Engineering',
            'desc' => 'página de la solución de diseño electrónico, firmware y software a medida',
        ],
        'fin_tech' => [
            'es' => 'Fin-Tech', 'en' => 'Fin-Tech',
            'desc' => 'página de billeteras electrónicas y medios de pago',
        ],
        'soluciones_agro' => [
            'es' => 'Soluciones para el Agro', 'en' => 'Agro solutions',
            'desc' => 'página de tecnología para el campo (monitoreo, automatización y control rural)',
        ],
        'automatizacion_industrial' => [
            'es' => 'Automatización industrial', 'en' => 'Industrial automation',
            'desc' => 'página de modernización electrónica de líneas de producción y maquinaria',
        ],
        'soluciones_integrales' => [
            'es' => 'Soluciones para Consorcios', 'en' => 'Building management solutions',
            'desc' => 'página de control de accesos y seguridad para edificios, consorcios y viviendas',
        ],
        'soluciones_industria' => [
            'es' => 'Industria Automotriz', 'en' => 'Automotive industry',
            'desc' => 'página de sistemas electrónicos para vehículos',
        ],
        'seguridad_personal' => [
            'es' => 'Seguridad y Control de Personal', 'en' => 'Personnel security & control',
            'desc' => 'página de control de accesos, presencia y trazabilidad de personas',
        ],
        'sistemas_especiales' => [
            'es' => 'Sistemas especiales UV-C', 'en' => 'UV-C special systems',
            'desc' => 'página de dispositivos de desinfección UV-C',
        ],
    ];
}

/** Definición de la herramienta para Gemini (function calling). */
function cadipel_site_tool_declaration(): array
{
    return [
        'functionDeclarations' => [[
            'name'        => 'navigate_site',
            'description' => 'Lleva al visitante a una página o sección del sitio web de Cadipel.',
            'parameters'  => [
                'type'       => 'OBJECT',
                'properties' => [
                    'target' => [
                        'type'        => 'STRING',
                        'description' => 'Destino al que llevar al visitante.',
                        'enum'        => array_keys(cadipel_site_targets()),
                    ],
                ],
                'required'   => ['target'],
            ],
        ]],
    ];
}

/** Instrucciones extra del system prompt cuando el pedido viene del panel del landing. */
function cadipel_site_actions_prompt(): string
{
    $lines = [];
    foreach (cadipel_site_targets() as $key => $t) {
        $lines[] = "- {$key}: {$t['desc']}";
    }
    $list = implode("\n        ", $lines);

    return <<<TXT
        Contexto: esta conversación ocurre en un panel compacto dentro del sitio web (no en el chat
        completo), así que respondé muy breve: máximo 3 frases cortas.

        Además podés mover a la persona por el sitio con la herramienta navigate_site. Usala solo cuando
        pida ver o ir a algo ("mostrame", "llevame", "quiero ver", "dónde veo…") o cuando enseñarle esa
        página sea claramente lo más útil; no la uses para preguntas generales. Orden obligatorio:
        1) escribí una frase corta diciendo qué le vas a mostrar, 2) la línea [[sugerencias: …]] y
        3) recién ahí llamá la herramienta. Máximo una llamada por respuesta. Destinos disponibles:
        {$list}
        TXT;
}
