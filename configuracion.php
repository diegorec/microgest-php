<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/BaseDatos.php';
include __DIR__ . '/librerias/autoload.php';
require __DIR__ . '/basesdatosexterna.php';

define ('SERVIDOR', 'http://192.168.1.4/');
//define('SERVIDOR', 'http://192.168.1.199:8081/catalogo/');

define('MENSAJESTERMINAL', true);
define('USER_AGENT', '192.168.1.50:mantenimientos');
define('RUTA_LOG_BASE', "/var/log/grcurl/output-");
define('RUTA_COMANDOS_BASE', "/var/log/grcurl/comandos-");
define('RUTA_FICHEROSTEMPORALES', "/home/gr/temporales-catalogov2");
define('RUTA_COMANDOS', "/mnt/imagenes/php/probasv2/comandos/");
define('RUTA_PARAMS', "/mnt/imagenes/php/probasv2/params/");
define('RUTA_ASSETS', "/mnt/imagenes/php/probasv2/assets/");
define('LOGGERTAG', "192.168.1.50:mantenimientos-catalogo");
define('CLAVELOGIN', "NSM3JygkKRxRXF1TOj4nICstCVFEUVJdQj");

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
