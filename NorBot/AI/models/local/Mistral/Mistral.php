

<?php

function Mistral($input) {
    $data = [
        'model' => 'mistral',
        'prompt' => $input,
        'stream' => false
    ];

    $ch = curl_init('http://ollama-norpikeri.pagekite.me/api/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($response, true);
    return $json['response'] ?? 'Error al generar respuesta.';
}

?>