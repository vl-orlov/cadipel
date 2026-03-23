<?php include __DIR__ . '/site_header.php'; ?>
<div class="hero_solucion hero_nosotros">
    <div class="hero_solucion_background">
        <img src="img/casos_de_exito/casos_de_exito0.png" alt="" class="hero_solucion_bg_image hero_nosotros_bg_image">
    </div>
    <div class="hero_solucion_content hero_nosotros_content">
        <h1 class="hero_solucion_title hero_nosotros_title" data-i18n="casos_hero_title">Casos de éxito</h1>
        <a onclick="window.location.href='?page=landing'" class="hero_nosotros_back" data-i18n="casos_hero_back">Volver</a>
    </div>
</div>

<!-- CONTENT -->
<?php
$success_case_items = [
    ['grupo_agroempresa_argentina.png', 'casos_logo_1', 'Grupo Agroempresa Argentina'],
    ['aeropuerto_argentino_2000.png', 'casos_logo_2', 'Aeropuerto Argentino 2000'],
    ['grupo_disney.png', 'casos_logo_3', 'Grupo Disney'],
    ['bridgestone.png', 'casos_logo_4', 'Bridgestone'],
    ['firestone.png', 'casos_logo_5', 'Firestone'],
    ['alfa_romeo_argentina.png', 'casos_logo_6', 'Alfa Romeo Argentina'],
    ['champion_technologies.png', 'casos_logo_7', 'Champion Technologies'],
    ['coca_cola_company_sa.png', 'casos_logo_8', 'Coca Cola Company S.A.'],
    ['electrolux.png', 'casos_logo_9', 'Electrolux'],
    ['nestle_purina.png', 'casos_logo_10', 'Nestle Purina'],
    ['automoviles_citroen.png', 'casos_logo_11', 'Automoviles CITROEN'],
    ['swissport.png', 'casos_logo_12', 'Swissport'],
    ['software_america.png', 'casos_logo_13', 'Software America'],
    ['genesys_informatica.png', 'casos_logo_14', 'Genesys Informatica'],
    ['roqueta_prat_hoteles.png', 'casos_logo_15', 'Roqueta Prat Hoteles'],
    ['alvear_palace_hotel.png', 'casos_logo_16', 'Alvear Palace Hotel'],
    ['hotel_bauen_bs_as.png', 'casos_logo_17', 'Hotel BAUEN Bs. As.'],
    ['bingo_royal.png', 'casos_logo_18', 'Bingo Royal'],
    ['codere_group.png', 'casos_logo_19', 'Codere Group'],
    ['boulogne_golf_club.png', 'casos_logo_20', 'Boulogne Golf Club'],
    ['banco_columbia.png', 'casos_logo_21', 'Banco Columbia'],
    ['banco_los_andes.png', 'casos_logo_22', 'Banco Los Andes'],
    ['canal_trece.png', 'casos_logo_23', 'Canal Trece'],
    ['sony_music.png', 'casos_logo_24', 'Sony Music'],
    ['siemens.png', 'casos_logo_25', 'SIEMENS'],
    ['westronic.png', 'casos_logo_26', 'Westronic'],
    ['gruppo_campari.png', 'casos_logo_27', 'Gruppo Campari'],
    ['honda_motor_argentina_sa.png', 'casos_logo_28', 'Honda Motor Argentina S.A.'],
    ['ernst_young.png', 'casos_logo_29', 'Ernst & Young'],
    ['capital_markets_argentina.png', 'casos_logo_30', 'Capital Markets Argentina'],
    ['isikawa_electronica_sa.png', 'casos_logo_31', 'Isikawa Electronica S.A.'],
    ['repsol_ypf.png', 'casos_logo_32', 'Repsol YPF'],
    ['banco_saenz.png', 'casos_logo_33', 'Banco Sáenz'],
    ['bapro_medios_de_pagos_sa.png', 'casos_logo_34', 'BaPro Medios de Pagos S.A.'],
    ['bbva_consolidar.png', 'casos_logo_35', 'BBVA Consolidar'],
    ['caja_popular_de_ahorros_de_tucuman.png', 'casos_logo_36', 'Caja Popular de Ahorros de Tucumán'],
    ['Lorfin.png', 'casos_logo_37', 'Lorfin'],
    ['banco_bbva_prevision.png', 'casos_logo_38', 'Banco BBVA Previsión'],
    ['universidad_nacional_del_litoral.png', 'casos_logo_39', 'Universidad Nacional del Litoral'],
    ['ejercito_argentino.png', 'casos_logo_40', 'Ejército Argentino']
];
?>
<section class="success_logos_section" aria-label="Casos de exito">
    <div class="success_logos_inner">
        <div class="success_logos_grid">
            <?php foreach ($success_case_items as [$logo_file, $logo_key, $logo_name]): ?>
            <article class="success_logo_item">
                <div class="success_logo_image_wrap">
                    <img src="img/casos_de_exito/<?php echo rawurlencode($logo_file); ?>" alt="<?php echo htmlspecialchars($logo_name, ENT_QUOTES, 'UTF-8'); ?>" class="success_logo_image" loading="lazy">
                </div>
                <p class="success_logo_name" data-i18n="<?php echo htmlspecialchars($logo_key, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($logo_name, ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
            <?php endforeach; ?>
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
initLang('casos_de_exito');
</script>