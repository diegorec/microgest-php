<?php

use clientecatalogo\Login;
use clientecatalogo\objetos\Usuario;
use grcURL\Request;

/**
 * Description of Matriculas
 *
 * @author diego.gonda
 */
class Matriculas {

    private $usuario;

    public function __construct() {
        $this->usuario = new Usuario();
        $this->usuario->centro = "recalvi";
        $this->usuario->identidad = "diegogonda@recalvi.es";
        $this->usuario->contrasena = "chari";
        $this->usuario->empresa = "soledad";
    }

    public function _get($comandos) {
        $centro = $comandos['centro'];
        $actualiza = $comandos['suma'];
        $login = new Login($this->usuario);
        $login->url = SERVIDOR;
        $loginstr = $login->getLogin();
        $url = SERVIDOR . "mantenimiento/contadormatriculas/centro/$centro/sumador/$actualiza?$loginstr";
        _echo ($url);
        $ficheroLog = _getRutaLog();
        $request = new Request($url, $ficheroLog, LOGGERTAG);
        $request->_USERAGENT = USER_AGENT;
        $respuesta = $request->get();
        _var_dump($respuesta);
    }

}
