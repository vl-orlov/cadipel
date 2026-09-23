#!/usr/bin/env bash
# Vhost de cadipel.pribridge.pro (instalación desde cero). Correr en el VPS como root.
# Pasa a PHP solo index.php, api/*.php y admin/*.php (+ admin/api/*.php); cualquier otro .php → 404.
# fastcgi_buffering off en /api/: ai_stream.php responde en streaming (SSE).
set -euo pipefail

tee /etc/nginx/sites-available/cadipel.pribridge.pro > /dev/null <<'EOF'
server {
    listen 80;
    server_name cadipel.pribridge.pro;

    root /var/www/cadipel.pribridge.pro/public;
    index index.php;

    client_max_body_size 8m;   # audio de dictado (base64) hasta ~6 MB

    add_header X-Robots-Tag "noindex, nofollow" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location ~ /\. {
        deny all;
    }

    location = /index.php {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME /var/www/cadipel.pribridge.pro/public/index.php;
        include fastcgi_params;
    }

    location ~ ^/api/[a-z_]+\.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffering off;
        fastcgi_read_timeout 90s;
        gzip off;
    }

    location ~ ^/admin/(api/)?[a-z_]+\.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ \.php$ {
        return 404;
    }

    location / {
        try_files $uri $uri/ =404;
    }

    location ~* \.(css|js|png|svg|jpg|json)$ {
        expires 1h;
    }
}
EOF

ln -sf /etc/nginx/sites-available/cadipel.pribridge.pro /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
certbot --nginx -d cadipel.pribridge.pro
nginx -t && systemctl reload nginx
