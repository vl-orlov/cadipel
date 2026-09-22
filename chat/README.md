# Chat del asistente Cadipel — cadipel.pribridge.pro

Chat a pantalla completa (PHP + JS vanilla, sin BD). Se abre desde el botón flotante del landing
(www.cadipel.com.ar). Es un servicio autónomo: tiene sus propias claves de IA, su rate limit y su copia
de la base de conocimiento; solo consulta al sitio principal para traer las instrucciones extra del admin.

## Estructura
```
chat/
  public/            ← docroot de nginx (único directorio accesible por web)
    index.php  css/  js/  img/  lang/chat/{es,en}.json
    api/ai_stream.php  api/tts.php  api/ai_transcribe.php
  src/               ← fuera del docroot: bootstrap (config, origen, rate limit), prompt (base + sync)
  var/               ← fuera del docroot: caché del prompt y contadores de rate limit (escribible)
  config.php         ← claves (NO versionado; copiar de config.example.php)
  deploy/nginx.conf  ← script listo para correr en el VPS (crea el vhost + certbot)
```

## Requisitos
PHP ≥ 8.0 con extensiones `curl` y `mbstring`; nginx + php-fpm. Si hay un proxy/CDN delante (p. ej. Cloudflare),
el rate limit ve la IP del proxy: habría que leer la IP real desde `X-Forwarded-For`/`CF-Connecting-IP`.

## Despliegue (VPS 31.97.92.144, el mismo de pribridge.pro/assisi-pribridge/garcia-baillos — ver
`~/projects/cadipel/vps/portero-hosting-cfg/`, mismo patrón que `cfg_assisi-pribridge.txt`)

Estructura en el servidor (calca la del repo — `public/`, `src/`, `var/`, `config.php` son hermanos):
```
/var/www/cadipel.pribridge.pro/
├── public/     ← contenido de chat/public/ (docroot de nginx)
├── src/        ← contenido de chat/src/ (fuera del docroot)
├── var/        ← creado en el servidor, vacío al inicio (caché del prompt, rate limit)
└── config.php  ← se crea una vez a mano; los redeploys nunca lo tocan
```

**1. DNS** — nada que hacer: `pribridge.pro` ya tiene wildcard (`*` → 31.97.92.144), `cadipel.pribridge.pro`
ya resuelve a este VPS.

**2. Carpetas y nginx** (por SSH, una sola vez):
```bash
ssh root@31.97.92.144
mkdir -p /var/www/cadipel.pribridge.pro/{public,src,var}
```
Correr `chat/deploy/nginx.conf` en el servidor (crea el vhost, activa el sitio y corre certbot —
certbot reescribe el archivo para agregar HTTPS, no hace falta tocar nada más).

**3. `config.php`** (una sola vez, a mano — no se sube por deploy):
```bash
nano /var/www/cadipel.pribridge.pro/config.php
```
Copiar el contenido de `chat/config.example.php` y completar `GEMINI_KEY`, `OPENAI_KEY`,
`AZURE_TTS_KEY`/`AZURE_TTS_REGION`, `GOOGLE_TTS_KEY` (las que uses) y `PROMPT_SYNC_TOKEN`
(`php -r "echo bin2hex(random_bytes(32));"`, mismo valor que en `web/api/config.php` del landing).
Dejar `APP_ENV` en `'prod'` y `ALLOWED_HOSTS` en `cadipel.pribridge.pro`.

**4. FTP** (`cadipelftp`, igual que `assisipribridgeftp` — ver `cfg_assisi-pribridge.txt` §5; vsftpd
ya está instalado y configurado en este VPS, no hace falta instalarlo):
```bash
useradd -m cadipelftp -d /var/www/cadipel.pribridge.pro -s /bin/bash
echo "cadipelftp:ITxa6s5j5c3O7KXX" | chpasswd

echo "local_root=/var/www/cadipel.pribridge.pro" | tee /etc/vsftpd.user_conf/cadipelftp
echo "allow_writeable_chroot=YES" | tee -a /etc/vsftpd.user_conf/cadipelftp

usermod -aG www-data cadipelftp
chown -R cadipelftp:www-data /var/www/cadipel.pribridge.pro
find /var/www/cadipel.pribridge.pro -type d -exec chmod 2775 {} \;
find /var/www/cadipel.pribridge.pro -type f -exec chmod 664 {} \;

systemctl restart vsftpd
```
`chmod 2775` en los directorios mantiene el bit setgid: los archivos que crea `var/` en tiempo de
ejecución (caché del prompt, contadores de rate limit) quedan con grupo `www-data`, así php-fpm
(que corre como `www-data`) puede escribirlos sin depender de quién los creó.

**5. Deploy / redeploy** (por SSH con `rsync`, más simple que FTP para el día a día — reemplaza el
`git clone`/`git pull` que se mencionaba antes; el repo no hace falta clonarlo en el servidor):
```bash
rsync -avz --delete chat/public/ root@31.97.92.144:/var/www/cadipel.pribridge.pro/public/
rsync -avz --delete chat/src/ root@31.97.92.144:/var/www/cadipel.pribridge.pro/src/
ssh root@31.97.92.144 'chown -R cadipelftp:www-data /var/www/cadipel.pribridge.pro/public /var/www/cadipel.pribridge.pro/src
  find /var/www/cadipel.pribridge.pro/public /var/www/cadipel.pribridge.pro/src -type d -exec chmod 2775 {} \;
  find /var/www/cadipel.pribridge.pro/public /var/www/cadipel.pribridge.pro/src -type f -exec chmod 664 {} \;'
```
`--delete` en `public/`/`src/` es seguro porque `config.php` y `var/` viven un nivel arriba, fuera de
esos dos directorios — nunca se tocan en un redeploy.

**6. Probar:** abrir https://cadipel.pribridge.pro y escribir un mensaje.

## Landing (FTP a www.cadipel.com.ar, carpeta `web/`)
Subir/reemplazar: `index.php`, `css/style.css`, `js/i18n.js`, `api/bootstrap.php`, `api/prompt_public.php`,
`api/.htaccess`, `admin/includes/prompt.php`, `admin/includes/login_check.php`, `admin/js/login.js`,
`css/avatar.css`, `js/assistant-avatar.js`, `img/assistant_avatar/` (los mismos archivos que en el chat, sin el retrato viejo).
En `api/config.php` **del servidor**: agregar `define('PROMPT_SYNC_TOKEN', '<mismo token que en chat/config.php>');`
y **borrar** las claves de IA (GEMINI_KEY, OPENAI_KEY, AZURE_*, GOOGLE_TTS_KEY): ya no se usan ahí.

Borrar del servidor (obsoletos): `api/ai_stream.php`, `api/ai_transcribe.php`, `api/tts.php`, `api/cadipel_prompt.php`,
`js/assistant*.js` (assistant, -avatar, -chat, -lipsync, -mic-feedback, -tts, -voice, -voice-hold), `css/assistant.css`,
`lang/assistant/`, `img/assistant_avatar/`, e iconos `img/icons/{camia_send,camia_text,camia_voice,eliminar,mic_arrow_up,mic_close,mic_lock,microphone_icon,volume_mute_icon}.svg`.

Orden recomendado: primero el chat (para que el enlace ya funcione), después el landing.

## Protecciones
- Rate limit por IP (archivos en `var/ratelimit/`): chat 20/min y 200/día; TTS y dictado 7/min y 70/día → HTTP 429.
  Si `var/` no es escribible, esos endpoints responden 503 en lugar de quedar sin límite.
  Detrás de Cloudflare u otro proxy, `TRUST_PROXY` en `config.php` (si no, todos comparten la IP del proxy).
- Historial máximo 20 mensajes, 2000 caracteres por mensaje, texto TTS máx. 500 caracteres, audio máx. ~6 MB.
- Solo se aceptan requests con `Origin`/`Referer` de `ALLOWED_HOSTS` (filtra uso accidental; el rate limit frena abuso).
- `noindex` en cabecera y meta.

## Prompt
La base de conocimiento vive en `src/prompt.php` (versionada). Las instrucciones extra se editan en
`https://www.cadipel.com.ar/admin` (pestaña "Prompt IA") y llegan acá en ≤ 2 minutos
(`PROMPT_CACHE_TTL`); si el sitio principal no responde se usa la última copia.
