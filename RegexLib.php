<?php
class RegexLib {

    public function validarComillas($cadena, $validarBlancos = false) {
        $valor = [];
        preg_match("/(^\"|^')/", $cadena, $valor); // Solo caracteres
        $valor = $valor[0] ?? false;

        $valorCierre = [];
        preg_match("/(\"|\')$/", $cadena, $valorCierre); // Solo caracteres
        $valorCierre = $valorCierre[0] ?? false;

        if ($validarBlancos) {
            if (strpos($cadena, ' ') !== false && !strpos($cadena, '"')) {
                return true;
            }
        }

        return !empty($valor) || !empty($valorCierre);
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
