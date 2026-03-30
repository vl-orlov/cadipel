<div class="header">
    <div class="header_container">
        <div class="logo">
            <a href="?" aria-label="CADIPEL home">
                <img src="img/logo.png" alt="CADIPEL" class="logo_image">
            </a>
        </div>
        <div class="header_right">
            <button class="header_back_button" onclick="window.location.href='?page=landing'" aria-label="Volver">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12.5 15L7.5 10L12.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Volver</span>
            </button>
            <div class="home_lang" onclick="toggleLangMenu()">
                <img src="img/icons/lang.png">
                <span id="current_lang">ES</span>
                <ul id="home_lang_menu" class="home_lang_menu hidden">
                    <li onclick="setLang('<?= $page ?>', 'es')">Español</li>
                    <li onclick="setLang('<?= $page ?>', 'en')">English</li>
                </ul>
            </div>
        </div>
    </div>
</div>