<?php

include __DIR__ . '/configuracion.php';
include __DIR__ . './helpers.php';
include __DIR__ . '/./vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use phpcli\Colors;

// fix error: Otro de los infinitos problemas de tener dos canales de entrada para los comandos.... 
// El que está bien es el de mantenimineto.php ya que el modo verbose debe eliminarse
// una vez se ha detectado (aqui no se hace para poder reenviarlo a mantenimientos.php)
$modoVerbose = false; 
foreach ($argv as $key => $value) {
    if ($value === '--verbose') {
        $modoVerbose = true;
        break;
    }
}
define('MODO_VERBOSE', $modoVerbose);

$colors = new Colors();
try {
    $date = date('YW');
    $name = "preproducción";
    $logger = new Logger($name);
    $logger->pushHandler(new StreamHandler(RUTA_COMANDOS_BASE . "$date.log", Logger::INFO));
    $logger->pushHandler(new FirePHPHandler());
    $comando = implode(' ', $argv);
    $logger->addInfo("[$comando]");
} catch (UnexpectedValueException $e) {
    _echo($colors->error($e->getMessage()));
}


$permitidos = array(
    'cliente-catalogo' => array('method' => 'post', 'path' => 'admin/rest/usuario'),
    'baja-cliente-catalogo' => array('method' => 'delete', 'path' => 'admin/rest/usuario'),
    'eliminar-genericos-padre' => array('method' => 'post', 'path' => 'mantenimiento/genericospadres'),
    'eliminar-publicidades' => array('method' => 'post', 'path' => 'mantenimiento/publicidades'),
);

if (!(isset($argv[1]) && is_string($argv[1]) && isset($argv[2]) && is_string($argv[2]) && isset($argv[3]) && isset($argv[4]) && is_string($argv[4])
//        && isset($argv[5]) && is_numeric($argv[5])
//        && isset($argv[6]) && is_numeric($argv[6])
        && isset($permitidos[$argv[1]]))) {
    unset($argv[0]); // eliminamos index.php de la lista de comandos recibidos
    $argvStr = implode(" ", $argv);
    $comando = "php " . __DIR__ . "/mantenimientos.php $argvStr";
    _echo($comando);
    _echo(shell_exec($comando));
    exit;
} else if (strcmp($argv[1], 'cliente-catalogo') === 0) {
    $centro = $argv[2];
    $nocliente = $argv[3];
    $subdivision = $argv[4];
    $empresa = 0;
    $cliente = 0;
    $comando = "sudo php " . __DIR__ . "/mantenimientos.php clientes-catalogo -c $centro -n $nocliente -s $subdivision -e $empresa -cli $cliente";
    _echo("COMANDO: $comando");
    _echo(shell_exec($comando));
    exit();
} else if (strcmp($argv[1], 'eliminar-publicidades') === 0) {
    $centro = $argv[2];
    $comando = "sudo php " . __DIR__ . "/mantenimientos.php eliminar-publicidades -c $centro";
    _echo("COMANDO: $comando");
    _echo(shell_exec($comando));
    exit();
} else if (strcmp($argv[1], 'eliminar-genericos-padre') === 0) {
    $centro = $argv[2];
    $comando = "sudo php " . __DIR__ . "/mantenimientos.php eliminar-genericos -c $centro";
    _echo("COMANDO: $comando");
    _echo(shell_exec($comando));
    exit();
}
