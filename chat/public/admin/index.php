<?php

require_once __DIR__ . '/../../src/admin.php';
admin_session_start();
admin_page_headers();

if (!admin_logged_in()) {
    header('Location: login.php');
    exit;
}

$customPromptText = admin_read_prompt();
$adminName = (string) $_SESSION['cadipel_admin'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <title>CADIPEL - Admin</title>
    <link rel="icon" type="image/png" href="../img/logo_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/brand.css" rel="stylesheet">
</head>
<body id="page-top" class="page_abm_layout">

<div id="wrapper">

    <ul class="navbar-nav bg-white sidebar sidebar-light accordion" id="accordionSidebar">
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            <span class="admin_brand admin_brand_sidebar">CADIPEL</span>
        </a>
        <hr class="sidebar-divider my-0">
        <hr class="sidebar-divider">
        <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <svg class="nav-link-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path fill="currentColor" d="M2 2.75C2 2.336 2.336 2 2.75 2h10.5c.414 0 .75.336.75.75v7.5a.75.75 0 0 1-.75.75H9.06l-2.56 2.56a.75.75 0 0 1-1.28-.53V11H2.75a.75.75 0 0 1-.75-.75v-7.5Z"/>
                    <path fill="currentColor" d="M4.5 5.25h7v1H4.5v-1Zm0 2.5h5v1h-5v-1Z" opacity=".55"/>
                </svg>
                <span>Prompt IA</span>
            </a>
        </li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <nav class="navbar navbar-expand navbar-light bg-primary topbar mb-4 static-top shadow">
                <button type="button" class="cadipel_nav_toggle" id="sidebarToggleTop" aria-label="Menú">
                    <img src="img/icons/menu_icon.svg" class="cadipel_nav_toggle_icon" alt="" aria-hidden="true">
                </button>
                <ul class="navbar-nav ml-auto cadipel_topbar_actions">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item">
                        <span class="nav-link cadipel_topbar_user">
                            <span class="mr-2 d-none d-lg-inline text-white-600 small"><?= htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8') ?></span>
                            <img class="img-profile rounded-circle" src="img/undraw_profile.svg" alt="">
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="cadipel_logout_btn" href="logout.php" aria-label="Salir">
                            <img src="img/icons/logout_icon.svg" class="cadipel_logout_icon" alt="" aria-hidden="true">
                            <span class="cadipel_logout_label">Salir</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="container-fluid abm_page">
                <div class="abm_card card shadow mb-4">
                    <div class="card-header py-3">
                        <h1 class="h5 mb-0">Instrucciones adicionales para el asistente IA</h1>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Este texto se agrega al final del prompt del sistema del asistente (junto con la
                            información base del sitio) en cada conversación. Usalo para ajustar el tono,
                            sumar avisos temporales o corregir algún comportamiento — sin tocar código.
                            El asistente toma los cambios en la respuesta siguiente.
                        </p>
                        <form id="promptForm">
                            <div class="form-group">
                                <textarea id="promptTextarea" class="form-control" rows="14"
                                    maxlength="<?= (int) ADMIN_PROMPT_MAX ?>"
                                    placeholder="Ej: Durante esta semana mencioná que Cadipel participa de la feria X..."
                                ><?= htmlspecialchars($customPromptText, ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>
                            <div id="promptStatus" class="login_message" role="status"></div>
                            <button type="submit" class="login_btn" style="width:auto; padding:10px 28px;">Guardar</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <footer class="sticky-footer bg-primary">
            <div class="container my-auto">
                <div class="copyright text-center text-white my-auto">
                    <span>Powered by: CADIPEL</span>
                </div>
            </div>
        </footer>
        <script src="js/admin.js"></script>
    </div>
</div>

<script src="js/prompt.js"></script>
</body>
</html>
