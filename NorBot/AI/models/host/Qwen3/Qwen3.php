
<?php

function Qwen3($input) {

    $ch = curl_init('https://router.huggingface.co/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer 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'
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => 'Qwen/Qwen3-Coder-480B-A35B-Instruct:novita',
            'messages' => [['role' => 'user', 'content' => $input]]
        ])
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($res, true);
    return $json['choices'][0]['message']['content'] ?? 'Error';

}

?>