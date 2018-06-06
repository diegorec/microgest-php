<?php

use grcURL\Request;

class ClientesCatalogo {

    public $parametrosLogin = array(
        "correo" => "diegogonda@recalvi.es",
        "centro" => "recalvi",
        "pass" => "chari",
        "empresa" => "internos");
    
    public $uri = 'admin/rest/usuarioaaa';

    public function generar($comandos) {
        $login = new LoginCatalogo();
        $loginToken = $login->generar($this->parametrosLogin, true);
        $urlCatalogo = SERVIDOR . "$this->uri?$loginToken";
        
        $request = new Request($urlCatalogo, _getRutaLog());
        $request->_USERAGENT = USER_AGENT;
        $respuesta = $request->post($comandos);
        _var_dump($respuesta);
    }

}
