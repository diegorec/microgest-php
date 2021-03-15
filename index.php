<?php

include __DIR__ . '/configuracion.php';
include __DIR__ . '/helpers.php';
include __DIR__ . '/readme.php';
include __DIR__ . '/vendor/autoload.php';

$date = date('YW');
log_info("comandos", implode(' ', $argv));

$modoVerbose = false;
foreach ($argv as $key => $value) {
    if ($value === '--verbose') {
        $modoVerbose = true;
        unset($argv[$key]);
        break;
    }
}
define('MODO_VERBOSE', $modoVerbose);

// Ayuda en la línea de comandos
$needsHelp = needs_help($argv);
if ($needsHelp) {
    help(RUTA_COMANDOS, $needsHelp);
    exit();
}

//Ejecución del comando
try {
    $parametros = command_data(RUTA_COMANDOS, $argv[1]);

    if (is_object($parametros)) {
        $comandos = check_parameters($argv, $parametros);
        $respuesta = [];
        foreach ($parametros->acciones as $clave => $valor) {
            _echo_info("## Se inicia la ejecución de la acción: $clave");
            $clase = $valor->clase;
            $metodo = $valor->metodo;
            $objeto = new $clase();
            if (isset($valor->parametros)) {
                foreach ($valor->parametros as $pClave => $pValor) {
                    $pMetodoSet = "set$pClave";
                    if (method_exists($clase, $pMetodoSet)) {
                        _echo_info("Se añade a la clase $clase la variable $pMetodoSet");
                        $objeto->$pMetodoSet($pValor);
                    }
                }
            }
            $respuesta[] = $objeto->$metodo($comandos);
            _echo_info("## Se finaliza la ejecución de la acción: $clave");
        }
    }
} catch (grcURL\Exception $e) {
    log_error($e->getMessage(), $e->getTrace());
    _echo_error($e->getMessage());
} catch (\Exception $e) {
    log_error($e->getMessage(), $e->getTrace());
    _echo_error($e->getMessage());
} catch (UnexpectedValueException $e) {
    log_error($e->getMessage(), $e->getTrace());
    _echo_error($e->getMessage());
}
