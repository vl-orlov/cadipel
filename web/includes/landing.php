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

<section class="cta_section">
    <div class="cta_container">
        <div class="cta_content">
            <div class="cta_logo">
                <img src="img/logo.png" alt="CADIPEL" class="cta_logo_image">
            </div>
            <h2 class="cta_title" data-i18n="cta_title">Agenda una cita con nuestro equipo</h2>
            <p class="cta_text" data-i18n="cta_text">Contanos sobre tu proyecto y evaluemos juntos cómo podemos ayudarte con ingeniería electrónica y software a medida</p>
            <a href="#" class="cta_button" data-i18n="cta_button">Agendar reunión</a>
        </div>
        <div class="cta_image_wrapper">
            <div class="cta_image_shape"></div>
            <img src="img/team.png" class="cta_image">
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer_container">
        <div class="footer_left">
            <div class="footer_contact">
                <p class="footer_address" data-i18n="footer_address">Av. Independencia 4281, CABA (CP 1226)</p>
                <p class="footer_phone" data-i18n="footer_phone">+54 11 6264 4638</p>
                <p class="footer_email" data-i18n="footer_email">info@cadipel.com.ar</p>
            </div>
            <div class="footer_social">
                <a href="#" class="social_link" aria-label="LinkedIn">
                    <img src="img/icons/linkedin_icon.png" alt="LinkedIn">
                </a>
                <a href="#" class="social_link" aria-label="Instagram">
                    <img src="img/icons/instagram_icon.png" alt="Instagram">
                </a>
            </div>
        </div>
        <div class="footer_right">
            <h3 class="footer_title" data-i18n="footer_title">Tu aliado tecnológico</h3>
            <p class="footer_description" data-i18n="footer_description">Escuchamos tu necesidad y diseñamos una solución a medida para llevar tu proyecto a producción.</p>
        </div>
    </div>
    <div class="footer_copyright">
        <p data-i18n="footer_copyright">© CADIPEL - Todos los derechos reservados.</p>
    </div>
</footer>

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

