<!-- HEADER -->
<div class="header">
    <div class="header_container">
        <div class="logo">
            <img src="img/logo.png" alt="CADIPEL" class="logo_image">
        </div>
        <div class="header_right">
            <nav class="nav" id="nav_menu">
                <a href="#" data-i18n="nav_business_units">Unidades de negocio</a>
                <a href="#" data-i18n="nav_companies">Compañías</a>
                <a href="#" data-i18n="nav_success_cases">Casos de éxito</a>
                <a href="#" data-i18n="nav_about">Sobre CADIPEL</a>
                <a href="#" data-i18n="nav_contact">Contacto</a>
            </nav>
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

<!-- BUSINESS UNITS CAROUSEL -->
<div class="business_units_section">
    <div class="business_units_container">
        <h2 class="business_units_title" data-i18n="business_units_title">Organizamos nuestras soluciones en distintas unidades de negocio para adaptarnos a las necesidades de cada industria</h2>
        <div class="business_units_carousel">
            <div class="business_units_track">
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/ingenieria_desarrollo.png" alt="Ingeniería y Desarrollo" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit1_title">Ingeniería y Desarrollo Hard & Soft</h3>
                            <p class="business_unit_text" data-i18n="business_unit1_text">Diseño electrónico, firmware y software a medida para proyectos que requieren alta especialización técnica</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit1_title">Ingeniería y Desarrollo Hard & Soft</h3>
                        <p class="business_unit_text" data-i18n="business_unit1_text">Diseño electrónico, firmware y software a medida para proyectos que requieren alta especialización técnica</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/fintech.png" alt="Fin-Tech" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit2_title">Fin-Tech - Billeteras electrónicas</h3>
                            <p class="business_unit_text" data-i18n="business_unit2_text">Desarrollo y operación de billeteras electrónicas seguras, escalables y listas para integrarse con bancos y medios de pago</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit2_title">Fin-Tech - Billeteras electrónicas</h3>
                        <p class="business_unit_text" data-i18n="business_unit2_text">Desarrollo y operación de billeteras electrónicas seguras, escalables y listas para integrarse con bancos y medios de pago</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/agro.png" alt="Agro" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit3_title">Soluciones específicas para el Agro</h3>
                            <p class="business_unit_text" data-i18n="business_unit3_text">Tecnología aplicada al campo para monitoreo, automatización y control de procesos productivos en entornos rurales</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit3_title">Soluciones específicas para el Agro</h3>
                        <p class="business_unit_text" data-i18n="business_unit3_text">Tecnología aplicada al campo para monitoreo, automatización y control de procesos productivos en entornos rurales</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/automatizacion.png" alt="Automatización" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit4_title">Automatización de líneas de producción</h3>
                            <p class="business_unit_text" data-i18n="business_unit4_text">Modernización electrónica y digital de maquinaria industrial sin necesidad de reemplazar equipos existentes</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit4_title">Automatización de líneas de producción</h3>
                        <p class="business_unit_text" data-i18n="business_unit4_text">Modernización electrónica y digital de maquinaria industrial sin necesidad de reemplazar equipos existentes</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/consorcios.png" alt="Consorcios" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit5_title">Soluciones integrales para Consorcios</h3>
                            <p class="business_unit_text" data-i18n="business_unit5_text">Plataforma digital para control de accesos y seguridad en edificios, viviendas y empresas</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit5_title">Soluciones integrales para Consorcios</h3>
                        <p class="business_unit_text" data-i18n="business_unit5_text">Plataforma digital para control de accesos y seguridad en edificios, viviendas y empresas</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/industria_automotriz.png" alt="Industria Automotriz" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit6_title">Industria Automotriz</h3>
                            <p class="business_unit_text" data-i18n="business_unit6_text">Sistemas electrónicos inteligentes para modernización estética y funcional de vehículos</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit6_title">Industria Automotriz</h3>
                        <p class="business_unit_text" data-i18n="business_unit6_text">Sistemas electrónicos inteligentes para modernización estética y funcional de vehículos</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/seguridad_control.png" alt="Seguridad y Control" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit7_title">Seguridad y Control de Personal</h3>
                            <p class="business_unit_text" data-i18n="business_unit7_text">Gestión electrónica de accesos, presencia y trazabilidad de personas en entornos corporativos e industriales</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit7_title">Seguridad y Control de Personal</h3>
                        <p class="business_unit_text" data-i18n="business_unit7_text">Gestión electrónica de accesos, presencia y trazabilidad de personas en entornos corporativos e industriales</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/desinfeccion_uvc.png" alt="Desinfección UV-C" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit8_title">Sistemas especiales de desinfección - UV-C</h3>
                            <p class="business_unit_text" data-i18n="business_unit8_text">Dispositivos electrónicos para sanitización de aire, superficies y líquidos sin uso de químicos</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit8_title">Sistemas especiales de desinfección - UV-C</h3>
                        <p class="business_unit_text" data-i18n="business_unit8_text">Dispositivos electrónicos para sanitización de aire, superficies y líquidos sin uso de químicos</p>
                    </div>
                </div>
                <!-- Дублируем все карточки для бесконечной прокрутки -->
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/ingenieria_desarrollo.png" alt="Ingeniería y Desarrollo" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit1_title">Ingeniería y Desarrollo Hard & Soft</h3>
                            <p class="business_unit_text" data-i18n="business_unit1_text">Diseño electrónico, firmware y software a medida para proyectos que requieren alta especialización técnica</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit1_title">Ingeniería y Desarrollo Hard & Soft</h3>
                        <p class="business_unit_text" data-i18n="business_unit1_text">Diseño electrónico, firmware y software a medida para proyectos que requieren alta especialización técnica</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/fintech.png" alt="Fin-Tech" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit2_title">Fin-Tech - Billeteras electrónicas</h3>
                            <p class="business_unit_text" data-i18n="business_unit2_text">Desarrollo y operación de billeteras electrónicas seguras, escalables y listas para integrarse con bancos y medios de pago</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit2_title">Fin-Tech - Billeteras electrónicas</h3>
                        <p class="business_unit_text" data-i18n="business_unit2_text">Desarrollo y operación de billeteras electrónicas seguras, escalables y listas para integrarse con bancos y medios de pago</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/agro.png" alt="Agro" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit3_title">Soluciones específicas para el Agro</h3>
                            <p class="business_unit_text" data-i18n="business_unit3_text">Tecnología aplicada al campo para monitoreo, automatización y control de procesos productivos en entornos rurales</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit3_title">Soluciones específicas para el Agro</h3>
                        <p class="business_unit_text" data-i18n="business_unit3_text">Tecnología aplicada al campo para monitoreo, automatización y control de procesos productivos en entornos rurales</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/automatizacion.png" alt="Automatización" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit4_title">Automatización de líneas de producción</h3>
                            <p class="business_unit_text" data-i18n="business_unit4_text">Modernización electrónica y digital de maquinaria industrial sin necesidad de reemplazar equipos existentes</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit4_title">Automatización de líneas de producción</h3>
                        <p class="business_unit_text" data-i18n="business_unit4_text">Modernización electrónica y digital de maquinaria industrial sin necesidad de reemplazar equipos existentes</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/consorcios.png" alt="Consorcios" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit5_title">Soluciones integrales para Consorcios</h3>
                            <p class="business_unit_text" data-i18n="business_unit5_text">Plataforma digital para control de accesos y seguridad en edificios, viviendas y empresas</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit5_title">Soluciones integrales para Consorcios</h3>
                        <p class="business_unit_text" data-i18n="business_unit5_text">Plataforma digital para control de accesos y seguridad en edificios, viviendas y empresas</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/industria_automotriz.png" alt="Industria Automotriz" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit6_title">Industria Automotriz</h3>
                            <p class="business_unit_text" data-i18n="business_unit6_text">Sistemas electrónicos inteligentes para modernización estética y funcional de vehículos</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit6_title">Industria Automotriz</h3>
                        <p class="business_unit_text" data-i18n="business_unit6_text">Sistemas electrónicos inteligentes para modernización estética y funcional de vehículos</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">
                        <img src="img/carousel/seguridad_control.png" alt="Seguridad y Control" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit7_title">Seguridad y Control de Personal</h3>
                            <p class="business_unit_text" data-i18n="business_unit7_text">Gestión electrónica de accesos, presencia y trazabilidad de personas en entornos corporativos e industriales</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit7_title">Seguridad y Control de Personal</h3>
                        <p class="business_unit_text" data-i18n="business_unit7_text">Gestión electrónica de accesos, presencia y trazabilidad de personas en entornos corporativos e industriales</p>
                    </div>
                </div>
                <div class="business_unit_card">
                    <div class="business_unit_image_wrapper">   
                        <img src="img/carousel/desinfeccion_uvc.png" alt="Desinfección UV-C" class="business_unit_image">
                        <div class="business_unit_overlay"></div>
                        <div class="business_unit_content business_unit_content_overlay">
                            <h3 class="business_unit_title" data-i18n="business_unit8_title">Sistemas especiales de desinfección - UV-C</h3>
                            <p class="business_unit_text" data-i18n="business_unit8_text">Dispositivos electrónicos para sanitización de aire, superficies y líquidos sin uso de químicos</p>
                            <a href="#" class="business_unit_button" data-i18n="business_unit_button">Ver solución</a>
                        </div>
                    </div>
                    <div class="business_unit_content business_unit_content_default">
                        <h3 class="business_unit_title" data-i18n="business_unit8_title">Sistemas especiales de desinfección - UV-C</h3>
                        <p class="business_unit_text" data-i18n="business_unit8_text">Dispositivos electrónicos para sanitización de aire, superficies y líquidos sin uso de químicos</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END BUSINESS UNITS CAROUSEL -->

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
        <div class="what_we_do_line">
            <div class="what_we_do_line_progress" id="scroll_progress">
                <div class="what_we_do_line_indicator"></div>
            </div>
        </div>
    </div>
</div>
<!-- END WHAT WE DO SECTION -->

<!-- STATS SECTION -->
<div class="stats_section">
    <div class="stats_container">
        <div class="stats_image_wrapper">
            <div class="stats_content">
                <div class="stats_item">
                    <div class="stats_number_wrapper">
                        <span class="stats_number_prefix">+</span>
                        <span class="stats_number" data-target="20" data-suffix=" años">0</span>
                    </div>
                    <div class="stats_description">
                        <span class="stats_icon">
                            <img src="img/icons/star_icon.png" alt="Icon">
                        </span>
                        <span data-i18n="stats_description1">Componentes electrónicos montados por mes junto a ASSISI (en un solo turno)</span>
                    </div>
                </div>
                <div class="stats_item">
                    <div class="stats_number_wrapper">
                        <span class="stats_number_prefix">+</span>
                        <span class="stats_number" data-target="7" data-suffix=" sectores">0</span>
                    </div>
                    <div class="stats_description">
                        <span class="stats_icon">
                            <img src="img/icons/star_icon.png" alt="Icon">
                        </span>
                        <span data-i18n="stats_description2">Industrial, automotriz, aeroespacial, seguridad, salud, comunicaciones y Tecnologías de la Información</span>
                    </div>
                </div>
                <div class="stats_item">
                    <div class="stats_number_wrapper">
                        <span class="stats_number_prefix">+</span>
                        <span class="stats_number" data-target="4" data-suffix=" millones">0</span>
                    </div>
                    <div class="stats_description">
                        <span class="stats_icon">
                            <img src="img/icons/star_icon.png" alt="Icon">
                        </span>
                        <span data-i18n="stats_description3">Experiencia en ingeniería y montaje electrónico</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END STATS SECTION -->

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
  
  // Анимация счетчика для статистики
  const statsSection = document.querySelector('.stats_section');
  const statsNumbers = document.querySelectorAll('.stats_number');
  
  if (statsSection && statsNumbers.length > 0) {
    let hasAnimated = false;
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting && !hasAnimated) {
          hasAnimated = true;
          
          statsNumbers.forEach((numberEl) => {
            const target = parseInt(numberEl.getAttribute('data-target'));
            const duration = 2000; // 2 секунды
            const steps = 60;
            const increment = target / steps;
            const stepDuration = duration / steps;
            let current = 0;
            
            const timer = setInterval(() => {
              current += increment;
              if (current >= target) {
                current = target;
                clearInterval(timer);
              }
              numberEl.textContent = Math.floor(current);
            }, stepDuration);
          });
        }
      });
    }, {
      threshold: 0.3
    });
    
    observer.observe(statsSection);
  }
  
  // Карусель с автоматической прокруткой и hover-управлением (бесконечная замкнутая)
  const carousel = document.querySelector('.business_units_carousel');
  const track = document.querySelector('.business_units_track');
  
  if (carousel && track) {
    let isDown = false;
    let startX;
    let scrollLeft;
    let currentTranslate = 0;
    let cardWidth = 0;
    let gap = 0;
    let singleSetWidth = 0; // Ширина одного полного набора карточек (8 карточек)
    let autoScrollInterval = null;
    let isHovered = false;
    let isCardHovered = false; // Флаг наведения на карточку
    let hoverDirection = 0; // -1 влево, 0 нет, 1 вправо
    let hoverScrollSpeed = 0;
    let animationFrameId = null;
    let autoScrollSpeed = 1.0; // Автоматическая прокрутка
    
    // Вычисляем размеры и клонируем карточки для бесконечной прокрутки
    function calcDimensions() {
      const firstCard = track.querySelector('.business_unit_card:not(.clone)');
      if (!firstCard) return;
      const styles = getComputedStyle(track);
      gap = parseFloat(styles.gap || 0);
      cardWidth = firstCard.getBoundingClientRect().width;
      
      // Вычисляем ширину одного полного набора (8 карточек)
      const totalCards = 8;
      singleSetWidth = (cardWidth + gap) * totalCards;
      
      // Удаляем существующие клоны
      track.querySelectorAll('.business_unit_card.clone').forEach(clone => clone.remove());
      
      // Получаем оригинальные карточки (без клонов)
      const originalCards = Array.from(track.querySelectorAll('.business_unit_card:not(.clone)'));
      
      // Клонируем карточки для бесконечной прокрутки
      // Добавляем клоны в конец (для прокрутки влево)
      const numClones = Math.ceil(carousel.offsetWidth / (cardWidth + gap)) + 2;
      for (let i = 0; i < numClones; i++) {
        const clone = originalCards[i % originalCards.length].cloneNode(true);
        clone.classList.add('clone');
        track.appendChild(clone);
      }
      
      // Добавляем клоны в начало (для прокрутки вправо)
      for (let i = 0; i < numClones; i++) {
        const clone = originalCards[(originalCards.length - 1 - i) % originalCards.length].cloneNode(true);
        clone.classList.add('clone');
        track.insertBefore(clone, track.firstChild);
      }
      
      // Устанавливаем начальную позицию (начинаем с оригинальных карточек)
      currentTranslate = -singleSetWidth;
      setTransform();
    }
    
    calcDimensions();
    window.addEventListener('resize', calcDimensions);
    
    // Функция для обновления позиции
    function setTransform() {
      track.style.transform = `translateX(${currentTranslate}px)`;
    }
    
    // Функция для бесшовной бесконечной прокрутки
    function checkInfiniteLoop() {
      // Если прокрутили слишком далеко влево (за оригинальные карточки), перескакиваем на клоны
      if (currentTranslate <= -singleSetWidth * 2) {
        currentTranslate += singleSetWidth;
        // Отключаем transition для мгновенного перехода
        track.style.transition = 'none';
        setTransform();
        // Принудительно перерисовываем
        track.offsetWidth; // trigger reflow
        // Включаем transition обратно
        track.style.transition = '';
      }
      // Если прокрутили слишком далеко вправо (за оригинальные карточки), перескакиваем на клоны
      else if (currentTranslate > 0) {
        currentTranslate -= singleSetWidth;
        // Отключаем transition для мгновенного перехода
        track.style.transition = 'none';
        setTransform();
        // Принудительно перерисовываем
        track.offsetWidth; // trigger reflow
        // Включаем transition обратно
        track.style.transition = '';
      }
    }
    
    // Автоматическая прокрутка влево
    function startAutoScroll() {
      if (autoScrollInterval) return;
      
      autoScrollInterval = setInterval(() => {
        // Останавливаем прокрутку если наведена мышь на карусель или идет drag
        if (!isHovered && !isDown) {
          currentTranslate -= autoScrollSpeed; // Медленная прокрутка влево
          setTransform();
          checkInfiniteLoop();
        }
      }, 16); // ~60fps
    }
    
    function stopAutoScroll() {
      if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
        autoScrollInterval = null;
      }
    }
    
    // Плавная прокрутка при наведении на края карточек
    function handleHoverScroll() {
      if (isDown) {
        animationFrameId = requestAnimationFrame(handleHoverScroll);
        return;
      }
      
      if (hoverDirection !== 0 && hoverScrollSpeed > 0 && isHovered) {
        currentTranslate += hoverDirection * hoverScrollSpeed;
        setTransform();
        checkInfiniteLoop();
      }
      
      animationFrameId = requestAnimationFrame(handleHoverScroll);
    }
    
    // Запускаем автоматическую прокрутку
    startAutoScroll();
    // Запускаем обработку hover-прокрутки
    handleHoverScroll();
    
    // Получаем все карточки
    const cards = track.querySelectorAll('.business_unit_card');
    
    // Функция для определения крайних видимых карточек
    function getVisibleCards() {
      const carouselRect = carousel.getBoundingClientRect();
      const visibleCards = [];
      
      cards.forEach((card, index) => {
        const cardRect = card.getBoundingClientRect();
        // Проверяем, видна ли карточка в карусели
        if (cardRect.right > carouselRect.left && cardRect.left < carouselRect.right) {
          visibleCards.push({ card, index, left: cardRect.left, right: cardRect.right });
        }
      });
      
      return visibleCards;
    }
    
    // Mouse events для карусели
    carousel.addEventListener('mouseenter', () => {
      isHovered = true;
      stopAutoScroll(); // Останавливаем автоматическую прокрутку при наведении
    });
    
    carousel.addEventListener('mouseleave', () => {
      isHovered = false;
      hoverDirection = 0;
      hoverScrollSpeed = 0;
      
      if (isDown) {
        isDown = false;
        track.classList.remove('dragging');
        carousel.style.cursor = 'grab';
      }
      
      // Запускаем автоматическую прокрутку обратно при уходе мыши
      startAutoScroll();
    });
    
    carousel.addEventListener('mousemove', (e) => {
      if (!isHovered) return;
      
      // Если drag - прокручиваем в сторону движения мыши (ИСПРАВЛЕНО направление)
      if (isDown) {
        e.preventDefault();
        const x = e.pageX - carousel.offsetLeft;
        const walk = (x - startX) * 1.2;
        // ИСПРАВЛЕНО: при листании влево карусель идет влево
        currentTranslate = scrollLeft + walk;
        setTransform();
        checkInfiniteLoop();
        return;
      }
      
      // Определяем позицию мыши относительно карусели
      const carouselRect = carousel.getBoundingClientRect();
      const mouseX = e.clientX;
      const carouselLeft = carouselRect.left;
      const carouselRight = carouselRect.right;
      const carouselWidth = carouselRect.width;
      const edgeZone = carouselWidth * 0.15; // 15% от края карусели для зоны прокрутки
      const relativeX = mouseX - carouselLeft; // Позиция мыши относительно левого края карусели
      
      const visibleCards = getVisibleCards();
      
      if (visibleCards.length === 0) {
        hoverDirection = 0;
        hoverScrollSpeed = 0;
        return;
      }
      
      // Сортируем видимые карточки по позиции слева направо
      const sortedVisibleCards = [...visibleCards].sort((a, b) => a.left - b.left);
      const leftmostCard = sortedVisibleCards[0];
      const rightmostCard = sortedVisibleCards[sortedVisibleCards.length - 1];
      
      // Проверяем, наведена ли мышь на края карусели (включая области между карточками)
      if (relativeX < edgeZone) {
        // Левая зона карусели - прокрутка ВПРАВО
        hoverDirection = 1;
        const distanceFromEdge = relativeX / edgeZone; // 0 до 1
        hoverScrollSpeed = (1 - distanceFromEdge) * 2.5; // Скорость от 0 до 2.5
      } else if (relativeX > carouselWidth - edgeZone) {
        // Правая зона карусели - прокрутка ВЛЕВО
        hoverDirection = -1;
        const distanceFromEdge = (carouselWidth - relativeX) / edgeZone; // 0 до 1
        hoverScrollSpeed = (1 - distanceFromEdge) * 2.5; // Скорость от 0 до 2.5
      } else {
        // Центральная зона - проверяем наведение на карточки
        let hoveredCard = null;
        for (const item of sortedVisibleCards) {
          if (mouseX >= item.left && mouseX <= item.right) {
            hoveredCard = item;
            break;
          }
        }
        
        if (hoveredCard) {
          const cardWidth = hoveredCard.right - hoveredCard.left;
          const cardLeft = hoveredCard.left;
          const relativeCardX = mouseX - cardLeft; // Позиция мыши относительно левого края карточки
          const cardEdgeZone = cardWidth * 0.2; // 20% от края карточки для зоны прокрутки
          
          const isLeftmost = hoveredCard.card === leftmostCard.card;
          const isRightmost = hoveredCard.card === rightmostCard.card;
          
          // Проверяем, наведена ли мышь на края карточки
          if (relativeCardX < cardEdgeZone) {
            // Левая часть карточки - прокрутка ВПРАВО
            hoverDirection = 1;
            const distanceFromEdge = relativeCardX / cardEdgeZone; // 0 до 1
            hoverScrollSpeed = (1 - distanceFromEdge) * 2.5; // Скорость от 0 до 2.5
          } else if (relativeCardX > cardWidth - cardEdgeZone) {
            // Правая часть карточки - прокрутка ВЛЕВО
            hoverDirection = -1;
            const distanceFromEdge = (cardWidth - relativeCardX) / cardEdgeZone; // 0 до 1
            hoverScrollSpeed = (1 - distanceFromEdge) * 2.5; // Скорость от 0 до 2.5
          } else if (isLeftmost) {
            // Левая крайняя карточка в центральной части - прокрутка ВПРАВО
            hoverDirection = 1;
            hoverScrollSpeed = 1.5;
          } else if (isRightmost) {
            // Правая крайняя карточка в центральной части - прокрутка ВЛЕВО
            hoverDirection = -1;
            hoverScrollSpeed = 1.5;
          } else {
            // Центральная часть центральных карточек - нет прокрутки
            hoverDirection = 0;
            hoverScrollSpeed = 0;
          }
        } else {
          // Мышь между карточками в центральной зоне - нет прокрутки
          hoverDirection = 0;
          hoverScrollSpeed = 0;
        }
      }
    });
    
    carousel.addEventListener('mousedown', (e) => {
      isDown = true;
      startX = e.pageX - carousel.offsetLeft;
      scrollLeft = currentTranslate;
      track.classList.add('dragging');
      track.classList.remove('smooth');
      carousel.style.cursor = 'grabbing';
      hoverDirection = 0;
      hoverScrollSpeed = 0;
      stopAutoScroll(); // Останавливаем автоматическую прокрутку при drag
      e.preventDefault();
    });
    
    carousel.addEventListener('mouseup', () => {
      if (isDown) {
        isDown = false;
        track.classList.remove('dragging');
        track.classList.add('smooth');
        carousel.style.cursor = 'grab';
        checkInfiniteLoop();
        // Запускаем автоматическую прокрутку обратно, если мышь не на карусели
        if (!isHovered) {
          startAutoScroll();
        }
      }
    });
    
    // Обработчики для карточек - не останавливаем прокрутку при наведении на крайние карточки
    const isMobile = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    let touchStartCardX = 0;
    let touchStartCardY = 0;
    let hasMovedDuringTouch = false;
    
    cards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        // Не останавливаем прокрутку, если это крайняя карточка
        // Логика прокрутки обрабатывается в mousemove карусели
      });
      
      card.addEventListener('mouseleave', () => {
        // При уходе с карточки останавливаем прокрутку
        // Проверяем, не наведена ли мышь на другую крайнюю карточку
        // Это будет обработано в mousemove карусели
        hoverDirection = 0;
        hoverScrollSpeed = 0;
      });
      
      // Предотвращаем клики на карточках во время drag
      let hasMoved = false;
      card.addEventListener('mousedown', () => {
        hasMoved = false;
      });
      
      card.addEventListener('mousemove', () => {
        if (isDown) {
          hasMoved = true;
        }
      });
      
      card.addEventListener('click', (e) => {
        if (hasMoved) {
          e.preventDefault();
          e.stopPropagation();
        }
      });
      
      // Обработка touch событий для мобильных устройств
      if (isMobile) {
        let cardTouchStartX = 0;
        let cardTouchStartY = 0;
        let cardHasMoved = false;
        
        card.addEventListener('touchstart', (e) => {
          cardTouchStartX = e.touches[0].clientX;
          cardTouchStartY = e.touches[0].clientY;
          cardHasMoved = false;
          // Останавливаем распространение, чтобы карусель не обрабатывала это событие
          e.stopPropagation();
        }, { passive: true });
        
        card.addEventListener('touchmove', (e) => {
          if (e.touches.length > 0) {
            const deltaX = Math.abs(e.touches[0].clientX - cardTouchStartX);
            const deltaY = Math.abs(e.touches[0].clientY - cardTouchStartY);
            // Если движение больше 15px, считаем это drag
            if (deltaX > 15 || deltaY > 15) {
              cardHasMoved = true;
            }
          }
          // Останавливаем распространение, чтобы карусель не обрабатывала это событие
          e.stopPropagation();
        }, { passive: true });
        
        card.addEventListener('touchend', (e) => {
          // Если не было движения (это клик), активируем карточку
          if (!cardHasMoved) {
            e.preventDefault();
            e.stopPropagation();
            
            // Убираем активный класс со всех карточек
            cards.forEach(c => {
              if (c !== card) {
                c.classList.remove('active');
              }
            });
            
            // Переключаем активный класс на текущей карточке
            card.classList.toggle('active');
          }
          cardHasMoved = false;
        }, { passive: false });
      }
    });
    
    // Закрытие активной карточки при клике вне карусели (для мобильных)
    if (isMobile) {
      document.addEventListener('click', (e) => {
        if (!carousel.contains(e.target)) {
          cards.forEach(c => c.classList.remove('active'));
        }
      });
    }
    
    // Touch events для мобильных
    let touchStartX = 0;
    let touchScrollLeft = 0;
    let isTouching = false;
    
    carousel.addEventListener('touchstart', (e) => {
      // Проверяем, не начался ли touch на карточке
      const card = e.target.closest('.business_unit_card');
      if (card) {
        // Не обрабатываем drag карусели, если touch начался на карточке
        // Но не вызываем preventDefault, чтобы click событие могло сработать
        return;
      }
      
      isTouching = true;
      touchStartX = e.touches[0].pageX - carousel.offsetLeft;
      touchScrollLeft = currentTranslate;
      track.classList.add('dragging');
      track.classList.remove('smooth');
      stopAutoScroll(); // Останавливаем автоматическую прокрутку при touch
      e.preventDefault();
    }, { passive: false });
    
    carousel.addEventListener('touchmove', (e) => {
      if (!isTouching) return;
      // Проверяем, не начался ли touch на карточке
      if (e.target.closest('.business_unit_card')) {
        return; // Не обрабатываем, если touch на карточке
      }
      
      const x = e.touches[0].pageX - carousel.offsetLeft;
      const walk = (x - touchStartX) * 1.2;
      // ИСПРАВЛЕНО: при листании влево карусель идет влево
      currentTranslate = touchScrollLeft + walk;
      setTransform();
      checkInfiniteLoop();
      e.preventDefault();
    }, { passive: false });
    
    carousel.addEventListener('touchend', () => {
      if (isTouching) {
        isTouching = false;
        touchStartX = 0;
        track.classList.remove('dragging');
        track.classList.add('smooth');
        checkInfiniteLoop();
        // Запускаем автоматическую прокрутку обратно
        if (!isHovered) {
          startAutoScroll();
        }
      }
    });
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

