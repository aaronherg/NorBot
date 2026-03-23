<?php
include(__DIR__ . "/models/host/Qwen3/Qwen3.php");
include(__DIR__ . "/models/host/Groq/Groq.php");
include(__DIR__ . "/models/local/Mistral/Mistral.php");
include(__DIR__ . "/db/query.php");

$mensaje = $_POST['mensaje'] ?? '';

$prompt_generales = __DIR__ . '/prompts/prompt_generales.prompt';
$prompt_sql = __DIR__ . '/prompts/prompt_sql.prompt';
$prompt_analisis = __DIR__ . '/prompts/prompt_analisis.prompt';
$prompt_desiciones = __DIR__ . '/prompts/prompt_desiciones.prompt';
$prompt_restrinciones = __DIR__ . '/prompts/prompt_restrinciones.prompt';
$prompt_tablas_db = __DIR__ . '/prompts/prompt_tablas_db.prompt';

if (!file_exists($prompt_generales) || !file_exists($prompt_sql) || !file_exists($prompt_analisis) || !file_exists($prompt_desiciones) || !file_exists($prompt_restrinciones) || !file_exists($prompt_tablas_db)) {
    echo "Faltan uno o más archivos de prompt.";
    exit;
}

$promptGenerales = file_get_contents($prompt_generales);
$promptSql = file_get_contents($prompt_sql);
$promptAnalisis = file_get_contents($prompt_analisis);
$promptDesiciones = file_get_contents($prompt_desiciones);
$promptRestrinciones = file_get_contents($prompt_restrinciones);
$promptTablasDb = file_get_contents($prompt_tablas_db);


//Variables
$contador_reconsulta = 0;
$MAXIMO_RECONSULTA = 3;


$respuestaDesicion = Groq($promptGenerales . $promptDesiciones . $promptTablasDb . "\nUsuario: " . $mensaje);

if(trim($respuestaDesicion) == "SQL"){

    $respuestaQuery = Groq($promptGenerales . $promptSql . $promptTablasDb . "\nUsuario: " . $mensaje); 

    if (trim($respuestaQuery) == "NOQUERY") {
        echo "No hemos podido entender tu petición.";
        exit;
    }
    else{

        $resultDB = query($respuestaQuery);

        if ((json_decode($resultDB, true)['resultado'] ?? '') == 'SIN_RESULTADOS') {
            //echo "No se encontraron resultados relacionados con tu pregunta.";
            /*
            do {
                $resultadoReconsulta =  Groq("\nUsuario: " . $mensaje);

            }while();
            */
            exit;
        } 
        else if ((json_decode($resultDB, true)['resultado'] ?? '') == "CONSULTA_INVALIDA") {
            echo "La consulta generada fue inválida. Reformula tu pregunta con mas detalles para poder entenderte mejor.";
            exit;

        } 
        else {
            
            $respuestaAnalisis = Groq($promptGenerales . $promptAnalisis . $promptRestrinciones . "\nUsuario: " . $mensaje ."\nResultados: " . $resultDB);
            echo $respuestaAnalisis;
            exit;      

        }

    }
    
}
else if(trim($respuestaDesicion) == "NOSQL"){

    $respuestaComun = Groq($promptGenerales . $promptTablasDb .  $promptRestrinciones . "\nUsuario: " . $mensaje);
    echo $respuestaComun;

}
else{
    
}


?>
