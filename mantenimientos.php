<?php

include __DIR__ . '/configuracion.php';

use grcURL\Exception as grException;
use phpcli\Colors;

$colors = new Colors();
/**
 * En este archivo se van a generar las nuevas funcionalidades que nos van a permitir manejar el catálogo web desde microgest
 * sin la necesidad de interactuar con él
 * No se trabaja directamente desde index.php porque éste se quedó un poco desactualizado con las nuevas necesidades y necesitamos algunas 
 * mejoras al respecto...
 * 
 *  */
try {
    $parametros = getDatosComando($argv[1]);

    if (is_object($parametros)) {
        $comandos = _compruebaParametros($argv, $parametros);
//    _var_dump($comandos);
        $respuesta = [];
        foreach ($parametros->acciones as $clave => $valor) {
            _echo($colors->info("## Se inicia la ejecución de la acción: $clave"));
            $clase = $valor->clase;
            $metodo = $valor->metodo;
            $objeto = new $clase();
            if ($valor->parametros) {
                foreach ($valor->parametros as $pClave => $pValor) {
                    $pMetodoSet = "set$pClave";
                    if (method_exists($clase, $pMetodoSet)) {
                        _echo($colors->info("Se añade a la clase $clase la variable $pMetodoSet"));
                        $objeto->$pMetodoSet($pValor);
                    }
                }
            }
            $respuesta [] = $objeto->$metodo($comandos);
            _echo($colors->info("## Se finaliza la ejecución de la acción: $clave"));
        }
    }
} catch (grException $e) {
    _echo($e->getMessage());
} catch (\Exception $e) {
    _echo($e->getMessage());
}

function _compruebaParametros($consulta, $parametrosMinimos) {
    $array = $consulta;
    $contador = count($array);
    unset($array[0]); // nombre del fichero
    unset($array[1]); // etiqueta con la accion
    // creamos un array con pares clave valor
    // para ello la cuenta total de parámetros tiene que ser par
    if (count($array) % 2 !== 0) {
        return false;
    }
    $comandos = [];
    for ($i = 2; $i < $contador; $i++) {
        $clave = $array[$i++];
        $valor = $array[$i];
        $comandos [$clave] = $valor;
    }
    // Comprobamos que no se hayan introducido parámetros de menos ...
    if ($parametrosMinimos->cantidad > count($comandos)) {
        return false;
    }
    foreach ($parametrosMinimos->indices as $clave => $valor) {
        $tipo = $valor->tipo;
        if (!isset($comandos[$clave]) || !$tipo($comandos[$clave])) {
            return false;
        }
        if (isset($valor->tag)) {
            $comandos [$valor->tag] = $comandos[$clave];
            unset($comandos[$clave]);
        }
    }
    return $comandos;
}

function getDatosComando($tag) {
    $ficheroComando = RUTA_COMANDOS . "$tag.json";
    if (!file_exists($ficheroComando)) {
        throw new \Exception('No existe el comando. No se continúa');
    }
    $fileContent = file_get_contents($ficheroComando);
    return json_decode($fileContent);
}
