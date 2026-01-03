<!-- HEADER -->
<div class="header">
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
</div>
<!-- END HEADER -->

<!-- HERO SECTION -->
<div class="hero">
    <div class="hero_background">
        <img src="img/circuit-board.jpg" alt="Circuit board" class="hero_bg_image">
        <div class="hero_overlay"></div>
    </div>
    <div class="hero_content">
        <h1 class="hero_title" data-i18n-html="hero_title">Compañía internacional<br>desarrolladora y fabricante<br>de tecnologías en electrónica<br>y software</h1>
        <a href="#" class="hero_cta" data-i18n="hero_cta">Descubrir nuestras soluciones</a>
    </div>
</div>
<!-- END HERO SECTION -->

<!-- SERVICES SECTION -->
<div class="services_section">
    <div class="services_container">
        <h2 class="services_title" data-i18n="services_title">Acompañamos a empresas que buscan integrar tecnología electrónica y software sin frenar su operación ni sumar complejidad interna</h2>
        <div class="services_grid">
            <div class="service_card">
                <div class="service_image_wrapper">
                    <img src="img/service1.png" alt="De la idea al producto" class="service_image">
                </div>
                <div class="service_content">
                    <h3 class="service_card_title" data-i18n="service1_title">De la idea al producto</h3>
                    <p class="service_card_text" data-i18n="service1_text">Diseñamos y desarrollamos soluciones electrónicas y de software desde la etapa conceptual hasta la validación funcional</p>
                </div>
            </div>
            <div class="service_card">
                <div class="service_image_wrapper">
                    <img src="img/service2.png" alt="Del prototipo a la producción" class="service_image">
                </div>
                <div class="service_content">
                    <h3 class="service_card_title" data-i18n="service2_title">Del prototipo a la producción</h3>
                    <p class="service_card_text" data-i18n="service2_text">Reducimos tiempos de desarrollo y llegada al mercado mediante prototipado rápido, DFM y fabricación en serie</p>
                </div>
            </div>
            <div class="service_card">
                <div class="service_image_wrapper">
                    <img src="img/service3.png" alt="Tecnología para las industrias" class="service_image">
                </div>
                <div class="service_content">
                    <h3 class="service_card_title" data-i18n="service3_title">Tecnología para las industrias</h3>
                    <p class="service_card_text" data-i18n="service3_text">Integramos soluciones electrónicas y digitales en entornos reales: industria, agro, fintech, seguridad y más</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SERVICES SECTION -->

<!-- WHAT WE DO SECTION -->
<div class="what_we_do_section">
    <div class="what_we_do_container">
        <div class="what_we_do_content">
            <div class="what_we_do_list">
                <h2 class="what_we_do_title" data-i18n="what_we_do_title">Lo que hacemos</h2>
                <div class="what_we_do_items">
                    <div class="what_we_do_item">
                        <div class="what_we_do_icon">
                            <img src="img/icons/what_we_do_item1.png" alt="Design and development">
                        </div>
                        <p class="what_we_do_text" data-i18n="what_we_do_item1">Diseño y desarrollo de equipos electrónicos para distintas industrias.</p>
                    </div>
                    <div class="what_we_do_item">
                        <div class="what_we_do_icon">
                            <img src="img/icons/what_we_do_item2.png" alt="Software development">
                        </div>
                        <p class="what_we_do_text" data-i18n="what_we_do_item2">Desarrollo de software embebido e interfaces de usuario/servidor a medida.</p>
                    </div>
                    <div class="what_we_do_item">
                        <div class="what_we_do_icon">
                            <img src="img/icons/what_we_do_item3.png" alt="Product cycle">
                        </div>
                        <p class="what_we_do_text" data-i18n="what_we_do_item3">Acompañamiento en todo el ciclo de producto: especificación, diseño, validación y puesta en marcha.</p>
                    </div>
                    <div class="what_we_do_item">
                        <div class="what_we_do_icon">
                            <img src="img/icons/what_we_do_item4.png" alt="Manufacturing">
                        </div>
                        <p class="what_we_do_text" data-i18n="what_we_do_item4">Fabricación de equipos electrónicos en series pequeñas, medianas y grandes.</p>
                    </div>
                    <div class="what_we_do_item">
                        <div class="what_we_do_icon">
                            <img src="img/icons/what_we_do_item5.png" alt="Technical support">
                        </div>
                        <p class="what_we_do_text" data-i18n="what_we_do_item5">Soporte técnico y mantenimiento continuo para garantizar el rendimiento óptimo.</p>
                    </div>
                </div>
            </div>
            <div class="how_we_do_list">
                <h2 class="how_we_do_title" data-i18n="how_we_do_title">Cómo lo hacemos</h2>
                <div class="how_we_do_items">
                    <div class="how_we_do_item">
                        <div class="how_we_do_icon">
                            <img src="img/icons/what_we_do_item4.png" alt="Rapid prototyping">
                        </div>
                        <div class="how_we_do_item_content">
                            <h3 class="how_we_do_item_title" data-i18n="how_we_do_item1_title">Prototipado rápido y Time To Market (TTM)</h3>
                            <p class="how_we_do_item_text" data-i18n="how_we_do_item1_text">Fabricación propia de circuitos impresos para prototipos rápidos, reduciendo los tiempos de desarrollo y llegada al mercado.</p>
                        </div>
                    </div>
                    <div class="how_we_do_item">
                        <div class="how_we_do_icon">
                            <img src="img/icons/how_we_do_item2_title.png" alt="DFM">
                        </div>
                        <div class="how_we_do_item_content">
                            <h3 class="how_we_do_item_title" data-i18n="how_we_do_item2_title">Diseño orientado a fabricación (DFM)</h3>
                            <p class="how_we_do_item_text" data-i18n="how_we_do_item2_text">Diseñamos pensando en serie: viabilidad productiva, optimización de costos y procesos, y calidad consistente.</p>
                        </div>
                    </div>
                    <div class="how_we_do_item">
                        <div class="how_we_do_icon">
                            <img src="img/icons/how_we_do_item3_title.png" alt="Strategic alliance">
                        </div>
                        <div class="how_we_do_item_content">
                            <h3 class="how_we_do_item_title" data-i18n="how_we_do_item3_title">Alianza estratégica con ASSISI SRL</h3>
                            <p class="how_we_do_item_text" data-i18n="how_we_do_item3_text">Ecosistema productivo con montaje THT y SMT y experiencia en múltiples industrias para escalar de prototipo a serie.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="what_we_do_line">
            <div class="what_we_do_line_progress" id="scroll_progress">
                <div class="what_we_do_line_indicator"></div>
            </div>
        </div>
    </div>
</div>
<!-- END WHAT WE DO SECTION -->

<!-- PROJECTS SECTION -->
<div class="projects_section">
    <div class="projects_container">
        <h2 class="projects_title" data-i18n="projects_title">Trabajamos con empresas de distintas industrias, integrando tecnología electrónica y software en proyectos concretos, medibles y escalables</h2>
        <div class="projects_grid">
            <div class="project_card">
                <div class="project_image_wrapper">
                    <img src="img/project1.png" alt="Industria / Automatización" class="project_image">
                    <div class="project_overlay"></div>
                    <div class="project_content">
                        <span class="project_category" data-i18n="project1_category">Industria / Automatización</span>
                        <h3 class="project_card_title" data-i18n="project1_title">Nombre del proyecto</h3>
                        <p class="project_card_text" data-i18n="project1_text">Modernización de una línea existente mediante electrónica de control, sensado y visualización. Se redujeron tiempos operativos y se mejoró la repetitividad del proceso sin reemplazar maquinaria.</p>
                    </div>
                </div>
            </div>
            <div class="project_card">
                <div class="project_image_wrapper">
                    <img src="img/project2.png" alt="Electrónica / Producto" class="project_image">
                    <div class="project_overlay"></div>
                    <div class="project_content">
                        <span class="project_category" data-i18n="project2_category">Electrónica / Producto</span>
                        <h3 class="project_card_title" data-i18n="project2_title">Nombre del proyecto</h3>
                        <p class="project_card_text" data-i18n="project2_text">Diseño de hardware, firmware y software asociado, con prototipado rápido y validación funcional. Escalado a fabricación aplicando criterios DFM para asegurar viabilidad en serie.</p>
                    </div>
                </div>
            </div>
            <div class="project_card">
                <div class="project_image_wrapper">
                    <img src="img/project3.png" alt="Seguridad / Accesos" class="project_image">
                    <div class="project_overlay"></div>
                    <div class="project_content">
                        <span class="project_category" data-i18n="project3_category">Seguridad / Accesos</span>
                        <h3 class="project_card_title" data-i18n="project3_title">Nombre del proyecto</h3>
                        <p class="project_card_text" data-i18n="project3_text">Implementación de un sistema de acceso y gestión remota, reemplazando llaves y credenciales físicas. Se mejoró la seguridad, el control de usuarios y la trazabilidad de eventos.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END PROJECTS SECTION -->

<!-- CTA SECTION -->
<div class="cta_section">
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
            <div class="cta_image_background cta_image_background_left"></div>
            <div class="cta_image_background cta_image_background_right"></div>
            <img src="img/team.png" class="cta_image">
        </div>
    </div>
</div>
<!-- END CTA SECTION -->

<!-- FOOTER -->
<div class="footer">
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
</div>
<!-- END FOOTER -->

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
  
  // Вертикальный слайдер для секций "Lo que hacemos" и "Cómo lo hacemos"
  const whatWeDoContainer = document.querySelector('.what_we_do_container');
  const whatWeDoList = document.querySelector('.what_we_do_list');
  const howWeDoList = document.querySelector('.how_we_do_list');
  const scrollProgressBar = document.getElementById('scroll_progress');
  
  if (whatWeDoContainer && whatWeDoList && howWeDoList && scrollProgressBar) {
    let scrollProgress = 0;
    let isHovered = false;
    
    whatWeDoContainer.addEventListener('mouseenter', () => {
      isHovered = true;
    });
    
    whatWeDoContainer.addEventListener('mouseleave', () => {
      isHovered = false;
      // Возвращаем к начальному состоянию при уходе мыши
      scrollProgress = 0;
      updateSections();
    });
    
    whatWeDoContainer.addEventListener('wheel', (e) => {
      if (!isHovered) return;
      
      e.preventDefault();
      e.stopPropagation();
      
      // Увеличиваем прогресс при скролле вниз, уменьшаем при скролле вверх
      if (e.deltaY > 0) {
        scrollProgress = Math.min(1, scrollProgress + 0.1);
      } else {
        scrollProgress = Math.max(0, scrollProgress - 0.1);
      }
      
      updateSections();
    }, { passive: false });
    
    function updateSections() {
      // Обновляем градиент линии прогресса
      // scrollProgress = 0: первая половина закрашена (#00b3ff), вторая белая (#ffffff)
      // scrollProgress = 1: первая половина белая (#ffffff), вторая закрашена (#00b3ff)
      // Граница всегда на 50%, но цвета меняются местами
      const firstHalfColor = scrollProgress <= 0.5 
        ? (scrollProgress === 0 ? '#00b3ff' : '#ffffff') 
        : '#ffffff';
      const secondHalfColor = scrollProgress <= 0.5 
        ? '#ffffff' 
        : '#00b3ff';
      
      scrollProgressBar.style.background = `linear-gradient(to bottom, ${firstHalfColor} 0%, ${firstHalfColor} 50%, ${secondHalfColor} 50%, ${secondHalfColor} 100%)`;
      
      if (scrollProgress > 0.3) {
        whatWeDoList.classList.add('scrolled');
        howWeDoList.classList.add('visible');
      } else {
        whatWeDoList.classList.remove('scrolled');
        howWeDoList.classList.remove('visible');
      }
    }
  }
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

