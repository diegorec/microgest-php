<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/BaseDatos.php';
include __DIR__ . '/librerias/autoload.php';
require __DIR__ . '/basesdatosexterna.php';

define('SERVIDOR', 'http://192.168.1.8:8081/');
define('REST_API', 'http://192.168.1.8:8082/');

define('MENSAJESTERMINAL', true);
define('USER_AGENT', '192.168.1.154:microgest-php:pruebas');
define('RUTA_LOG_BASE', __DIR__ . "/../log/");
define('RUTA_COMANDOS_BASE', __DIR__ . "/../log/");
define('RUTA_FICHEROSTEMPORALES', __DIR__ . "/../log/");
define('RUTA_COMANDOS', __DIR__ . "/comandos/");
define('RUTA_PARAMS', __DIR__ . "/params/");
define('RUTA_ASSETS', __DIR__ . "/assets/");
define('LOGGERTAG', "192.168.1.154:microgest-php");
define('CLAVELOGIN', "NSM3JygkKRxRXF1TOj4nICstCVFEUVJdQj");
define('DOCUMENTO_PERSONAL', "http://catalogoonline.recalvi.es/imagenesgestion/personal/");
define('RUTA_TEMP_FILES', __DIR__ . "/../temp/");

function _getRutaLog($prefix = '') {
    $base = RUTA_LOG_BASE;
    $date = date("YW");
    $extension = ".log";
    return  "$base$prefix$date$extension";
}
