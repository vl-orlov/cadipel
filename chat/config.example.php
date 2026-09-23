<?php

// Copiar este archivo a config.php (junto a este mismo archivo, FUERA de public/) y completar.
// config.php está en .gitignore — nunca subir claves reales al repositorio.

// Gemini (proveedor principal para chat y transcripción de voz)
define('GEMINI_KEY', '');

// OpenAI (fallback para chat/transcripción, y fallback para TTS)
define('OPENAI_KEY', '');

// Azure Cognitive Services — Text to Speech (proveedor principal de voz, español)
define('AZURE_TTS_KEY', '');
define('AZURE_TTS_REGION', '');

// Google Cloud Text-to-Speech (último fallback de voz)
define('GOOGLE_TTS_KEY', '');

// Panel /admin/ del chat (un usuario, sin base de datos).
// Generar el hash con: php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', '');

// Dominios desde los que se aceptan requests (comparación con Origin/Referer), separados por coma.
define('ALLOWED_HOSTS', 'cadipel.pribridge.pro');

// 'dev' además acepta localhost / 127.0.0.1 (para desarrollo local).
define('APP_ENV', 'prod');

// true solo si delante hay un proxy de confianza (p. ej. Cloudflare): la IP del visitante
// se lee de CF-Connecting-IP o del primer X-Forwarded-For. En false se usa la IP de la
// conexión. No activarlo si el origen es accesible directo: esos headers se pueden falsificar.
define('TRUST_PROXY', false);
