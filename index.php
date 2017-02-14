<?php

require 'vendor/autoload.php';
require 'BaseDatos.php';

use \Firebase\JWT\JWT;

class GR_JWT extends \Firebase\JWT\JWT {

    private $tiempoVida = 3;
    private $key;
    private $bd;

    public static function encode($payload) {
        $this->key = GR_JWT::recuperarClave();
        return $this->key;
        return parent::encode($payload, $this->key);
    }

    public static function decode($jwt, $key) {
        return parent::decode($jwt, $key);
    }

    /**
     * Colección de datos generados aleatoriamente con el que se obtendrán claves univocas.
     */
    public static function crearClave() {
        $clave = array(
            'time' => microtime(),
            'cl' => sha1($_SERVER["LESSCLOSE"]),
            'po' => sha1($_SERVER["OLDPWD"]),
            'np' => gmp_strval(gmp_nextprime(intval(microtime(true))))
        );
        $hash = hash('sha512', json_encode($clave));
        (new BaseDatos())->lanzarQuery('insert into claves (clave, activa) values ("' . $hash . '", 1)');
        return $hash;
    }

    /**
     * Devuelve una de las las claves que actualmente están activas en el sistema.
     */
    public static function recuperarClave() {
        return (new BaseDatos())->select_row('SELECT clave FROM claves WHERE activa = 1 ORDER BY RAND() LIMIT 1')['clave'];
    }

}

$token = array(
    "iss" => "http://example.org",
    "aud" => "http://example.com",
    "iat" => 1356999524,
    "nbf" => 1357000000
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
//$jwt = (new GR_JWT)->encode($token);
//
//echo $jwt . PHP_EOL;
//$decoded = JWT::decode($jwt, $key, array('HS256'));
//
//print_r($decoded);
//$server = filter_input(INPUT_SERVER, $_SERVER["XDG_RUNTIME_DIR"]);
//var_dump($server);
//echo gmp_strval(gmp_nextprime(intval(microtime(true)))) . PHP_EOL;
//var_dump( GR_JWT::recuperarClave() ); echo PHP_EOL;


$xml = '<S:Envelope xmlns:S="http://schemas.xmlsoap.org/soap/envelope/">
   <S:Body>
      <ns2:buscarClienteCatalogosResponse xmlns:ns2="http://ws/">
         <return>
            <catalogoonline>
               <almacen>01</almacen>
               <contecdoc>1</contecdoc>
<emailconfirmacion>chari@recalvi.es</emailconfirmacion>
               <solonetos>1</solonetos>
               <vacesta>1</vacesta>
               <veestadisticas>1</veestadisticas>
               <vefacturacion>1</vefacturacion>
            </catalogoonline>
            <cliente>
               <cif/>
               <codigo>000502</codigo>
               <direccion/>
               <emailfact/>
               <fax/>
               <ibanRecalvi/>
               <nombre>JORGE ALFONSO- RECALVI</nombre>
               <poblacion/>
               <previopago/>
               <provincia/>
               <razonsocial/>
               <subdivision>00</subdivision>
               <tarifa/>
               <tarifadto/>
               <telefono/>
               <tipo/>
            </cliente>
            <password>chari</password>
            <tipo_catalogo>0</tipo_catalogo>
            <usuario>chari@recalvi.es</usuario>
         </return>
      </ns2:buscarClienteCatalogosResponse>
   </S:Body>
</S:Envelope>';
//var_dump($xml);
//$centro= $argv[1];
$centro = 'recalvi';


$envelope = 'S-Envelope';
$body = 'S-Body';
$buscarClienteCatalogosResponse = 'ns2-buscarClienteCatalogosResponse';
$usuario = json_decode(Zend\Xml2Json\Xml2Json::fromXml(trim(str_replace(':', '-', $xml)), true))->$envelope->$body->$buscarClienteCatalogosResponse->return;

$catalogo = $usuario->catalogoonline;
$cliente = $usuario->cliente;
$password = $usuario->password;
$tipoCatalogo = $usuario->tipo_catalogo;
$email = $usuario->usuario;
//var_dump($cliente);

$consulta = new stdClass();
$users = new stdClass();
$users->email = $email;
$users->contrasena = $password;
$users->nombre = $cliente->nombre;
$users->apellidos = '';
$users->empresa = $centro;


$info = new stdClass();
$info->centro = $centro;
$info->nocliente = $cliente->codigo;
$info->subdivision = $cliente->subdivision;
$info->almacen = $catalogo->almacen;
$info->email_confirmacion = $catalogo->emailconfirmacion;
$info->va_a = (intval($catalogo->vacesta) === 1) ? 'cesta' : 'mecanica';
$info->sin_tecdoc = (intval($catalogo->contecdoc) === 1) ? 0 : 1;
$info->ver_estadisticas = $catalogo->veestadisticas;
$info->solo_netos = $catalogo->solonetos;


//var_dump($users);

$consulta->principal = $users;
$consulta->info = $info;

//var_dump($consulta);
$respuesta = consulta(array('parametros' => array ('centro' => 'recalvi', 'cliente' => 502, 'subdivision' => 0, 'cliente-externo' => 0, 'tipo-catalogo' => 0), 'cabeceras' => array('Content-Type: application/json')));

var_dump($respuesta);

function consulta($parametros) {
    $clave = GR_JWT::recuperarClave();
    /*
     * Create the token as an array
     */
    $iss = 'aplicacion-alta-usuarios';
    $exp = time() + 70;
    //Para añadir más seguridad jti en se codifican los datos del issuer, exp y todos los datos de la consulta en codificados en JSON, que debe comprobarse que, realizando la misma comprobación debe dar el mismo resultado.
    $jwt = JWT::encode(array(
                'iss' => $iss,
                'exp' => $exp,
                'jti' => hash('sha512', base64_encode(
                        json_encode(
                                array (
                                    'iss' => $iss, 
                                    'exp' => $exp, 
                                    'datos' => $parametros['parametros']
                                    )
                                )
                        )
                        )
        ), $clave);
    array_push($parametros ['cabeceras'], 'X-Gr-Key: ' . $jwt);
    $request = new \cURL\Request('http://192.168.1.4/d_catalogo_online/admin/rest/usuario');
    $request->getOptions()
            ->set(CURLOPT_POST, true)
            ->set(CURLOPT_RETURNTRANSFER, true)
            ->set(CURLOPT_SSL_VERIFYPEER, false)
            ->set(CURLOPT_SSL_VERIFYHOST, false)
            ->set(CURLOPT_HTTPAUTH, CURLAUTH_BASIC)
            ->set(CURLOPT_TIMEOUT, 10)
            ->set(CURLOPT_CONNECTTIMEOUT, 10)
            ->set(CURLOPT_POSTFIELDS, json_encode(array('usuario' => $parametros ['parametros'])))
            ->set(CURLOPT_HTTPHEADER, $parametros ['cabeceras']);
    return $request->send()->getContent();
}
