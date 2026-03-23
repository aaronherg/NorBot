<?php

function Groq($input) {
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer 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'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'openai/gpt-oss-120b',
            'messages' => [
                ['role' => 'user', 'content' => $input]
            ]
        ])
    ]);

    $res = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($res, true);
    return $json['choices'][0]['message']['content'] ?? 'Error en la respuesta';
}

?>
