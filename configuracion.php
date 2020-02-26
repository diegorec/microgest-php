<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/BaseDatos.php';
include __DIR__ . '/librerias/autoload.php';
require __DIR__ . '/basesdatosexterna.php';

define('SERVIDOR', 'http://192.168.1.8:8081/');

define('MENSAJESTERMINAL', true);
define('USER_AGENT', '192.168.1.154:microgest-php:pruebas');
define('RUTA_LOG_BASE', "/var/log/grcurl/");
define('RUTA_COMANDOS_BASE', "/var/log/grcurl/");
define('RUTA_FICHEROSTEMPORALES', "/var/log/grcurl/");
define('RUTA_COMANDOS', "comandos/");
define('RUTA_PARAMS', "params/");
define('RUTA_ASSETS', "assets/");
define('LOGGERTAG', "192.168.1.154:microgest-php");
define('CLAVELOGIN', "NSM3JygkKRxRXF1TOj4nICstCVFEUVJdQj");
define('DOCUMENTO_PERSONAL', "http://catalogoonline.recalvi.es/imagenesgestion/personal/");

function _echo($string) {
    if (MENSAJESTERMINAL) {
        echo $string . PHP_EOL;
    }
}

function _var_dump($mixed) {
    if (MENSAJESTERMINAL) {
        var_dump($mixed);
    }
}

function _getRutaLog() {
    $base = RUTA_LOG_BASE;
    $date = date("YW");
    $extension = ".log";
    return  "$base$date$extension";
}
