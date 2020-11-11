<?php

include __DIR__ . '/configuracion.php';
include __DIR__ . '/helpers.php';
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

try {
    $parametros = getDatosComando($argv[1]);

    if (is_object($parametros)) {
        $comandos = _compruebaParametros($argv, $parametros);
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
} catch(UnexpectedValueException $e) {
    log_error($e->getMessage(), $e->getTrace());
    _echo_error($e->getMessage());
}

function _compruebaParametros($consulta, $parametrosMinimos) {
    $array = $consulta;
    $contador = count($array);
    $etiqueta = $array[1];
    unset($array[0]); // nombre del fichero
    unset($array[1]); // etiqueta con la accion
    // creamos un array con pares clave valor
    // para ello la cuenta total de parámetros tiene que ser par
    if (count($array) % 2 !== 0) {
        throw new \Exception("[$etiqueta] Todo parámetro debe disponer de un valor");
    }
    $comandos = [];
    for ($i = 2; $i < $contador; $i++) {
        $clave = $array[$i++];
        $valor = $array[$i];
        $comandos[$clave] = $valor;
    }
    // Comprobamos que no se hayan introducido parámetros de menos ...
    if ($parametrosMinimos->cantidad > count($comandos)) {
        throw new \Exception("[$etiqueta] Este comando debe estar compuesto por $parametrosMinimos->cantidad parametro(s)");
    }
    foreach ($parametrosMinimos->indices as $clave => $valor) {
        $tipo = $valor->tipo;
        if ($valor->obligatorio && (!isset($comandos[$clave]) || !$tipo($comandos[$clave]))) {
            throw new \Exception("[$etiqueta] $clave no es de tipo $tipo");
        }
        if (isset($valor->tag, $comandos[$clave])) {
            $comandos[$valor->tag] = $comandos[$clave];
            unset($comandos[$clave]);
        }
    }
    return $comandos;
}

function getDatosComando($tag) {
    $ficheroComando = RUTA_COMANDOS . "$tag.json";
    _echo("Abriendo fichero: $ficheroComando");
    if (!file_exists($ficheroComando)) {
        throw new \Exception("[$ficheroComando] No existe el comando. No se continúa");
    }
    $fileContent = file_get_contents($ficheroComando);
    return json_decode($fileContent);
}
