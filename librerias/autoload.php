<?php

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__), RecursiveIteratorIterator::LEAVES_ONLY);
$includes = Array('php');
$actual = __DIR__ . '/autoload.php';

/**
 * Buscamos dentro de todas las subcarpetas de las librerías los archivos include
 * para obtener los ficheros ordenados que tenemos que incluir en las definiciones
 */
foreach ($iterator as $path) {
    $ruta = $path->__toString();
    $archivoInclude = $ruta . '/include.php';
    if (is_dir($ruta) && is_file($archivoInclude)) {
        include $archivoInclude;
    }
}

foreach ($include as $tipo => $datos) {
    foreach ($datos ['archivos'] as $fichero) {
        $file = $datos['ruta'] . '/' . $fichero;
        if (is_file($file)) {
//            echo  $file .PHP_EOL;
            include $file;
        }
    }
}
