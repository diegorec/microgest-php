<?php

// ALTA: php ~/alta-usuarios/index.php cliente-catalogo recalvi 502 0
// BAJA:  php ~/alta-usuarios/index.php baja-cliente-catalogo recalvi 502 0

include __DIR__ . '/configuracion.php';
include __DIR__ . '/./vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$date = date('YW');
$name = "preproducción";
$logger = new Logger($name);
$logger->pushHandler(new StreamHandler(RUTA_COMANDOS_BASE . "$date.log", Logger::INFO));
$logger->pushHandler(new FirePHPHandler());
$comando = implode(' ', $argv);
$logger->addInfo("[$comando]");


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
    $comando = "php " . __DIR__ . "/mantenimientos.php clientes-catalogo -c $centro -n $nocliente -s $subdivision -e $empresa -cli $cliente";
    _echo("COMANDO: $comando");
    _echo(shell_exec($comando));
    exit();
}

$accion = 'cliente-catalogo';
$metodo = 'post';
//$metodo = $permitidos [$argv[1]];
foreach ($permitidos as $clave => $valor) {
    if (strcmp($clave, $argv[1]) === 0) {
        $accion = $clave;
        $metodo = $valor['method'];
        $path = $valor['path'];
    }
}

$parametros = array(
    'centro' => $argv[2],
    'cliente' => $argv[3],
    'subdivision' => $argv[4],
    'cliente-externo' => ((isset($argv[5])) ? $argv[5] : '0'),
    'tipo-catalogo' => '0' //$argv[6]
);
_var_dump(consulta(array('parametros' => $parametros, 'cabeceras' => array('Content-Type: application/json')), $metodo, $path));

function consulta($parametros, $method = 'post', $path = 'admin/rest/usuario') {
//    $clave = GR_JWT::recuperarClave();
    /*
     * Create the token as an array
     */
    $iss = 'aplicacion-alta-usuarios';
    $exp = time() + 70;
    //Para añadir más seguridad jti en se codifican los datos del issuer, exp y todos los datos de la consulta en codificados en JSON, que debe comprobarse que, realizando la misma comprobación debe dar el mismo resultado.
    $jwt = GR_JWT::encode(array(
                'iss' => $iss,
                'exp' => $exp,
                'jti' => hash('sha512', base64_encode(
                                json_encode(
                                        array(
                                            'iss' => $iss,
                                            'exp' => $exp,
                                            'datos' => $parametros['parametros']
                                        )
                                )
    ))));
    array_push($parametros ['cabeceras'], 'X-Gr-Key: ' . $jwt);
    $url = SERVIDOR . $path;
    _echo("Lanzando consulta");
    _echo("url: $url");
    _echo("X-Gr-Key: $jwt");
    _var_dump($parametros);
    return (new Consultas($url))->$method($parametros ['cabeceras'], array('usuario' => $parametros ['parametros']), $url);
}
