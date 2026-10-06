<?php /* Asistente IA: "isla" pegada a la cabecera. Se incluye desde site_header.php y landing_header.php; lógica en js/assistant-island.js */ ?>
    <div class="cadipel_island_clip" aria-hidden="false">
    <div class="cadipel_island" id="cadipel_island">
        <div class="cadipel_island_body">
            <button type="button" class="cadipel_island_mini cadipel_island_mini_search" id="cadipel_island_mini_search" data-i18n-aria-label="assistant_bar_placeholder" aria-label="¿En qué puedo ayudarte?">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            </button>
            <button type="button" class="cadipel_island_avatar_btn" id="cadipel_island_toggle" aria-expanded="false" aria-controls="cadipel_island_form" data-i18n-aria-label="assistant_bar_open" aria-label="Asistente IA de Cadipel">
                <span class="cadipel_island_avatar" id="cadipel_bar_avatar"></span>
                <span class="cadipel_island_status" aria-hidden="true"></span>
            </button>
            <form class="cadipel_island_form" id="cadipel_bar_form" autocomplete="off">
                <svg class="cadipel_island_search_icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                <input type="text" name="q" maxlength="500" tabindex="-1" placeholder="¿En qué puedo ayudarte?" data-i18n-placeholder="assistant_bar_placeholder" data-i18n-aria-label="assistant_bar_placeholder" aria-label="¿En qué puedo ayudarte?">
            </form>
            <div class="cadipel_island_actions">
                <a class="cadipel_island_action" id="cadipel_island_mic" href="https://cadipel.pribridge.pro/?lang=es" target="_blank" rel="noopener" data-chat-link tabindex="-1" data-i18n-aria-label="assistant_bar_mic" aria-label="Hablar con el asistente">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>
                </a>
            </div>
            <a class="cadipel_island_mini cadipel_island_mini_mic" id="cadipel_island_mini_mic" href="https://cadipel.pribridge.pro/?lang=es" target="_blank" rel="noopener" data-chat-link data-i18n-aria-label="assistant_bar_mic" aria-label="Hablar con el asistente">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>
            </a>
        </div>
        <div class="cadipel_island_panel" id="cadipel_island_panel">
            <div class="cadipel_panel_clip">
                <div class="cadipel_panel_card">
                    <div class="cadipel_panel_head">
                        <span class="cadipel_panel_title" data-i18n="assistant_panel_title">Asistente CADIPEL</span>
                        <span class="cadipel_panel_head_actions">
                            <button type="button" class="cadipel_panel_link" id="cadipel_panel_full">
                                <span data-i18n="assistant_open_full">Abrir chat completo</span>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17L17 7M9 7h8v8"/></svg>
                            </button>
                            <button type="button" class="cadipel_panel_icon" id="cadipel_panel_new" tabindex="-1" data-i18n-aria-label="assistant_new" aria-label="Nueva consulta">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                            </button>
                            <button type="button" class="cadipel_panel_icon" id="cadipel_panel_close" tabindex="-1" data-i18n-aria-label="assistant_close" aria-label="Cerrar">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </button>
                        </span>
                    </div>
                    <div class="cadipel_panel_messages" id="cadipel_panel_messages" role="log" aria-live="polite"></div>
                    <div class="cadipel_panel_chips" id="cadipel_panel_chips">
                        <button type="button" class="cadipel_chip" tabindex="-1" data-i18n="assistant_chip_1">¿Qué soluciones ofrecen?</button>
                        <button type="button" class="cadipel_chip" tabindex="-1" data-i18n="assistant_chip_2">Quiero automatizar mi línea de producción</button>
                        <button type="button" class="cadipel_chip" tabindex="-1" data-i18n="assistant_chip_3">Muéstrame los casos de éxito</button>
                        <button type="button" class="cadipel_chip" tabindex="-1" data-i18n="assistant_chip_4">Quiero hablar con el equipo</button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" class="cadipel_island_bubble" id="cadipel_island_bubble">
            <span class="cadipel_island_bubble_text">
                <strong data-i18n="assistant_bar_hello">¡Hola!</strong>
                <span data-i18n="assistant_bar_hello_text">Soy tu asistente de CADIPEL.</span>
            </span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </button>
    </div>
        <span class="cadipel_island_line" aria-hidden="true"><i></i><i></i></span>
        <button type="button" class="cadipel_island_handle" id="cadipel_island_handle" tabindex="-1" data-i18n-aria-label="assistant_bar_open" aria-label="Asistente IA de Cadipel">
            <span class="cadipel_island_handle_glow" aria-hidden="true"></span>
            <svg class="cadipel_island_handle_svg" viewBox="0 0 96 16" width="56" height="9.33" aria-hidden="true">
                <defs>
                    <linearGradient id="cadipel_handle_fill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#8ea6ec"/>
                        <stop offset="1" stop-color="#5470cf"/>
                    </linearGradient>
                    <linearGradient id="cadipel_handle_sweep" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0" stop-color="#fff" stop-opacity="0"/>
                        <stop offset="0.5" stop-color="#fff" stop-opacity="0.75"/>
                        <stop offset="1" stop-color="#fff" stop-opacity="0"/>
                    </linearGradient>
                    <clipPath id="cadipel_handle_clip"><path d="M0,0 A7,7 0 0 1 7,7 L7,8 A8,8 0 0 0 15,16 L81,16 A8,8 0 0 0 89,8 L89,7 A7,7 0 0 1 96,0 Z"/></clipPath>
                </defs>
                <path d="M0,0 A7,7 0 0 1 7,7 L7,8 A8,8 0 0 0 15,16 L81,16 A8,8 0 0 0 89,8 L89,7 A7,7 0 0 1 96,0 Z" fill="url(#cadipel_handle_fill)"/>
                <g clip-path="url(#cadipel_handle_clip)">
                    <rect class="cadipel_island_handle_sweep" x="-40" y="0" width="40" height="16" fill="url(#cadipel_handle_sweep)"/>
                    <rect x="0" y="0" width="96" height="6" fill="#fff" opacity="0.18"/>
                </g>
                <path d="M7,7 L7,8 A8,8 0 0 0 15,16 L81,16 A8,8 0 0 0 89,8 L89,7" fill="none" stroke="#fff" stroke-opacity="0.9" stroke-width="1.2" stroke-linecap="round"/>
                <rect class="cadipel_island_handle_dash" x="38" y="6.5" width="20" height="2.4" rx="1.2" fill="#fff"/>
            </svg>
        </button>
    </div>
