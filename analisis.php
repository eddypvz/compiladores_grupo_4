<?php
function dd($var) {
    print '<pre>';
    print_r($var);
    print '</pre>';
}

function separarPorSaltoDeLinea($codigo) {

    // Arreglo que va a devolver mi código
    $arrCodigo = [];

    // lo separo por salto de línea
    $code = preg_split("/\r\n|\n|\r/", $codigo);

    // recorro las líneas
    foreach ($code as $line) {
        // quito caracteres en blanco de la línea
        $line = trim($line);
        $arrCodigo[] = $line;
    }
    return $arrCodigo;
}

function prepararSalida($arrayCodigo) {

    $strSalida = '';

    $contador = 1;
    foreach ($arrayCodigo as $linea) {
        $strSalida .= "<div class='linea'><div class='contador'>{$contador}</div><div class='contenido'>{$linea}</div></div>";
        $contador++;
    }

    return $strSalida;
}

function analisisLexico($codigo) {
    print "Análisis Léxico <br> <br>";

    $code = separarPorSaltoDeLinea($codigo);

    print prepararSalida($code);
}

function analisisSintactico($codigo) {
    print "Análisis Sintáctico <br> <br>";

    $code = separarPorSaltoDeLinea($codigo);

    print prepararSalida($code);
}
