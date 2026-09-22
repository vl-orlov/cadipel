<?php
header('X-Robots-Tag: noindex, nofollow');
/** URL con ?v=<mtime> para que un deploy no quede tapado por caché del navegador/nginx. */
function asset(string $path): string
{
    $file = __DIR__ . '/' . $path;
    return $path . '?v=' . (is_file($file) ? filemtime($file) : '0');
}
header('Cache-Control: no-cache');
$lang = (($_GET['lang'] ?? '') === 'en') ? 'en' : 'es';
$langV = 0;
foreach (['lang/chat/es.json', 'lang/chat/en.json'] as $rel) {
    $langFile = __DIR__ . '/' . $rel;
    if (is_file($langFile)) {
        $langV = max($langV, filemtime($langFile));
    }
}
?><!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <title>Asistente Cadipel</title>
    <link rel="icon" type="image/png" href="img/logo_icon.png">
    <link rel="apple-touch-icon" href="img/logo_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/avatar.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/chat.css') ?>">
    <script>
        // Tema antes del primer render, para evitar el destello claro/oscuro.
        window.CADIPEL_LANG_V = <?= json_encode((string) $langV) ?>;
        try {
            var th = localStorage.getItem('cadipel_chat_theme');
            if (!th) th = matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', th);
        } catch (e) { document.documentElement.setAttribute('data-theme', 'light'); }
    </script>
</head>
<body>
<div class="app" id="app">

    <header class="topbar">
        <button class="icon_btn sidebar_toggle" id="sidebar_toggle" type="button" data-i18n-aria-label="aria_menu" aria-label="Menú">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a class="brand" href="https://www.cadipel.com.ar/" target="_blank" rel="noopener" aria-label="CADIPEL">
            <img src="img/logo.png" alt="CADIPEL" class="brand_logo">
        </a>
        <div class="topbar_spacer"></div>
        <a class="pill_btn pill_btn--primary hide_sm" id="contact_btn" href="https://www.cadipel.com.ar/#contacto" target="_blank" rel="noopener" data-i18n="contact_team">Hablar con el equipo</a>
        <a class="pill_btn pill_btn--ghost hide_sm" href="https://www.cadipel.com.ar/" target="_blank" rel="noopener" data-i18n="back_to_site">Volver al sitio</a>
        <button class="pill_btn pill_btn--ghost hide_sm" id="lang_btn" type="button" data-i18n-aria-label="aria_lang" aria-label="Idioma">ES</button>
        <button class="icon_btn hide_sm" id="theme_btn" type="button" data-i18n-aria-label="aria_theme" aria-label="Tema"></button>
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="new_chat_btn" id="new_chat_btn" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                <span data-i18n="new_chat">Nuevo chat</span>
            </button>
            <p class="sidebar_label" data-i18n="conversations">Conversaciones</p>
            <ul class="conv_list" id="conv_list"></ul>
            <div class="sidebar_footer">
                <!-- En mobile, el topbar solo tiene menú + logo: contacto/idioma/tema viven acá. -->
                <div class="sidebar_quick_actions show_sm">
                    <a class="pill_btn pill_btn--primary" href="https://www.cadipel.com.ar/#contacto" target="_blank" rel="noopener" data-i18n="contact_team">Hablar con el equipo</a>
                    <div class="sidebar_quick_row">
                        <button class="pill_btn pill_btn--ghost" id="lang_btn_sidebar" type="button" data-i18n-aria-label="aria_lang" aria-label="Idioma">ES</button>
                        <button class="icon_btn" id="theme_btn_sidebar" type="button" data-i18n-aria-label="aria_theme" aria-label="Tema"></button>
                    </div>
                </div>
                <button class="side_link" id="export_btn" type="button" data-i18n="export_chat">Exportar conversación</button>
                <a class="side_link show_sm" href="https://www.cadipel.com.ar/" target="_blank" rel="noopener" data-i18n="back_to_site">Volver al sitio</a>
            </div>
        </aside>
        <div class="sidebar_backdrop" id="sidebar_backdrop"></div>

        <main class="chat">
            <div class="chat_head">
                <div class="chat_head_avatar" id="head_avatar"></div>
                <div class="chat_head_text">
                    <strong data-i18n="assistant_name">Asistente Cadipel</strong>
                    <span class="chat_head_status"><i></i><span data-i18n="online">En línea</span></span>
                </div>
            </div>
            <div class="messages_scroll" id="messages_scroll">
                <div class="messages" id="messages" role="log" aria-live="polite"></div>
            </div>

            <div class="composer_wrap">
                <div class="composer" id="composer">
                    <div class="text_field" id="text_field">
                        <textarea id="input" rows="1" maxlength="2000" data-i18n-placeholder="placeholder" placeholder="Escribí tu pregunta..."></textarea>

                        <!-- Franja de grabación (dictado): reemplaza el textarea mientras se mantiene presionado el mic -->
                        <div class="hold_strip" id="hold_strip" hidden>
                            <div class="hold_trash_anchor" id="hold_trash_anchor" aria-hidden="true">
                                <img src="img/icons/eliminar.svg" class="hold_trash" alt="">
                            </div>
                            <div class="hold_meter" id="hold_meter">
                                <span class="hold_dot" aria-hidden="true"></span>
                                <span class="hold_time" id="hold_time">0:00,00</span>
                                <div class="hold_wave" id="hold_wave" aria-hidden="true"></div>
                            </div>
                            <span class="hold_cancel_hint" id="hold_cancel_hint" aria-hidden="true" data-i18n="release_to_cancel">Soltá para cancelar</span>
                        </div>

                        <!-- Animación de "tacho" al cancelar por swipe -->
                        <div class="replay_strip" id="replay_strip" aria-hidden="true" hidden></div>
                    </div>

                    <div class="composer_actions">
                        <div class="mic_slot" id="mic_slot">
                            <div class="mic_lock_zone" id="mic_lock_zone" aria-hidden="true">
                                <img src="img/icons/mic_lock.svg" class="mic_lock_icon" alt="">
                                <img src="img/icons/mic_arrow_up.svg" class="mic_lock_arrow" alt="">
                                <span class="mic_lock_label" data-i18n="lock_mic">Bloquear</span>
                            </div>
                            <button class="mic_btn" id="mic_btn" type="button" style="touch-action:none" data-i18n-aria-label="aria_mic" aria-label="Dictar por voz">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="3" width="6" height="12" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg>
                            </button>
                        </div>

                        <!-- Modo "bloqueado" (manos libres): cancelar arriba, enviar al costado -->
                        <div class="locked_actions" id="locked_actions" hidden>
                            <button class="locked_cancel" id="locked_cancel_btn" type="button" data-i18n-aria-label="aria_rec_cancel" aria-label="Cancelar grabación">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
                            </button>
                            <button class="locked_send" id="locked_send_btn" type="button" data-i18n-aria-label="aria_rec_send" aria-label="Terminar y enviar">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                            </button>
                        </div>

                        <button class="voice_toggle_btn" id="voice_toggle_btn" type="button" data-i18n-aria-label="aria_voice_mode" aria-label="Modo voz">
                            <span class="orb_wave" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
                        </button>

                        <button class="send_btn" id="send_btn" type="button" data-i18n-aria-label="aria_send" aria-label="Enviar">
                            <svg class="ic_send" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
                            <svg class="ic_stop" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="6" width="12" height="12" rx="2"/></svg>
                        </button>
                    </div>
                </div>
                <p class="disclaimer" data-i18n="disclaimer">El asistente puede equivocarse. Para consultas puntuales, hablá con nuestro equipo.</p>
            </div>

            <!-- Modo voz: pantalla dedicada con avatar + orbe "mantené para hablar" -->
            <div class="voice_view" id="voice_view">
                <button class="back_text_btn" id="back_text_btn" type="button" data-i18n-aria-label="aria_text_mode" aria-label="Modo texto">
                    <img src="img/icons/camia_text.svg" alt="" width="20" height="20">
                </button>

                <div class="voice_hero">
                    <div class="voice_avatar_wrap" id="voice_avatar"></div>
                    <div class="voice_caption" id="voice_caption">
                        <button class="stop_tts_btn" id="voice_stop_tts_btn" type="button" hidden data-i18n-aria-label="aria_stop_audio" aria-label="Detener audio">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 5 6 9H2v6h4l5 4V5z"/><path d="M16 9l6 6M22 9l-6 6"/></svg>
                        </button>
                        <div class="voice_caption_body md" id="voice_caption_body"></div>
                    </div>
                </div>

                <div class="voice_dock">
                    <div class="voice_orb_wrap" id="voice_orb_wrap">
                        <button class="voice_orb" id="voice_orb" type="button" style="touch-action:none" data-i18n-aria-label="aria_voice_orb" aria-label="Mantené para hablar con el asistente">
                            <span class="orb_wave orb_wave--lg" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
                        </button>
                    </div>
                    <div class="voice_footer">
                        <span class="voice_timer" id="voice_timer"></span>
                        <p class="voice_status" id="voice_status" data-i18n="hold_to_talk">Mantené para hablar</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script src="<?= asset('js/md.js') ?>"></script>
<script src="<?= asset('js/assistant-lipsync.js') ?>"></script>
<script src="<?= asset('js/assistant-avatar.js') ?>"></script>
<script src="<?= asset('js/assistant-tts.js') ?>"></script>
<script src="<?= asset('js/assistant-voice.js') ?>"></script>
<script src="<?= asset('js/assistant-mic-feedback.js') ?>"></script>
<script src="<?= asset('js/assistant-voice-hold.js') ?>"></script>
<script src="<?= asset('js/store.js') ?>"></script>
<script src="<?= asset('js/chat-app.js') ?>"></script>
</body>
</html>
