<?php

include __DIR__ . '/configuracion.php';

use grcURL\Exception as grException;

/**
 * En este archivo se van a generar las nuevas funcionalidades que nos van a permitir manejar el catálogo web desde microgest
 * sin la necesidad de interactuar con él
 * No se trabaja directamente desde index.php porque éste se quedó un poco desactualizado con las nuevas necesidades y necesitamos algunas 
 * mejoras al respecto...
 * 
 *  */
$tag = $argv [1];

$parametros = array(
    "matriculas" => array(
        'cantidad' => 2,
        'indices' => array(
            '-c' => array('tag' => 'centro', 'obligatorio' => true, 'tipo' => 'is_string'), // centro
            '-s' => array('tag' => 'suma', 'obligatorio' => true, 'tipo' => 'is_numeric')  // se suma al valor actual de la tabla
        ),
        'acciones' => array(
            'consulta-centro' => array('clase' => 'Matriculas', 'metodo' => '_get')
        )
    ),
    "tarjeta-personal" => array(
        'cantidad' => 2,
        'indices' => array(
            '-r' => array('tag' => 'ruta', 'obligatorio' => true, 'tipo' => 'is_string'), // centro
            '-s' => array('tag' => 'string', 'obligatorio' => true, 'tipo' => 'is_string')  // se suma al valor actual de la tabla
        ),
        'acciones' => array(
            'consulta-centro' => array('clase' => 'GenerarTarjetaPersonal', 'metodo' => '_get')
        )
    ),
    "neumaticos-soledad" => array(
        'cantidad' => 1,
        'indices' => array(
            '-r' => array('tag' => 'ruta', 'obligatorio' => true, 'tipo' => 'is_string') //ruta a la carpeta para el fichero de salida
        ),
        'acciones' => array(
            'consulta-centro' => array('clase' => 'NeumaticosSoledad', 'metodo' => '_generarMasters')
        )
    ),
    'precios-soledad' => array(
        'cantidad' => 1,
        'indices' => array(
            '-p' => array('tag' => 'porcentaje', 'obligatorio' => false, 'tipo' => 'is_numeric')
        ),
        'acciones' => array(
            'generar-precios' => array('clase' => 'NeumaticosSoledad', 'metodo' => '_generarPrecios'),
            'generar-stock' => array('clase' => 'Stock', 'metodo' => '_generar')
        )
    )
);
_echo("Comenzamos ...");
if (!is_string($tag)) {
    _echo("Parámetros incorrectos. No se continúa...");
    exit();
}
try {
    if (isset($parametros[$tag])) {
//    _echo($tag);
        $comandos = _compruebaParametros($argv, $parametros[$tag]);
//    _var_dump($comandos);
        $respuesta = [];
        foreach ($parametros[$tag]['acciones'] as $clave => $valor) {
            $clase = $valor['clase'];
            $metodo = $valor['metodo'];
            $objeto = new $clase();
            $respuesta [] = $objeto->$metodo($comandos);
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
    if ($parametrosMinimos['cantidad'] > count($comandos)) {
        return false;
    }
    foreach ($parametrosMinimos['indices'] as $clave => $valor) {
        $tipo = $valor['tipo'];
        if (!isset($comandos[$clave]) || !$tipo($comandos[$clave])) {
            return false;
        }
        if (isset($valor['tag'])) {
            $comandos [$valor['tag']] = $comandos[$clave];
            unset($comandos[$clave]);
        }
    }
    return $comandos;
}
