<?php

// ALTA: php ~/alta-usuarios/index.php cliente-catalogo recalvi 502 0
// BAJA:  php ~/alta-usuarios/index.php baja-cliente-catalogo recalvi 502 0

include 'configuracion.php';
$permitidos = array(
    'cliente-catalogo'  => array ('method' => 'post', 'path' => 'admin/rest/usuario'),
    'baja-cliente-catalogo' => array ('method' => 'delete'),
    'eliminar-genericos-padre' => array ('method' => 'post', 'path' => 'mantenimiento/equivalencias'),
    'eliminar-publicidades' => array ('method' => 'post', 'path' => 'mantenimiento/publicidades'),
);

if (!(isset($argv[1]) && is_string($argv[1]) 
        && isset($argv[2]) && is_string($argv[2]) 
        && isset($argv[3])  
        && isset($argv[4]) && is_string($argv[4])
//        && isset($argv[5]) && is_numeric($argv[5])
//        && isset($argv[6]) && is_numeric($argv[6])
        && !is_null($permitidos[$argv[1]]))) {
    echo 'Error' . PHP_EOL;
    exit;
}

$accion = 'cliente-catalogo';
$metodo = 'post';
//$metodo = $permitidos [$argv[1]];
foreach ($permitidos as $clave => $valor) {
    if (strcmp($clave, $argv[1]) === 0){
        $accion = $clave;
        $metodo = $valor['method'];
        $path = $valor['path'];
    }
}

$parametros = array(
    'centro' => $argv[2],
    'cliente' => $argv[3],
    'subdivision' => $argv[4],
    'cliente-externo' => ((isset ($argv[5]))? $argv[5]: '0'),
    'tipo-catalogo' => '0' //$argv[6]
);
consulta(array('parametros' => $parametros, 'cabeceras' => array('Content-Type: application/json')), $metodo, $path);

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
    return (new Consultas($url))->$method($parametros ['cabeceras'], array('usuario' => $parametros ['parametros']), $url);
}
