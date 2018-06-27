<?php

use grcURL\Request;

/**
 * Description of Matriculas
 *
 * @author diego.gonda
 */
class Matriculas {

    private $usuario;
    public $tokenPublico = CLAVELOGIN;

    public function __construct() {
        $this->usuario = array(
            'centro' => "recalvi", 
            "correo" => "diegogonda@recalvi.es", 
            "pass" => "chari", 
            "empresa" => 0
        );
    }
    
    public function _get($comandos) {
        $centro = $comandos['centro'];
        $actualiza = $comandos['suma'];
        $login = new LoginCatalogo();
        $loginstr = $login->generar($this->usuario);
        $url = SERVIDOR . "mantenimiento/contadormatriculas/centro/$centro/sumador/$actualiza?$loginstr";
        _echo ($url);
        $ficheroLog = _getRutaLog();
        $request = new Request($url, $ficheroLog, LOGGERTAG);
        $request->_USERAGENT = USER_AGENT;
        $respuesta = $request->get();
        _var_dump($respuesta);
    }

}
