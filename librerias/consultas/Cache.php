<?php

use grcURL\Request;

class Cache {

    public $parametrosLogin = array(
        "correo" => "diegogonda@recalvi.es",
        "centro" => "recalvi",
        "pass" => "chari",
        "empresa" => "internos");
    public $uri;

    public function __construct() {
        $login = new LoginCatalogo();
        $this->loginToken = $login->generar($this->parametrosLogin, true);
        _echo("token-l: $this->loginToken");
    }

    public function _eliminar($comandos) {
        _var_dump($comandos);
        $params = [];
        foreach ($comandos as $clave => $valor) {
            $params [] = "$clave/$valor";
        }
        $paramsString = implode('/', $params);
        $urlCatalogo = SERVIDOR . "$this->uri/$paramsString?$this->loginToken";
        _echo($urlCatalogo);
        $request = new Request($urlCatalogo, _getRutaLog());
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $request->delete();
        _var_dump($respuesta);
    }

    public function getUri() {
        return $this->uri;
    }

    public function setUri($uri) {
        $this->uri = $uri;
    }

}
