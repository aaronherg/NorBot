<?php

    $hots = "localhost";
    $usuario = "root";
    $contrasena = "";
    $dbnombre = "AI";

    error_reporting(0);
    ini_set('display_errors', 0);

    try {

        $conn = new mysqli($hots, $usuario, $contrasena, $dbnombre);

        if ($conn->connect_error) {
            throw new Exception("Lo sentimos, error inesperado comuniquese con el desarrollador...");
        }

    } catch (Exception $e) {
        echo $e->getMessage();
    }

?>