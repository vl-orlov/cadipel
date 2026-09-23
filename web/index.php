<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Cadipel</title>
    <link rel="icon" type="image/png" href="img/icons/logo_icon.png">
    <link rel="shortcut icon" type="image/png" href="img/icons/logo_icon.png">
    <link rel="apple-touch-icon" href="img/icons/logo_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/avatar.css">
    <script src="js/i18n.js"></script>
</head>
<body>

<?
$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';

SWITCH ( $page ) {
    case 'nosotros':                        include "includes/nosotros.php";                     break;
    case 'companias_asociadas':             include "includes/companias_asociadas.php";          break;
    case 'lo_que_hacemos':                  include "includes/lo_que_hacemos.php";               break;
    case 'casos_de_exito':                  include "includes/casos_de_exito.php";               break;
    case 'fin_tech':                        include "includes/fin_tech.php";                    break;
    case 'soluciones_agro':                 include "includes/soluciones_agro.php";             break;
    case 'ingenieria_desarrollo':           include "includes/ingenieria_desarrollo.php";       break;
    case 'automatizacion_industrial':       include "includes/automatizacion_industrial.php";   break;
    case 'soluciones_integrales':           include "includes/soluciones_integrales.php";       break;
    case 'soluciones_industria':            include "includes/soluciones_industria.php";        break;
    case 'sistemas_especiales':             include "includes/sistemas_especiales.php";         break;
    case 'seguridad_personal':              include "includes/seguridad_personal.php";          break;
    case 'landing':                         include "includes/landing.php";                     break;
    default:                                include "includes/landing.php";                     break;
}
?>

<!-- ASISTENTE IA: chat a pantalla completa en otro dominio -->
<div class="cadipel_assistant_float">
    <div class="cadipel_assistant_bubble" id="cadipel_float_bubble" role="link" tabindex="0">
        <button type="button" class="cadipel_assistant_bubble_close" data-i18n-aria-label="assistant_bubble_close" aria-label="Cerrar">✕</button>
        <p class="cadipel_assistant_bubble_eyebrow" data-i18n="assistant_bubble_eyebrow">Cadipel</p>
        <p class="cadipel_assistant_bubble_text" data-i18n="assistant_bubble_text">¿Tenés dudas sobre nuestras soluciones? Preguntame 👋</p>
    </div>
    <a class="cadipel_assistant_float_btn" href="https://cadipel.pribridge.pro/?lang=es" target="_blank" rel="noopener" data-chat-link aria-label="Asistente IA de Cadipel">
        <span class="cadipel_assistant_float_avatar" id="cadipel_float_avatar"></span>
    </a>
</div>
<script src="js/assistant-avatar.js"></script>
<script>
    var floatAvatar = document.getElementById('cadipel_float_avatar');
    if (floatAvatar && window.CadipelAssistant && CadipelAssistant.avatar) {
        CadipelAssistant.avatar.create(floatAvatar, { size: 64 });
    }

    (function () {
        var bubble = document.getElementById('cadipel_float_bubble');
        var link = document.querySelector('[data-chat-link]');
        if (!bubble || !link) return;
        var closeBtn = bubble.querySelector('.cadipel_assistant_bubble_close');
        var DISMISS_KEY = 'cadipel_assistant_dismissed';
        var dismissed = false;
        try { dismissed = sessionStorage.getItem(DISMISS_KEY) === '1'; } catch (e) {}
        var showTimer, hideTimer;
        function showBubble() {
            if (dismissed) return;
            bubble.classList.add('is-visible');
            hideTimer = setTimeout(hideBubble, 40000);
        }
        function hideBubble() {
            clearTimeout(hideTimer);
            bubble.classList.remove('is-visible');
            if (!dismissed) showTimer = setTimeout(showBubble, 120000);
        }
        function openChat() {
            window.open(link.href, '_blank', 'noopener');
        }
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            dismissed = true;
            try { sessionStorage.setItem(DISMISS_KEY, '1'); } catch (err) {}
            clearTimeout(hideTimer);
            clearTimeout(showTimer);
            bubble.classList.remove('is-visible');
        });
        bubble.addEventListener('click', openChat);
        bubble.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openChat();
            }
        });
        if (!dismissed) showTimer = setTimeout(showBubble, 1000);
    })();
</script>
<!-- END ASISTENTE IA -->

</body>
</html>
