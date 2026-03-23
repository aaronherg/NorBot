<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $texto = $data['text'] ?? '';
    $voz = $data['voice'] ?? '';

    if ($texto && $voz) {
        $audioData = Edge($texto, $voz);
        if ($audioData !== false) {
            header('Content-Type: audio/wav');
            echo $audioData;
            exit;
        } else {
            http_response_code(500);
            echo "Error al generar audio.";
        }
    } else {
        http_response_code(400);
        echo "Parámetros faltantes.";
    }
}

function Edge($texto, $voz) {
    $postData = [
        'text' => $texto,
        'voice' => $voz
    ];

    $ch = curl_init('http://127.0.0.1:5005/generate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($error || $statusCode !== 200) {
        return false;
    }
    return $response;
}
?>
