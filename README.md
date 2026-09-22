# cadipel

### Команда для запуска локально
```
cd web
php -d short_open_tag=On -S localhost:8888
```

### Asistente IA (chat en otro dominio)

El botón flotante del landing (`.cadipel_assistant_float`) abre en una pestaña nueva el chat a pantalla
completa: **https://cadipel.pribridge.pro** (código en `chat/`, se despliega en el VPS de pribridge; el
landing sigue en el hosting de www.cadipel.com.ar por FTP). El landing ya no tiene claves de IA.

- Local: `cd chat/public && php -S localhost:8899` con un `chat/config.php` que tenga `define('APP_ENV','dev');`
- Configuración, claves, nginx y pasos de despliegue: ver [`chat/README.md`](chat/README.md).

### Admin

Panel de administración en `/admin/` del landing (usuario único, sin base de datos). En `web/api/config.php`
completar:
```
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', ''); // generar con: php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
define('PROMPT_SYNC_TOKEN', '');   // el mismo valor que en chat/config.php
```
Desde la pestaña "Prompt IA" se edita `web/api/custom_prompt.txt` (no versionado). El chat lo lee por
`web/api/prompt_public.php` (protegido con `PROMPT_SYNC_TOKEN`), lo cachea 2 minutos y lo agrega al final
del prompt del sistema del asistente.
