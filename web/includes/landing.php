<header class="header">
    <div class="header_container">
        <div class="logo">
            <img src="img/logo.png" alt="CADIPEL" class="logo_image">
        </div>
        <nav class="nav" id="nav_menu">
            <a href="#" data-i18n="nav_business_units">Unidades de negocio</a>
            <a href="#" data-i18n="nav_companies">Compañías</a>
            <a href="#" data-i18n="nav_success_cases">Casos de éxito</a>
            <a href="#" data-i18n="nav_about">Sobre CADIPEL</a>
            <a href="#" data-i18n="nav_contact">Contacto</a>
        </nav>
        <div class="header_right">
            <button class="burger_menu" onclick="toggleNavMenu()" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="home_lang" onclick="toggleLangMenu()">
                <img src="img/icons/lang.png">
                <span id="current_lang">ES</span>
                <ul id="home_lang_menu" class="home_lang_menu hidden">
                    <li onclick="setLang('landing', 'es')">Español</li>
                    <li onclick="setLang('landing', 'en')">English</li>
                </ul>
            </div>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero_background">
        <img src="img/circuit-board.jpg" alt="Circuit board" class="hero_bg_image">
        <div class="hero_overlay"></div>
    </div>
    <div class="hero_content">
        <h1 class="hero_title" data-i18n-html="hero_title">Compañía internacional<br>desarrolladora y fabricante<br>de tecnologías en electrónica<br>y software</h1>
        <a href="#" class="hero_cta" data-i18n="hero_cta">Descubrir nuestras soluciones</a>
    </div>
</section>

<script>
function toggleLangMenu() {
  const menu = document.getElementById('home_lang_menu');
  menu.classList.toggle('hidden');
}

function toggleNavMenu() {
  const nav = document.getElementById('nav_menu');
  const burger = document.querySelector('.burger_menu');
  nav.classList.toggle('nav_open');
  burger.classList.toggle('burger_active');
}

document.addEventListener('DOMContentLoaded', () => {
  initLang('landing');
});

document.addEventListener('click', function (e) {
  const langBox = document.querySelector('.home_lang');
  const langMenu = document.getElementById('home_lang_menu');
  const nav = document.getElementById('nav_menu');
  const burger = document.querySelector('.burger_menu');
  
  if (langBox && !langBox.contains(e.target)) {
    langMenu.classList.add('hidden');
  }
  
  if (nav && burger && !nav.contains(e.target) && !burger.contains(e.target)) {
    nav.classList.remove('nav_open');
    burger.classList.remove('burger_active');
  }
});
</script>

