<?php

use Medoo\Medoo;

class Cache {

    public $tabla= "cache";
    public $tag;
    public $basedatos;
    public $rutaLog;

    public function __construct() {
        global $basesdatos;
        $this->basedatos = new Medoo($basesdatos['catalogo']);
    }


    public function _eliminar($comandos) {
        _var_dump($comandos);
        $key = $this->tag . "_" .$comandos['centro'];
        $this->basedatos->delete($this->tabla, [
            "clave[~]" => "%$key%"
        ]);
    }
    
    public function setTag($tag) {
        $this->tag = $tag;
    }
    
}
