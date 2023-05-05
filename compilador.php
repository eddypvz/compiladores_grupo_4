<?php

class Compilador {

    // Guarda el código como cadena
    private $codigo;

    // Librería de tokens
    private $tokensLib;

    // Contador de tokens
    private $tokensContador;

    // Tokens encontrados
    private $tokensList;

    function __construct($codigo) {
        $this->regexLib = new RegexLib();
        $this->tokensLib = [];
        $this->tokensContador = 0;
        $this->codigo = $codigo;

        // Inicio la libtería de tokens
        $this->iniciarLibreriaTokens();
    }

    private function registrarToken($tipo, $subtipo, $token, $regex, $regexType, $noLinea, $valor = '', $error = '') {

        $arrTmp = [
            'tipo' => $subtipo,
            'token' => $token,
            'regex' => $regex,
            'regexType' => $regexType,
            'line' => $noLinea,
            'value' => $valor,
            'error' => $error,
        ];

        //dd($arrTmp);

        $this->tokensList[$tipo][] = $arrTmp;

        $this->tokensContador++;
    }

    // Iniciar la librería de tokens, acá se listan todas las palabras o tokens del lenguaje.
    private function iniciarLibreriaTokens() {

        // TIPOS DE REGEX
        // con_valor = Declaración con valor, osea declara el valor del identificador en la misma línea
        // sin_valor = Declaración de la variable sin valor, solo la declara


        $this->tokensLib['palabrasReservadas'] = [
            'if' => [
                'token' => '/^Si/i',
                'regex' => [
                    'reservada' => '/^Si/i',
                    'valor' => '/\((.*)\)/i',
                ],
                'modificador' => 'exigir_opt_logica'
            ],
            'print' => [
                'token' => '/^#Mostrar/i',
                'regex' => [
                    'reservada' => '/^#Mostrar/i',
                    'valor' => '/\((.*)\)/i',
                ],
            ],
            'read' => [
                'token' => '/^#Leer/i',
                'regex' => [
                    'reservada' => '/^#Leer/i',
                    'valor' => '/\((.*)\)/i',
                ],
            ],
            'return' => [
                'token' => '/^#Regresar/i',
                'regex' => [
                    'reservada' => '/^#Regresar/i',
                    'valor' => '/\s*...*\s*;$/i',
                ],
            ],
            'braceClose' => [
                'token' => '/^}/i',
                'regex' => [
                    'reservada' => '/^}/i',
                ],
            ],
            'braceOpen' => [
                'token' => '/^{/i',
                'regex' => [
                    'reservada' => '/^{/i',
                ],
            ],
        ];

        $this->tokensLib['logicos'] = [
            'and' => [
                'token' => '/&&/i',
                'regex' => [
                    'valor' => '/\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*&&\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*/i'
                ],
            ],
            'diff' => [
                'token' => '/!=/i',
                'regex' => [
                    'valor' => '/\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*!=\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*/i'
                ],
            ],
            'minor' => [
                'token' => '/</i',
                'regex' => [
                    'valor' => '/\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*<\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*/i'
                ],
            ],
            'greater' => [
                'token' => '/>/i',
                'regex' => [
                    'valor' => '/\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*>\s*([a-zA-Z_]+[0-9]*|[0-9]*)*\s*/i'
                ],
            ],
        ];

        $this->tokensLib['identificadores'] = [
            // para con_valor: Que inicie con el identificador del tipo de dato (#Caracter, #Decimal, etc), luego uno o más espacios en blanco (\s), seguido de N letras hasta que encuentre uno o N espacios en blanco seguido de igual. Ejemplo:
            // #Decimal salario = "test";

            // para sin_valor: Que inicie con el identificador del tipo de dato (#Caracter, #Decimal, etc), luego uno o más espacios en blanco (\s), seguido de N letras hasta que encuentre uno o N espacios en blanco seguido de punto y coma. Ejemplo:
            // #Decimal salario;

            'string' => [
                'token' => '/\s+[a-zA-Z_]+[0-9]*\s*/',
                'regex' => [
                    'con_valor' => '/^#Caracter\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*=\s*/i',
                    'sin_valor' => '/^#Caracter\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*/i',
                    'valor' => '/([^"|\'])([^"|\']+)/i',
                ],
            ],
            'float' => [
                'token' => '/\s+[a-zA-Z_]+[0-9]*\s*/',
                'regex' => [
                    'con_valor' => '/^#Decimal\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*=\s*/i',
                    'sin_valor' => '/^#Decimal\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*/i',
                    'valor' => '/\s*[0-9]+.?[0-9]+$/i',
                ],
            ],
            'integer' => [
                'token' => '/\s+[a-zA-Z_]+[0-9]*\s*/',
                'regex' => [
                    'con_valor' => '/^#Integral\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*=\s*/i',
                    'sin_valor' => '/^#Integral\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*/i',
                    'valor' => '/\s*[0-9]*/i',
                ],
            ],
            'bool' => [
                'token' => '/\s+[a-zA-Z_]+[0-9]*\s*/',
                'regex' => [
                    'con_valor' => '/^#Decision\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*=\s*/i',
                    'sin_valor' => '/^#Decision\s*[a-zA-Z_]+[a-zA-Z_0-9]*\s*/i',
                    'valor' => '/\s*(verdadero|falso)\s*$/i',
                ],
            ],
        ];
    }

    public function lexico() {

        $arrCodigo = separarPorSaltoDeLinea($this->codigo);

        foreach ($arrCodigo as $noLinea => $linea) {
            $this->evaluarLinea($linea, $noLinea + 1);
        }

        foreach ($this->tokensList as $tipo => $list) {
            ?>
            <h5><?php print ucfirst($tipo) ?></h5>
            <table class="table table-striped mb-5">
                <thead class="thead-dark">
                <tr>
                    <td>Línea</td>
                    <td>Tipo</td>
                    <td>Token</td>
                    <td>Regex</td>
                    <td>Subtipo</td>
                    <td>Valor</td>
                    <td>Error</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($list as $token) {
                    ?>
                    <tr class="<?= (!empty($token['error'])) ? 'text-danger' : '' ?>">
                        <td><?= $token['line'] ?></td>
                        <td><?= $token['tipo'] ?></td>
                        <td><?= $token['token'] ?></td>
                        <td><?= $token['regex'] ?></td>
                        <td><?= $token['regexType'] ?></td>
                        <td><?= $token['value'] ?></td>
                        <td><?= $token['error'] ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
            <?php
        }



        /*dd($this->tokensList);
        dd($this->tokensContador);*/

        //dd($arrCodigo);

    }

    private function evaluarLinea($lineaCodigo, $noLinea, $fromParentLine = false) {

        $lineaProcesada = false;
        $lineaCodigo = trim($lineaCodigo); // borro espacios en blanco adelante y al final de la linea
        $lineaRespuesta = '';

        // Si la línea está en blanco, no hago nada
        if (empty($lineaCodigo)) return '';

        foreach ($this->tokensLib as $tipoToken => $tokenList) {

            // si son lógicos, los paso de largo porque los evaluo diferente dentro de una palabra reservada siempre
            if ($tipoToken === 'logicos') continue;

            foreach ($tokenList as $subtipo => $token) {

                foreach ($token['regex'] as $typeRegex => $regex) {

                    // Si el regex es de tipo valor, lo salto ya que lo evaluo diferente
                    if ($typeRegex === 'valor') continue;

                    // Validación si la linea coincide
                    $coincidencias = '';
                    $coincide = preg_match($regex, $lineaCodigo, $coincidencias);

                    // Si coincide con algo de la librería, lo quito de la cadena para seguir evaluando
                    if (!empty($coincide)) {

                        $error = '';
                        $lineaRespuesta = '';

                        // VALIDACIÓN POR TIPO DE TOKEN
                        if ($tipoToken === 'palabrasReservadas') {

                            $lineaRespuesta = preg_replace($regex, '', $lineaCodigo); // Lo reemplazo con nada
                            $identificador = '';
                            $valor = '';
                            $saltarPorSubError = false;

                            // Si tengo regex de valor, debo buscarlo
                            if (!empty($token['regex']['valor'])) {
                                $hasValor = preg_match($token['regex']['valor'], $lineaRespuesta, $valor); // Solo caracteres

                                $valor = $valor[1] ?? ($valor[0] ?? $lineaRespuesta);

                                // Si debería tener valor y no tiene, hay error
                                if (!$hasValor) {
                                    $error = 'Sintaxis incorrecta para "'.$identificador.'", línea '.$noLinea;
                                    $valor = $lineaRespuesta;
                                }
                                else {
                                    // El contenido de las palabras reservadas puede ser una comparación con operadores lógicos, para ello, la palabra reservada me indica el tipo de operacion (en el modificador si tiene)
                                    if (!empty($token['modificador'])) {

                                        // Si el token me exige una operación logica
                                        if ($token['modificador'] === 'exigir_opt_logica') {

                                            $optLogicaDetectada = false;
                                            foreach ($this->tokensLib['logicos'] as $operadorLogico => $regexLogico) {
                                                /*dd($operadorLogico);
                                                dd($regexLogico['token']);
                                                dd($regexLogico['regex']['valor']);*/

                                                // evaluo el valor logico si existe
                                                $valorLogico = [];
                                                preg_match($regexLogico['token'], $valor, $valorLogico); // Solo caracteres
                                                $valorLogico = $valorLogico[0] ?? false;

                                                // si es un valor lógico, valido su estructura, le caigo encima a las variables para no usar otras
                                                if (!empty($valorLogico)) {
                                                    $optLogicaDetectada = true;

                                                    $valorLogico = [];
                                                    preg_match($regexLogico['regex']['valor'], $valor, $valorLogico); // Solo caracteres
                                                    $valorLogico = $valorLogico[0] ?? false;

                                                    if (empty($valorLogico)) {
                                                        $error = 'Operación lógica inválida "'.$valor.'", línea '.$noLinea;
                                                    }
                                                }
                                            }

                                            if (!$optLogicaDetectada) {
                                                $saltarPorSubError = true;
                                                $this->registrarToken('otros', '', '', '', '', $noLinea, $lineaCodigo, "Expresión desconocida en operación lógica, línea {$noLinea}");
                                            }
                                        }
                                    }

                                    // si tiene valor, veo si necesito validar comillas, si sí, entonces se valida que abran y cierren bien
                                    if ($this->regexLib->validarComillas($valor) && !$this->regexLib->comillasCorrectas($valor)) {
                                        $error = 'Falta una comilla en el parámetro de"'.$identificador.'", línea '.$noLinea;
                                    }
                                }
                            }

                            // Registro el token
                            $lineaProcesada = true;

                            if (!$saltarPorSubError) {
                                $this->registrarToken($tipoToken, $subtipo, $identificador, $regex, $typeRegex, $noLinea, $valor, $error);
                            }
                        }
                        else if ($tipoToken === 'identificadores') {

                            $lineaRespuesta = preg_replace($regex, '', $lineaCodigo); // Lo reemplazo con nada
                            $lineaRespuesta = str_replace(';', '', $lineaRespuesta);
                            //dd($coincidencias);

                            $coincidencia = $coincidencias[0] ?? '';
                            $identificador = '';
                            $valor = '';

                            // busco el token (el identificador)
                            preg_match($token['token'], $coincidencia, $identificador); // Solo caracteres

                            // Si declaran la variable con valor, tengo que obtener su valor
                            if ($typeRegex === 'con_valor') {

                                // obtengo el valor del identificador
                                $hasValor = false;
                                if (!empty($token['regex']['valor'])) {
                                    $hasValor = preg_match($token['regex']['valor'], $lineaRespuesta, $valor); // Solo caracteres
                                    $valor = $valor[0] ?? '';
                                }

                                // Si debería tener valor y no tiene, hay error
                                if (!$hasValor && empty($valor)) {
                                    $error = "Declaración de variable con tipo de dato o valor incorrecto, línea {$noLinea}";
                                    $valor = $lineaRespuesta;
                                }
                            }

                            $identificador = $identificador[0] ?? '';

                            $identificador = str_replace(' ', '', $identificador);  // Reemplazo espacios en blanco e iguales, acá sé que llevará un igual ya que lo detecto un typeRegex con valor
                            $identificador = str_replace('=', '', $identificador);
                            $identificador = trim($identificador); // elimino espacios en blanco

                            // Registro el token
                            $lineaProcesada = true;
                            $this->registrarToken($tipoToken, $subtipo, $identificador, $regex, $typeRegex, $noLinea, $valor, $error);
                        }


                        // Si todavía hay contenido en la linea, vuelvo a llamar a la función de forma recursiva
                        /*if (!empty(trim($lineaRespuesta))) {
                            $lineaRespuesta = $this->evaluarLinea($lineaRespuesta, $noLinea, true);
                        }*/
                    }
                }
            }
        }

        // si la linea no se detectó en ningún lugar, la agrego como error
        if (!$lineaProcesada && !$fromParentLine) {
            $this->registrarToken('otros', '', '', '', '', $noLinea, $lineaCodigo, "Expresión desconocida en línea {$noLinea}");
        }

        return $lineaRespuesta;
    }
}
