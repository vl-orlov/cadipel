<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Cadipel</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/i18n.js"></script>
</head>
<body>

<?
$page = isset($_REQUEST['page']) ? htmlspecialchars($_REQUEST['page']) : '';

SWITCH ( $page ) {
    case 'landing':              include "includes/landing.php";                break;
    default:                     include "includes/landing.php";                break;
}
?>

</body>
</html>
