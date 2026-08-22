<?php

// Copiar este archivo a config.php y completar las claves reales.
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

// Panel de administración (web/admin/) — sin base de datos, un único usuario.
// Generar el hash con: php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', '');
