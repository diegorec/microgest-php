<?php

use phpcli\Colors;
use grcURL\Request;

class ClientesCatalogo {

    public $uri = 'microgest/cliente-catalogo';
    private $colors;
    private $rutaLog;

    public function __construct() {
        global $basesdatos;
        $this->colors = new Colors();
        $this->rutaLog = _getRutaLog("clientes-catalogo-");
        $this->db = new \Medoo\Medoo($basesdatos['catalogo']);
    }

    public function crear($comandos) {
        _echo("creamos el cliente");
        $usuarios = $this->retrieveUsuarios($comandos);
        if (!$usuarios) {
            throw new \Exception("[ClientesCatalogo] No se han encontrado cuentas asociadas. | " . json_encode($comandos));
        }
        foreach($usuarios as $u) {
            _var_dump($u->principal);
        }

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

    public function retrieveUsuarios(Array $comandos) {
        $centro = $comandos['centro'];
        $nocliente = $comandos['nocliente'];
        $subdivision = $comandos['subdivision'];
        $empresa = $comandos['empresa'];
        $cliente = $comandos['clientede'];
        $tipoCatalogo = 0;
        $urlCatalogo = REST_API . "$this->uri/$centro/$nocliente/$subdivision/$cliente/$empresa/$tipoCatalogo";
        _echo($this->colors->info("Consultando usuarios: $urlCatalogo"));
        $request = new Request($urlCatalogo, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $data = $request->get($comandos);
        if (isset($data->contenido) && is_array($data->contenido)) {
            return $data->contenido;
        }
    }

}
