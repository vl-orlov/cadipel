<?php

require_once __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_error(405, 'Method not allowed');
}

function cadipel_detect_tts_language(string $text, string $uiLang = ''): string
{
    if (preg_match('/\b(the|and|you|hello|please|contact|about|company)\b/i', $text)) {
        return 'en-US';
    }
    if (strtolower(trim($uiLang)) === 'en') {
        return 'en-US';
    }
    return 'es-ES';
}

function cadipel_voice_name(string $langCode, string $gender = 'f'): string
{
    $voices = [
        'es-ES' => ['f' => 'es-ES-Neural2-A', 'm' => 'es-ES-Neural2-B'],
        'en-US' => ['f' => 'en-US-Neural2-F', 'm' => 'en-US-Neural2-D'],
    ];
    return $voices[$langCode][$gender] ?? $voices['es-ES']['f'];
}

/** Plain text for short lines; SSML with pauses after sentences for longer speech. */
function cadipel_build_speech_input(string $text): array
{
    if (mb_strlen($text) < 100) {
        return ['text' => $text];
    }
    $escaped    = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    $withBreaks = preg_replace('/([.!?…])\s+/u', '$1<break time="280ms"/> ', $escaped) ?? $escaped;
    return ['ssml' => '<speak>' . $withBreaks . '</speak>'];
}

// ---- Azure TTS (primary, Spanish) ----
function cadipel_synthesize_azure(string $text, string $gender, float $rate): ?string
{
    if (AZURE_TTS_KEY === '' || AZURE_TTS_REGION === '') {
        return null;
    }
    $voice   = $gender === 'm' ? 'es-AR-TomasNeural' : 'es-AR-ElenaNeural';
    $escaped = htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    if (mb_strlen($text) >= 100) {
        $escaped = preg_replace('/([.!?…])\s+/u', '$1<break time="280ms"/> ', $escaped) ?? $escaped;
    }

    $ratePercent = round(($rate - 1.0) * 100);
    $rateStr     = ($ratePercent >= 0 ? '+' : '') . $ratePercent . '%';

    $ssml = sprintf(
        '<speak version="1.0" xml:lang="es-AR"><voice name="%s"><prosody rate="%s">%s</prosody></voice></speak>',
        $voice,
        $rateStr,
        $escaped,
    );

    $ch = curl_init("https://" . AZURE_TTS_REGION . ".tts.speech.microsoft.com/cognitiveservices/v1");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $ssml,
        CURLOPT_HTTPHEADER     => [
            'Ocp-Apim-Subscription-Key: ' . AZURE_TTS_KEY,
            'Content-Type: application/ssml+xml',
            'X-Microsoft-OutputFormat: audio-16khz-128kbitrate-mono-mp3',
        ],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr !== '' || $httpCode !== 200) {
        return null;
    }
    return base64_encode($response);
}

// ---- OpenAI TTS (fallback 1) ----
function cadipel_synthesize_openai(string $text, string $gender, float $rate): ?string
{
    if (OPENAI_KEY === '') {
        return null;
    }
    $voice = $gender === 'm' ? 'onyx' : 'nova';

    $ch = curl_init('https://api.openai.com/v1/audio/speech');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode([
            'model'           => 'tts-1',
            'input'           => $text,
            'voice'           => $voice,
            'speed'           => $rate,
            'response_format' => 'mp3',
        ]),
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . OPENAI_KEY, 'Content-Type: application/json'],
        CURLOPT_TIMEOUT    => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr !== '' || $httpCode !== 200) {
        return null;
    }
    return base64_encode($response);
}

// ---- Google TTS (fallback 2) ----
function cadipel_synthesize_google(string $langCode, string $voiceName, float $rate, array $input): ?string
{
    if (GOOGLE_TTS_KEY === '') {
        return null;
    }
    $payload = [
        'input'       => $input,
        'voice'       => ['languageCode' => $langCode, 'name' => $voiceName],
        'audioConfig' => ['audioEncoding' => 'MP3', 'speakingRate' => $rate],
    ];

    $ch = curl_init('https://texttospeech.googleapis.com/v1/text:synthesize?key=' . GOOGLE_TTS_KEY);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 15,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr !== '' || $httpCode !== 200) {
        return null;
    }
    $data = json_decode($response, true) ?? [];
    return $data['audioContent'] ?? null;
}

$body   = read_json_body();
$text   = trim((string) ($body['text'] ?? ''));
if ($text === '') {
    json_error(400, 'Missing text');
}

$uiLang = strtolower(trim((string) ($body['lang'] ?? '')));
$gender = isset($body['gender']) && $body['gender'] === 'm' ? 'm' : 'f';
$rate   = (float) ($body['rate'] ?? 1.0);
if (!is_finite($rate)) {
    $rate = 1.0;
}
$rate = max(0.5, min(2.0, $rate));

$langCode = cadipel_detect_tts_language($text, $uiLang);

$audio = null;

// Azure (primary, Spanish only — the site's default language)
if ($audio === null && str_starts_with($langCode, 'es-')) {
    $audio = cadipel_synthesize_azure($text, $gender, $rate);
}

// OpenAI (fallback 1, any language)
if ($audio === null) {
    $audio = cadipel_synthesize_openai($text, $gender, $rate);
}

// Google (fallback 2)
if ($audio === null) {
    $voiceName = cadipel_voice_name($langCode, $gender);
    $input     = cadipel_build_speech_input($text);
    $audio     = cadipel_synthesize_google($langCode, $voiceName, $rate, $input);
}

if ($audio === null) {
    json_error(503, 'All TTS providers unavailable');
}

echo json_encode(['audio' => $audio, 'mimeType' => 'audio/mp3']);
