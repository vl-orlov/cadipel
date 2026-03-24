<?php include __DIR__ . '/site_header.php'; ?>
<div class="hero_solucion hero_nosotros nosotros_page_hero">
    <div class="hero_solucion_background">
        <img src="img/nosotros/nosotros0.png" alt="" class="hero_solucion_bg_image hero_nosotros_bg_image">
    </div>
    <div class="hero_solucion_content hero_nosotros_content">
        <h1 class="hero_solucion_title hero_nosotros_title" data-i18n="nosotros_hero_title">Sobre CADIPEL</h1>
        <button type="button" class="hero_nosotros_scroll" data-i18n-aria-label="nosotros_hero_scroll_aria" aria-label="Ir al contenido principal">
            <svg class="hero_nosotros_scroll_icon" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="24" cy="24" r="21" stroke="currentColor" stroke-width="1.25"/>
                <path d="M24 17v10M18 25l6 6 6-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    <a href="?page=landing" class="hero_nosotros_back" data-i18n="nosotros_hero_back">Volver</a>
</div>

<!-- NOSOTROS CONTENT -->
<div id="nosotros_main" class="nosotros_story_section" aria-label="Sobre CADIPEL">
    <div class="nosotros_story_inner">
        <div class="nosotros_story_row">
            <div class="nosotros_story_text">
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row1_p1">Somos un grupo de Compañías Internacionales e Instituciones con amplia trayectoria (entre 15 y más de 60 años), que Investigan, Desarrollan y Fabrican equipamiento y software en el campo de la Tecnología de la Información y la Biometría.</p>
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row1_p2">Nuestra labor comprende todo el ciclo de diseño y desarrollo de equipos electrónicos desde la idea del producto, dando soporte desde la fase de especificación hasta la entrega del producto llave en mano.</p>
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row1_p3">Nuestros profesionales son expertos en diseño de hardware electrónico, diseño de software embebido y diseño de software de interfaces de usuario / servidor.</p>
            </div>
            <div class="nosotros_story_media">
                <img src="img/nosotros/nosotros1.png" alt="" class="nosotros_story_img" loading="lazy">
            </div>
        </div>
        <div class="nosotros_story_row nosotros_story_row_reverse">
            <div class="nosotros_story_media">
                <img src="img/nosotros/nosotros2.png" alt="" class="nosotros_story_img" loading="lazy">
            </div>
            <div class="nosotros_story_text">
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row2_p1">Entregamos productos llave en mano según los requisitos particulares requeridos por el cliente.</p>
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row2_p2">Nuestros diseños para sistemas embebidos abarcan sistemas de lógica programable, sistemas basados en microcontroladores de bajo consumo para alimentación a baterías y diseños de sistemas en tiempo real basados en microprocesadores de última generación.</p>
                <p class="nosotros_story_paragraph" data-i18n="nosotros_row2_p3">Operamos en Latinoamérica, Europa y Norteamérica, ofreciendo soluciones para los sectores aeronáutico, agrícola, industrial, automotriz, médico, bancario y de seguridad.</p>
            </div>
        </div>
    </div>
</div>
<!-- END NOSOTROS CONTENT -->

<? 
include "includes/landing_footer.php";
?>

<script>
function toggleLangMenu() {
  const menu = document.getElementById('home_lang_menu');
  menu.classList.toggle('hidden');
}
initLang('nosotros');
</script>