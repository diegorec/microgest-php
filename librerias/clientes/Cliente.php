<?php

class Cliente {

    protected $rutaLog;

    public function __construct() {
        global $basesdatos;
        $this->rutaLog = _getRutaLog("clientes-catalogo-");
        $this->db = new \Medoo\Medoo($basesdatos['catalogo']);
    }

}
