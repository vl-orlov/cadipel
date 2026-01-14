<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Cadipel</title>
    <link rel="icon" type="image/png" href="img/icons/logo_icon.png">
    <link rel="shortcut icon" type="image/png" href="img/icons/logo_icon.png">
    <link rel="apple-touch-icon" href="img/icons/logo_icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/i18n.js"></script>
</head>
<body>

<?
$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';

SWITCH ( $page ) {
    case 'fin_tech':                        include "includes/fin_tech.php";                    break;
    case 'soluciones_agro':                 include "includes/soluciones_agro.php";             break;
    case 'ingenieria_desarrollo':           include "includes/ingenieria_desarrollo.php";       break;
    case 'landing':                         include "includes/landing.php";                     break;
    default:                                include "includes/landing.php";                     break;
}
?>

</body>
</html>
