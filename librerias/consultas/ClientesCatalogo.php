<?php

use grcURL\Request;
use phpcli\Colors;

class ClientesCatalogo {

    public $parametrosLogin = array(
        "correo" => "diegogonda@recalvi.es",
        "centro" => "recalvi",
        "pass" => "chari",
        "empresa" => "internos");
    public $uri = 'admin/rest/usuario';
    private $loginToken;
    private $colors;

    public function __construct() {
        $login = new LoginCatalogo();
        $this->loginToken = $login->generar($this->parametrosLogin, true);
        $this->colors = new Colors();
    }

    public function crear($comandos) {
        $urlCatalogo = SERVIDOR . "$this->uri?$loginToken";
        $request = new Request($urlCatalogo, _getRutaLog());
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $this->request->post($comandos);
        _var_dump($respuesta);
    }

    public function eliminar($comandos) {
        _var_dump($comandos);
       
        $params = [];
        foreach ($comandos as $clave => $valor) {
            $params [] = "$clave/$valor";
        }
        $paramsString = implode('/', $params);
        $urlCatalogo = SERVIDOR . "$this->uri/$paramsString?$this->loginToken";
        _echo($this->colors->info($urlCatalogo));
        $request = new Request($urlCatalogo, _getRutaLog());
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $request->delete();
        _var_dump($respuesta);
    }

}
