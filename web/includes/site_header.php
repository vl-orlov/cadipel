<?php
if (!isset($page)) {
    $page = '';
}
$header_i18n_ns = $page !== '' ? $page : 'landing';
$header_i18n_ns_esc = htmlspecialchars($header_i18n_ns, ENT_QUOTES, 'UTF-8');
?>
<div class="header">
    <div class="header_container">
        <div class="logo">
            <img src="img/logo.png" alt="CADIPEL" class="logo_image">
        </div>
        <div class="header_right">
            <nav class="nav" id="nav_menu" aria-label="Principal">
                <a href="?page=nosotros" class="<?php echo $page === 'nosotros' ? 'nav_link_active' : ''; ?>" <?php echo $page === 'nosotros' ? 'aria-current="page"' : ''; ?> data-i18n="nav_business_units">Nosotros</a>
                <a href="?page=companias_asociadas" class="<?php echo $page === 'companias_asociadas' ? 'nav_link_active' : ''; ?>" <?php echo $page === 'companias_asociadas' ? 'aria-current="page"' : ''; ?> data-i18n="nav_companies">Compañías asociadas </a>
                <a href="?page=lo_que_hacemos" class="<?php echo $page === 'lo_que_hacemos' ? 'nav_link_active' : ''; ?>" <?php echo $page === 'lo_que_hacemos' ? 'aria-current="page"' : ''; ?> data-i18n="nav_success_cases">Lo que hacemos</a>
                <a href="?page=casos_de_exito" class="<?php echo $page === 'casos_de_exito' ? 'nav_link_active' : ''; ?>" <?php echo $page === 'casos_de_exito' ? 'aria-current="page"' : ''; ?> data-i18n="nav_about">Casos de éxito</a>
                <a href="#contacto" class="<?php echo $page === 'contacto' ? 'nav_link_active' : ''; ?>" <?php echo $page === 'contacto' ? 'aria-current="page"' : ''; ?> data-i18n="nav_contact">Contacto</a>
            </nav>
            <button class="burger_menu" onclick="toggleNavMenu()" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="home_lang" onclick="toggleLangMenu()">
                <img src="img/icons/lang.png" alt="">
                <span id="current_lang">ES</span>
                <ul id="home_lang_menu" class="home_lang_menu hidden">
                    <li onclick="setLang('<?= $header_i18n_ns_esc ?>', 'es')">Español</li>
                    <li onclick="setLang('<?= $header_i18n_ns_esc ?>', 'en')">English</li>
                </ul>
            </div>
        </div>
    </div>
</div>
