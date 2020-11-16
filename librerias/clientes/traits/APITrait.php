<?php

use grcURL\Request;

trait APITrait {

    public $uriclientes = 'microgest/cliente-catalogo';
    public $urirepresentantes = 'microgest/clientes-representantes';
    public $urisubdivisiones = 'microgest/subdivisiones-cliente';

    public function retrieveUsuarios(Array $comandos) {
        $centro = $comandos['centro'];
        $nocliente = $comandos['nocliente'];
        $subdivision = $comandos['subdivision'];
        $empresa = $comandos['empresa'];
        $cliente = $comandos['clientede'];
        $tipoCatalogo = 0;
        $urlCatalogo = REST_API . "$this->uriclientes/$centro/$nocliente/$subdivision/$cliente/$empresa/$tipoCatalogo";
        _echo_info("Consultando usuarios: $urlCatalogo");
        return $this->retrieve($urlCatalogo);
    }

    public function retrieveRepresentantes(Array $comandos) {
        $centro = $comandos['centro'];
        $nocliente = $comandos['nocliente'];
        $subdivision = $comandos['subdivision'];
        $empresa = $comandos['empresa'];
        $cliente = $comandos['clientede'];
        $representante = $comandos['representante'];
        $operador = $comandos['operador'];
        $urlCatalogo = REST_API . "$this->urirepresentantes/$centro/$nocliente/$subdivision/$cliente/$empresa/$representante/$operador";
        _echo_info("Consultando operador: $urlCatalogo");
        return $this->retrieve($urlCatalogo);
    }

    public function retrieveSubdivisiones(Array $comandos) {
        $centro = $comandos['centro'];
        $nocliente = $comandos['nocliente'];
        $subdivision = $comandos['subdivision'];
        $empresa = $comandos['empresa'];
        $cliente = $comandos['clientede'];
        $urlCatalogo = REST_API . "$this->urisubdivisiones/$centro/$nocliente/$subdivision/$cliente/$empresa";
        _echo_info("Consultando subdivisiones: $urlCatalogo");
        return $this->retrieve($urlCatalogo);
    }

    public function retrieve(string $url) {
        $request = new Request($url, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $data = $request->get($url);
        if (isset($data->contenido) && is_array($data->contenido)) {
            return $data->contenido;
        }
    }

    
}