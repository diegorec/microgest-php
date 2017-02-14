<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseDatos
 *
 * @author diego.gonda
 */
class BaseDatos {

    private $servidor;
    private $usuario;
    private $contrasena;
    private $base_datos;
    private $mysqli;
    private $consulta;
    private $query;

    public function __construct() {
        $this->servidor = "localhost";
        $this->usuario = "diego";
        $this->contrasena = ".#diego#.";
        $this->base_datos = "API-Usuarios";
        $this->consulta = null;
    }

    public function encode($valor) {
        return json_encode($valor, JSON_UNESCAPED_UNICODE);
    }

    public function decode($valor) {
        return json_decode($valor);
    }

    public function lanzarQuery($query = null) {
        if (!is_null($query)){
            $this->consulta = $query;
        }
        if (!is_null($this->consulta) && $this->conectar()) {
            $this->query = $this->mysqli->query($this->consulta);
            return ($this->query) ? $this->query: false;
        }
        return false;
    }
    
    public function select_row ($query = null) {
        return $this->lanzarQuery($query)->fetch_assoc();
    }

    private function conectar() {
        $this->mysqli = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->base_datos);
        $this->mysqli->set_charset("utf8");
        if ($this->mysqli->connect_errno) {
            echo "Fallo al conectar a MySQL: (" . $this->mysqli->connect_errno . ") " . $this->mysqli->connect_error;
            return false;
        }
        return true;
    }

    private function desconectar() {
        return $this->mysqli->close();
    }

}
