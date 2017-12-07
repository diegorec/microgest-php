<?php
use conexiones\ConexionesToken as cconexiones;
/**
 * Description of Matriculas
 *
 * @author diego.gonda
 */
class Matriculas {

    public function __construct() {
        
    }

    public function _get($comandos) {
        $centro = $comandos['centro'];
        $actualiza = $comandos['suma'];
        $respuesta = (new cconexiones)->blocking1x1(array (SERVIDOR . "mantenimiento/contadormatriculas/centro/$centro/sumador/$actualiza"));
        _var_dump($respuesta);
    }
    
}
