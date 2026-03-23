<?php include __DIR__ . '/site_header.php'; ?>
<div class="hero_solucion hero_nosotros">
    <div class="hero_solucion_background">
        <img src="img/companias_asociadas/companias_asociadas0.png" alt="" class="hero_solucion_bg_image hero_nosotros_bg_image">
    </div>
    <div class="hero_solucion_content hero_nosotros_content">
        <h1 class="hero_solucion_title hero_nosotros_title" data-i18n="companias_hero_title">Compañías e Instituciones asociadas</h1>
        <a onclick="window.location.href='?page=landing'" class="hero_nosotros_back" data-i18n="companias_hero_back">Volver</a>
    </div>
</div>

<!--  CONTENT -->
<section class="partners_section" aria-label="Compañías asociadas">
    <div class="partners_inner">
        <div class="partners_row">
            <div class="partners_text">
                <h2 class="partners_title" data-i18n="companias_assisi_title">ASSISI S.R.L.</h2>
                <p class="partners_paragraph" data-i18n="companias_assisi_p1">Es una empresa dedicada al desarrollo y fabricación de prototipos electrónicos y al montaje, ensamble y soldado de placas electrónicas en grandes volúmenes productivos dentro de su propia planta robotizada.</p>
                <p class="partners_paragraph" data-i18n="companias_assisi_p2">Nuestros más de 26 años de experiencia, unidos a una mejora constante de los procesos y la actualización tecnológica, nos posicionan como una empresa líder en el mercado Latinoamericano.</p>
            </div>
            <div class="partners_media">
                <img src="img/companias_asociadas/companias_asociadas1.png" alt="" class="partners_img" loading="lazy">
            </div>
        </div>
        <div class="partners_row partners_row_reverse">
            <div class="partners_media">
                <img src="img/companias_asociadas/companias_asociadas2.png" alt="" class="partners_img" loading="lazy">
            </div>
            <div class="partners_text">
                <h2 class="partners_title" data-i18n="companias_alu_title">ALU-Invent Argentina, ALU-Invent Rusia, ALU-Invent Canadá</h2>
                <p class="partners_paragraph" data-i18n="companias_alu_p1">Es una empresa de ingeniería electrónica dedicada al diseño y desarrollo de Hardware Electrónico, Sistemas Embebidos y diseño de Software Ejecutable con interfaces de usuario / servidor localizadas en tres países.</p>
                <p class="partners_paragraph" data-i18n="companias_alu_p2">Nuestros profesionales desarrollan para mercados internacionales, productos para varias industrias incluyendo: industria aeronáutica, industria bancaria, automotriz, agrícola, ganadera, de entretenimiento, automatizaciones en procesos de líneas industriales, etc.</p>
                <p class="partners_paragraph" data-i18n="companias_alu_p3">Nuestra labor y experiencia de más de 30 años, comprende todo el ciclo de diseño y desarrollo de equipos electrónicos desde la idea original al producto final, dando soporte desde la fase de especificaciones hasta la entrega del producto llave en mano.</p>
            </div>
        </div>
        <div class="partners_row">
            <div class="partners_text">
                <h2 class="partners_title" data-i18n="companias_techred_title">Tech-RED España, Tech-RED Rusia</h2>
                <p class="partners_paragraph" data-i18n="companias_techred_p1">Es una empresa con experiencia de más de 18 años de desarrollo de Hardware y Software en el área financiera, es creador de muchas tecnologías propias y novedosas en el mundo de los sistemas bancarios.</p>
                <p class="partners_paragraph" data-i18n="companias_techred_p2">Opera en los mercados de España y Rusia.</p>
            </div>
            <div class="partners_media">
                <img src="img/companias_asociadas/companias_asociadas3.png" alt="" class="partners_img" loading="lazy">
            </div>
        </div>
        <div class="partners_row partners_row_reverse">
            <div class="partners_media">
                <img src="img/companias_asociadas/companias_asociadas4.png" alt="" class="partners_img" loading="lazy">
            </div>
            <div class="partners_text">
                <h2 class="partners_title" data-i18n="companias_advbio_title">Advanced Biometric Technology</h2>
                <p class="partners_paragraph" data-i18n="companias_advbio_p1">Es una empresa de ingeniería electrónica dedicada al diseño, desarrollo y fabricación de equipos electrónicos y software en el rubro de sistemas de seguridad y biometría humana.</p>
                <p class="partners_paragraph" data-i18n="companias_advbio_p2">Durante más de 20 años, este laboratorio en biometría, desarrolló muchas soluciones y equipamientos en el rubro de la seguridad biométrica.</p>
                <p class="partners_paragraph" data-i18n="companias_advbio_p3">Los algoritmos de reconocimiento de personas por parámetros biométricos hoy siguen en la cima de los algoritmos más veloces del mundo.</p>
                <p class="partners_paragraph" data-i18n="companias_advbio_p4">Nuestras matemáticas son usadas por empresas mundiales número uno, como SIEMENS, para el desarrollo de sus productos.</p>
            </div>
        </div>
        <div class="partners_row">
            <div class="partners_text">
                <h2 class="partners_title" data-i18n="companias_lpun_title">LPUN - Laboratorio Politécnico de la Universidad de Novosibirsk</h2>
                <p class="partners_paragraph" data-i18n="companias_lpun_p1">En sus 66 años de antigüedad, la Universidad de Novosibirsk formó más de 90 mil profesionales en diferentes rubros científicos.</p>
                <p class="partners_paragraph" data-i18n="companias_lpun_p2">Dentro de ella se encuentran cátedras y laboratorios especializados en ciencias fundamentales de matemáticas, física, química y también de desarrollo en muchos rubros técnicos incluyendo la electrónica, software, misilística y energía nuclear entre otras.</p>
            </div>
            <div class="partners_media">
                <img src="img/companias_asociadas/companias_asociadas5.png" alt="" class="partners_img" loading="lazy">
            </div>
        </div>
    </div>
</section>
<!-- END  CONTENT -->

<? 
include "includes/landing_footer.php";
?>

<script>
function toggleLangMenu() {
  const menu = document.getElementById('home_lang_menu');
  menu.classList.toggle('hidden');
}
initLang('companias_asociadas');
</script>