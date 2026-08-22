# cadipel

### Команда для запуска локально
```
cd web
php -d short_open_tag=On -S localhost:8888
```

### Asistente IA

El sitio incluye un asistente flotante (botón + panel de chat texto/voz, con avatar animado)
que responde preguntas sobre Cadipel usando Gemini/OpenAI, con voz vía Azure/OpenAI/Google TTS.

Para activarlo con claves reales:
```
cp web/api/config.example.php web/api/config.php
```
Completar en `config.php` las que quieras usar (no hace falta tenerlas todas — cada proveedor
tiene fallback al siguiente; sin ninguna clave el asistente sigue funcionando en la interfaz
pero responde con un error de "servicio no disponible"):
- `GEMINI_KEY` — chat + transcripción de voz (proveedor principal)
- `OPENAI_KEY` — fallback de chat/transcripción y fallback de voz (TTS)
- `AZURE_TTS_KEY` / `AZURE_TTS_REGION` — voz en español (proveedor principal de TTS)
- `GOOGLE_TTS_KEY` — último fallback de voz

`config.php` está en `.gitignore` — nunca subir claves reales al repositorio.

### Admin

Panel de administración en `/admin/` (usuario único, sin base de datos). En `config.php`
completar:
```
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', ''); // generar con: php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
```
Desde la pestaña "Prompt IA" se edita el texto de `web/api/custom_prompt.txt` (tampoco versionado),
que se agrega al final del prompt del sistema del asistente.