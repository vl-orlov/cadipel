Пакет для деплоя www.cadipel.com.ar (папка web/ в репозитории), коммит 016fbba "подправил сайт".
Собран и проверен: 24 файла побайтово сверены с рабочей копией, все .php прошли php -l,
.js — node --check, .json — валидация json.load. Ничего вручную не редактировалось.

ВАЖНО: деплоить чат (cadipel.pribridge.pro) СНАЧАЛА, этот пакет — ПОСЛЕ. Иначе флоат-кнопка
временно будет вести в ещё не поднятый чат.

── 1. Залить (24 файла, пути отсюда = пути в web/ на сервере) ──────────────────
Сами файлы лежат в этом репозитории в web/ (это не отдельная папка-пакет — просто список,
какие 24 файла из web/ нужны для этого коммита). Указать FTP-клиенту локальный путь на web/
в репозитории и залить ровно эти файлы, сохраняя структуру подпапок:

  admin/includes/login_check.php
  admin/includes/prompt.php
  admin/js/login.js
  api/.htaccess
  api/bootstrap.php
  api/prompt_public.php
  css/style.css
  img/icons/chat_avatar.png
  includes/landing.php
  includes/site_header.php
  index.php
  js/i18n.js
  lang/casos_de_exito/en.json
  lang/casos_de_exito/es.json
  lang/companias_asociadas/en.json
  lang/companias_asociadas/es.json
  lang/landing/en.json
  lang/landing/es.json
  lang/lo_que_hacemos/en.json
  lang/lo_que_hacemos/es.json
  lang/nav/en.json      ← новый файл, новая папка lang/nav/
  lang/nav/es.json      ← новый файл
  lang/nosotros/en.json
  lang/nosotros/es.json

── 2. Удалить на сервере (40 файлов, список тоже в DELETE_ON_SERVER.txt) ────────
Это устаревшие файлы старого ассистента (панель в лендинге) и его иконок — на сервере их
физически не будет в этом пакете, удалить нужно руками через FTP-клиент. После удаления всех
файлов папки img/assistant_avatar/ и lang/assistant/ останутся пустыми — можно удалить и их
целиком.

── 3. Поправить вручную api/config.php НА СЕРВЕРЕ (не заливается, этого файла в пакете нет) ──
Добавить строку (взять PROMPT_SYNC_TOKEN из chat/config.php на VPS pribridge — значение
должно быть идентичным в обоих местах):

  define('PROMPT_SYNC_TOKEN', '...');

Удалить из этого же файла (больше не используются на лендинге, ключи теперь только в
chat/config.php на VPS):
  GEMINI_KEY, OPENAI_KEY, AZURE_TTS_KEY, AZURE_TTS_REGION, GOOGLE_TTS_KEY

── 4. Проверить после деплоя ────────────────────────────────────────────────────
- Открыть www.cadipel.com.ar — навигация (Nosotros / Compañías asociadas / Lo que hacemos /
  Casos de éxito) показывает текст, а не пустые ссылки (это и есть риск неполного деплоя,
  из-за которого собран весь коммит целиком, а не только файлы ассистента).
- Переключить на EN — те же пункты навигации переводятся.
- Флоат-кнопка справа снизу открывает https://cadipel.pribridge.pro в новой вкладке.
- /admin/ — вход и сохранение "Prompt IA" по-прежнему работают.
