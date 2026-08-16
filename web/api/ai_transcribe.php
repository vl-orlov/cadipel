<?php

require_once __DIR__ . '/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_error(405, 'Method not allowed');
}

const CADIPEL_MIN_AUDIO_BYTES = 512;

function cadipel_validate_audio_payload(string $audioBase64): void
{
    $decoded = base64_decode($audioBase64, true);
    if ($decoded === false || strlen($decoded) < CADIPEL_MIN_AUDIO_BYTES) {
        json_error(400, 'Audio too short');
    }
}

function cadipel_whisper_transcribe(string $audioBase64, string $audioMime, string $replyLang): string
{
    $ext     = ['audio/webm' => 'webm', 'audio/ogg' => 'ogg', 'audio/mp4' => 'mp4', 'audio/wav' => 'wav', 'audio/mpeg' => 'mp3'][$audioMime] ?? 'webm';
    $tmpFile = tempnam(sys_get_temp_dir(), 'cadipel_whisper_') . '.' . $ext;
    file_put_contents($tmpFile, base64_decode($audioBase64));

    $fields = [
        'file'            => new CURLFile($tmpFile, $audioMime, 'audio.' . $ext),
        'model'           => 'whisper-1',
        'response_format' => 'text',
    ];
    if (in_array($replyLang, ['es', 'en'], true)) {
        $fields['language'] = $replyLang;
    }

    $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $fields,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . OPENAI_KEY],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    unlink($tmpFile);

    if ($curlErr)          throw new RuntimeException('Whisper cURL: ' . $curlErr);
    if ($httpCode !== 200) throw new RuntimeException('Whisper error: ' . $response);

    return trim($response);
}

/** @return array{transcript: string} */
function cadipel_parse_transcribe_response(string $rawText): array
{
    $text = trim($rawText);
    $text = preg_replace('/^```(?:json)?\s*\n?/i', '', $text);
    $text = preg_replace('/\n?```\s*$/', '', $text);
    $text = trim((string) $text);

    $parsed = json_decode($text, true);
    if (is_array($parsed) && array_key_exists('transcript', $parsed)) {
        return ['transcript' => trim((string) $parsed['transcript'])];
    }

    if (preg_match('/"transcript"\s*:\s*"((?:[^"\\\\]|\\\\.)*)"/su', $text, $m)) {
        return ['transcript' => stripcslashes($m[1])];
    }

    return ['transcript' => ''];
}

function cadipel_gemini_transcribe(string $audioBase64, string $audioMime): string
{
    $payload = [
        'contents' => [[
            'role'  => 'user',
            'parts' => [
                ['inlineData' => ['mimeType' => $audioMime, 'data' => $audioBase64]],
            ],
        ]],
        'generationConfig' => [
            'responseMimeType' => 'application/json',
            'responseSchema'   => [
                'type'       => 'object',
                'properties' => ['transcript' => ['type' => 'string']],
                'required'   => ['transcript'],
            ],
            'thinkingConfig' => ['thinkingBudget' => 0],
        ],
        'systemInstruction' => ['parts' => [[
            'text' => 'Speech-to-text only. JSON field transcript = verbatim words spoken in the audio. '
                . 'Empty string if silent or unclear. Never invent words the speaker did not say.',
        ]]],
    ];

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . GEMINI_KEY;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 30,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr)          throw new RuntimeException('cURL error: ' . $curlErr);
    $data = json_decode($response, true) ?? [];
    if ($httpCode !== 200) throw new RuntimeException($data['error']['message'] ?? 'Gemini API error ' . $httpCode);

    $raw    = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $parsed = cadipel_parse_transcribe_response($raw);

    return trim($parsed['transcript']);
}

function cadipel_sanitize_transcript(string $transcript): string
{
    $text = trim($transcript);
    if ($text === '') {
        return '';
    }

    $parsed = json_decode($text, true);
    if (is_array($parsed) && array_key_exists('transcript', $parsed)) {
        $text = trim((string) $parsed['transcript']);
    }

    $lower = strtolower($text);
    if (str_contains($lower, 'transcribe only') || str_contains($lower, 'return json')) {
        return '';
    }

    return $text;
}

$body      = read_json_body();
$audio     = (string) ($body['audio'] ?? '');
$audioMime = (string) ($body['audioMime'] ?? 'audio/webm');
$replyLang = trim((string) ($body['lang'] ?? '')) ?: 'es';

if ($audio === '') {
    json_error(400, 'Missing audio');
}
cadipel_validate_audio_payload($audio);

try {
    if (OPENAI_KEY !== '') {
        $transcript = cadipel_sanitize_transcript(cadipel_whisper_transcribe($audio, $audioMime, $replyLang));
    } elseif (GEMINI_KEY !== '') {
        $transcript = cadipel_sanitize_transcript(cadipel_gemini_transcribe($audio, $audioMime));
    } else {
        json_error(503, 'No transcription provider configured');
        exit;
    }
} catch (RuntimeException $e) {
    json_error(502, 'Audio transcription failed: ' . $e->getMessage());
    exit;
}

echo json_encode(['transcript' => $transcript]);
