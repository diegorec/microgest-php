<?php


// php ~/alta-usuarios/index.php cliente-catalogo recalvi 502 0 0 0

include 'configuracion.php';
$permitidos = array('cliente-catalogo');

if (!(isset($argv[1]) && is_string($argv[1])
        && isset($argv[2]) && is_string($argv[2])
        && isset($argv[3]) && is_numeric($argv[3])
        && isset($argv[4]) && is_numeric($argv[4])
        && isset($argv[5]) && is_numeric($argv[5])
        && isset($argv[6]) && is_numeric($argv[6])
        && in_array ($argv[1], $permitidos, true))
        ) {
    echo 'Error' . PHP_EOL;
    exit;
}

$parametros = array(
    'centro' => $argv[2],
    'cliente' => $argv[3],
    'subdivision' => $argv[4],
    'cliente-externo' => $argv[5],
    'tipo-catalogo' => $argv[6]
);

var_dump(consulta(array('parametros' => $parametros, 'cabeceras' => array('Content-Type: application/json'))));

function consulta($parametros) {
    $clave = GR_JWT::recuperarClave();
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
                        )
                )
                    ));
    array_push($parametros ['cabeceras'], 'X-Gr-Key: ' . $jwt);
    return (new Consultas(SERVIDOR . 'admin/rest/usuario'))->post($parametros ['cabeceras'], array('usuario' => $parametros ['parametros']));
}
