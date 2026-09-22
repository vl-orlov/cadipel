<?php

// Copiar este archivo a config.php y completar.
// config.php está en .gitignore — nunca subir claves reales al repositorio.

// Las claves de IA (Gemini/OpenAI/Azure/Google) ya NO viven acá: el asistente corre en
// https://cadipel.pribridge.pro (ver chat/config.example.php).

// Panel de administración (web/admin/) — sin base de datos, un único usuario.
// Generar el hash con: php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', '');

// Token compartido con el chat (PROMPT_SYNC_TOKEN en chat/config.php) para que pueda leer las
// instrucciones del asistente guardadas en el admin. Debe ser idéntico en ambos lados.
// Generar con: php -r "echo bin2hex(random_bytes(32));"
define('PROMPT_SYNC_TOKEN', '');
