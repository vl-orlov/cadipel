<?php

/**
 * Incluir al principio de cualquier includes/*.php que renderice una pestaña del panel:
 * evita que el archivo se sirva directamente (sin pasar por index.php) sin sesión activa.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cadipel_admin'])) {
    header('Location: ../login.php');
    exit;
}
