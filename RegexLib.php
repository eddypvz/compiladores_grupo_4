<?php
class RegexLib {

    public function validarComillas($cadena) {
        $valor = [];
        preg_match("/(^\"|^')/", $cadena, $valor); // Solo caracteres
        $valor = $valor[0] ?? false;
        return !empty($valor);
    }

    public function comillasCorrectas($cadena) {
        $valor = [];
        preg_match("/(\")(.*)(\")/", $cadena, $valor); // Solo caracteres
        $valor = $valor[0] ?? false;

        if (empty($valor)) {
            $valor = [];
            preg_match("/(\')(.*)(\')/", $cadena, $valor); // Solo caracteres
            $valor = $valor[0] ?? false;
        }
        return !empty($valor);
    }
}
