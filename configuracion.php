<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/BaseDatos.php';
include __DIR__ . '/librerias/autoload.php';
require __DIR__ . '/basesdatosexterna.php';

define ('SERVIDOR', 'http://192.168.1.4/d_catalogo_online/');
//define ('SERVIDOR', 'http://192.168.1.199:8081/preproduccion/');

define ('MENSAJESTERMINAL', true);
define ('USER_AGENT', '192.168.1.4:mantenimientos');
define ('RUTA_LOG', "/var/log/grcurl/output-");
define ('RUTA_FICHEROSTEMPORALES', __DIR__ . "/temp");

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
