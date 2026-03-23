<?php
    include("config.php");

    function query($query) {
        global $conn;
        $result = $conn->query($query);

        if (!$result) {
            return json_encode(["error" => "CONSULTA_INVALIDA"]);
        }

        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        header('Content-Type: application/json');

        if (empty($data)) {
            return json_encode(["resultado" => "SIN_RESULTADOS"]);
        } else {
            return json_encode($data);
        }
    }
?>
