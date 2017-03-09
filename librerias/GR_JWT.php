<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../BaseDatos.php';

class GR_JWT extends \Firebase\JWT\JWT {

    public static function encode($payload) {
        return parent::encode($payload, self::recuperarClave());
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
        return '0d065aaee39dcdb5cc06def6526f9399ec8da19abce841b0f60eb8892ce346856d732d901274456f89a5f421354fd0abdf62943ebba88c14134ca37b79cd7b4d';//(new BaseDatos())->select_row('SELECT clave FROM claves WHERE activa = 1 ORDER BY RAND() LIMIT 1')['clave'];
    }

}