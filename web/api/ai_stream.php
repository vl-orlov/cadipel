<?php

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/cadipel_prompt.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    json_error(405, 'Method not allowed');
}

$body = read_json_body();

$messages = $body['messages'] ?? [];
$lang     = trim((string) ($body['lang'] ?? 'es'));

if (!is_array($messages) || empty($messages)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing messages']);
    exit;
}

$systemPrompt = build_cadipel_system_prompt($lang);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');
while (ob_get_level() > 0) {
    ob_end_flush();
}

/** @param list<array{role?: string, content?: string}> $messages */
function cadipel_emit_gemini_stream(array $messages, string $systemPrompt): bool
{
    $contents = [];
    foreach ($messages as $msg) {
        $role       = ($msg['role'] ?? '') === 'assistant' ? 'model' : 'user';
        $contents[] = ['role' => $role, 'parts' => [['text' => (string) ($msg['content'] ?? '')]]];
    }

    $payload = [
        'contents'         => $contents,
        'generationConfig' => ['thinkingConfig' => ['thinkingBudget' => 0]],
    ];
    if ($systemPrompt !== '') {
        $payload['systemInstruction'] = ['parts' => [['text' => $systemPrompt]]];
    }

    $url        = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:streamGenerateContent?alt=sse&key=' . GEMINI_KEY;
    $lineBuffer = '';
    $streamed   = false;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST          => true,
        CURLOPT_POSTFIELDS    => json_encode($payload),
        CURLOPT_HTTPHEADER    => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT       => 60,
        CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$lineBuffer, &$streamed) {
            $lineBuffer .= $data;
            $lines      = explode("\n", $lineBuffer);
            $lineBuffer = array_pop($lines) ?? '';

            foreach ($lines as $line) {
                $line = rtrim($line, "\r");
                if (!str_starts_with($line, 'data: ')) {
                    continue;
                }
                $raw = substr($line, 6);
                if ($raw === '[DONE]') {
                    continue;
                }

                $parsed = json_decode($raw, true);
                $text   = $parsed['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if ($text !== '') {
                    $streamed = true;
                    echo 'data: ' . json_encode(['text' => $text]) . "\n\n";
                    flush();
                }
            }
            return strlen($data);
        },
    ]);

    curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr !== '' || ($httpCode !== 0 && $httpCode !== 200)) {
        return false;
    }

    return $streamed;
}

/** @param list<array{role?: string, content?: string}> $messages */
function cadipel_fetch_openai_reply(array $messages, string $systemPrompt): string
{
    if (OPENAI_KEY === '') {
        return '';
    }

    $chatMessages = [];
    if ($systemPrompt !== '') {
        $chatMessages[] = ['role' => 'system', 'content' => $systemPrompt];
    }
    foreach ($messages as $m) {
        $chatMessages[] = [
            'role'    => ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
            'content' => (string) ($m['content'] ?? ''),
        ];
    }

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode(['model' => 'gpt-4o-mini', 'messages' => $chatMessages]),
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . OPENAI_KEY, 'Content-Type: application/json'],
        CURLOPT_TIMEOUT        => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr !== '' || $httpCode !== 200) {
        return '';
    }

    $data = json_decode($response, true) ?? [];
    return trim((string) ($data['choices'][0]['message']['content'] ?? ''));
}

$streamed = false;
if (GEMINI_KEY !== '') {
    $streamed = cadipel_emit_gemini_stream($messages, $systemPrompt);
}

if (!$streamed) {
    $reply = cadipel_fetch_openai_reply($messages, $systemPrompt);
    if ($reply === '') {
        echo 'data: ' . json_encode(['error' => 'All AI providers unavailable']) . "\n\n";
    } else {
        echo 'data: ' . json_encode(['text' => $reply]) . "\n\n";
    }
    flush();
}

echo "data: [DONE]\n\n";
flush();
