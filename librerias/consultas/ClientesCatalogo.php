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
    private $rutaLog;

    public function __construct() {
        $login = new LoginCatalogo();
        $this->loginToken = $login->generar($this->parametrosLogin, true);
        _echo("token-l: $this->loginToken");
        $this->colors = new Colors();
        $this->rutaLog = _getRutaLog("clientes-catalogo-");
    }

    public function crear($comandos) {
        _echo("creamos el cliente");
        _var_dump($comandos);
        $urlCatalogo = SERVIDOR . "$this->uri?$this->loginToken";
        _echo($urlCatalogo);
        $request = new Request($urlCatalogo, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $request->post($comandos);
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
        $request = new Request($urlCatalogo, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $request->delete();
        _var_dump($respuesta);
    }

}
