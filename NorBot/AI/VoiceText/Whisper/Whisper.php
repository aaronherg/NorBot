<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_FILES['audio'])) {
    $audioFile = $_FILES['audio']['tmp_name'];
    echo whisper($audioFile);

}

function whisper($audio){

    $ch = curl_init('http://127.0.0.1:5000/transcribe');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $post = ['audio' => new CURLFile($audio)];
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;

}

?>


