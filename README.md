# cadipel

### Команда для запуска локально
```
cd web
php -d short_open_tag=On -S localhost:8888
```
```
cd chat/public
php -d short_open_tag=On -S localhost:8899
```
### Asistente IA (chat en otro dominio)

El botón flotante del landing (`.cadipel_assistant_float`) abre en una pestaña nueva el chat a pantalla
completa: **https://cadipel.pribridge.pro** (código en `chat/`, se despliega en el VPS de pribridge; el
landing sigue en el hosting de www.cadipel.com.ar por FTP). El landing ya no tiene claves de IA.

- Local: `cd chat/public && php -S localhost:8899` con un `chat/config.php` que tenga `define('APP_ENV','dev');`
- Configuración, claves, nginx y pasos de despliegue: ver [`chat/README.md`](chat/README.md).

### Admin

Panel en `https://cadipel.pribridge.pro/admin/` (código en `chat/public/admin/`). Un usuario, sin base de
datos. En `chat/config.php`:
```
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD_HASH', ''); // php -r "echo password_hash('tu_clave', PASSWORD_DEFAULT);"
```
El texto se guarda en `chat/var/custom_prompt.txt` y el asistente lo usa en la respuesta siguiente.
El landing ya no tiene `/admin/` (la carpeta `admin/` se borra del hosting de www.cadipel.com.ar).
