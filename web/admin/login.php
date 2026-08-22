<?php
session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(E_ALL);

if (isset($_SESSION['cadipel_admin'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>CADIPEL - Admin</title>
    <link rel="icon" type="image/png" href="../img/icons/logo_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/brand.css" rel="stylesheet">
</head>
<body id="page-top" class="login_page">

<div class="login_wrapper">
    <div class="login_card">
        <div class="login_header">
            <h1 class="admin_brand admin_brand_login">CADIPEL</h1>
            <p class="login_subtitle">Panel de administración</p>
        </div>
        <form class="login_form" onsubmit="loginAdm(); return false;">
            <div class="login_field">
                <img src="img/icons/user_icon.svg" class="login_field_icon" alt="" aria-hidden="true">
                <input type="text" class="login_input" id="inputLogin" placeholder="Usuario" autocomplete="username">
            </div>
            <div class="login_field">
                <img src="img/icons/lock_icon.svg" class="login_field_icon" alt="" aria-hidden="true">
                <input type="password" class="login_input" id="inputPassword" placeholder="Contraseña" autocomplete="current-password">
            </div>
            <div id="mensaje" class="login_message" role="alert"></div>
            <button type="submit" class="login_btn">Iniciar sesión</button>
        </form>
    </div>
</div>

<script src="js/login.js"></script>
</body>
</html>
