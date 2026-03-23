<?php include __DIR__ . '/site_header.php'; ?>
<div class="hero_solucion hero_nosotros">
    <div class="hero_solucion_background">
        <img src="img/lo_que_hacemos/lo_que_hacemos0.png" alt="" class="hero_solucion_bg_image hero_nosotros_bg_image">
    </div>
    <div class="hero_solucion_content hero_nosotros_content">
        <h1 class="hero_solucion_title hero_nosotros_title" data-i18n="nosotros_hero_title">Sobre CADIPEL</h1>
        <a onclick="window.location.href='?page=landing'" class="hero_nosotros_back" data-i18n="nosotros_hero_back">Volver</a>
    </div>
</div>

<!-- CONTENT -->
<section class="whatdo_cards_section" aria-label="Lo que hacemos">
    <div class="whatdo_cards_inner">
        <div class="whatdo_cards_grid">
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos1.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_1">Diseño y desarrollo de equipos electrónicos para distintas industrias aplicando el DEM (Design For Manufacturing).</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos2.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_2">Fabricación de equipos electrónicos en series pequeñas, medianas y grandes optimizando el TTM (Time To Market).</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos3.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_3">Desarrollo de sistemas de bases de datos relacionales y resolvemos cualquier consulta SQL diferente estándares, y formatos SQL.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos4.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_4">Desarrollos de sistemas especializados para la gestión de centros de datos.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos5.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_5">Para el desarrollo, utilizamos las modernas tecnologías C, HTML, DHTML, JavaScript y creemos nuevos conceptos basados en ellas.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos6.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_6">Desarrollos de entornos gráficos que incluyen interfaces modernas con control de voz y sistemas de búsqueda, creamos conexiones API para la integración en varios sistemas y procesos externos.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos7.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_7">Creamos nuevas metodologías y KNOW-HOW en la esfera de TI para crear productos modernos de alta tecnología basados en las tecnologías "Blockchain" y "Only-WEB".</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos8.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_8">Desarrollo de software embebido e interfaces de usuario/servidor a medida.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos9.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_9">Nuestros profesionales utilizan C++, PHP, Python, Assembler, SQL, y otros lenguajes de programación y consulta.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos10.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_10">Soporte técnico y mantenimiento local y continuo.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos11.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_11">Desarrollamos aplicaciones para diferentes sistemas operativos: Windows, Linux, Android, iOS, FreeBSD UNIX.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos12.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_12">Creamos sistemas de servidor, utilizando plataformas UNIX y con la creación e instalación completa de Firewall, DNS-Servers, WEB-Servers, FTP-servers, Mail-Servers, SMS-Servers, Voice-Servers.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos13.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_13">Trabajamos con sistemas y protocolos RDP, SSH, OSP, JumpServer, sincronizamos procesos con el sistema SWIFT.</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos14.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_14">Desarrollo de sistemas bancarios y "Transaction-CORE", desarrollo de servicios APIs para diversas tareas bancarias (conexiones de adquisición, sistemas de trabajo con clientes, automatización y sincronización de procesos externos e internos, etc.).</p></article>
            <article class="whatdo_card"><img src="img/lo_que_hacemos/lo_que_hacemos15.png" alt="" class="whatdo_card_img" loading="lazy"><p class="whatdo_card_text" data-i18n="whatdo_card_15">Desarrollos en IA y Tecnología de voz.</p></article>
        </div>
    </div>
</section>
<!-- END CONTENT -->

<? 
include "includes/landing_footer.php";
?>

<script>
function toggleLangMenu() {
  const menu = document.getElementById('home_lang_menu');
  menu.classList.toggle('hidden');
}
initLang('lo_que_hacemos');
</script>