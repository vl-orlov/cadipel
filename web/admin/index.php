<?php

session_start();
date_default_timezone_set('America/Argentina/Buenos_Aires');
error_reporting(E_ALL);

if (!isset($_SESSION['cadipel_admin'])) {
    header('Location: login.php');
    exit;
}

$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
if ($page === 'logout') {
    include 'includes/logout.php';
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
<body id="page-top" class="page_abm_layout">

<div id="wrapper">

    <?php include 'includes/nav.php'; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">

            <?php include 'includes/topbar.php'; ?>

            <?php
            switch ($page) {
                case 'prompt':
                default:
                    include 'includes/prompt.php';
                    break;
            }
            ?>

        </div>

        <?php include 'includes/footer.php'; ?>

    </div>
</div>

</body>
</html>
