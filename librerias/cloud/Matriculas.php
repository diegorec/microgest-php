<?php

namespace cloud;

class Matriculas {

    private $dbOrigen;
    private $dbDestino;
    private $tablaDestino = "catalogo_matriculas";
    private $date;

    public function __construct() {
        global $basesdatos;
        $this->dbOrigen = new \Medoo\Medoo($basesdatos['catalogo']);
        $this->date = date('Y-m-d', strtotime("-1 day"));
    }

    public function generateCloudDatabaseConexion($centro) {
        global $basesdatos;
        if ($centro === 1) {
            $this->dbDestino = new \Medoo\Medoo($basesdatos['cloud']);
        } else if ($centro === 9) {
            $this->dbDestino = new \Medoo\Medoo($basesdatos['cloud_norte']);
        } else if ($centro === 3) {
            $this->dbDestino = new \Medoo\Medoo($basesdatos['cloud_prisauto']);
        } else if ($centro === 14) {
            $this->dbDestino = new \Medoo\Medoo($basesdatos['cloud_canarias']);
        }
    }

    public function migrar($parametros) {
        _echo("Inicio migración");
        _var_dump($parametros);
        $centro = $parametros['centro'];
        $this->generateCloudDatabaseConexion(intval($centro));
        if (isset($parametros['fecha'])) {
            if ($parametros['fecha'] === "todas") {
                $this->date = false;
            } else if (\DateTime::createFromFormat('Y-m-d', $parametros['fecha']) === FALSE) {
                throw new \Exception("Formato de fecha incorrecto");
            } else {
                $this->date = $parametros['fecha'];
            }
        }
        _echo("Centro: $centro");
        _echo("Fecha: $this->date");

        $matriculas = $this->selectData("matricula", "consultas_matriculas", $centro);
        $bastidores = $this->selectData("bastidor", "consultas_bastidores", $centro);

        $this->insertarData($matriculas);
        $this->insertarData($bastidores);
    }

    private function insertarData($datos) {
        foreach ($datos as $key => $value) {
            $data = [];
            foreach ($value as $k => $v) {
                if (is_string($k)) {
                    $data[$k] = $v;
                }
            }
            $matricula = (object) $value;
            _echo("$key> INSERTANDO $matricula->tipo: $matricula->codigo {$matricula->fecha}");
            $this->dbDestino->insert($this->tablaDestino, $data);
        }
    }

    private function selectData($tipo, $tabla, $centro) {
        $query = "SELECT 
                                            c.id_users,
                                            u.email,
                                            '$tipo' AS tipo,
                                            c.$tipo AS codigo,
                                            c.vehiculo,
                                            c.grupo_montaje,
                                            i.almacen,
                                            i.nocliente,
                                            i.subdivision,
                                            c.fecha
                                        FROM $tabla c
                                        JOIN users u ON u.id = c.id_users
                                        JOIN users_info i ON c.id_users = i.id_users
                                        WHERE 
                                            i.centro = $centro";
        if ($this->date) {
            $query .= " AND DATE(fecha) = DATE('$this->date')";
        }
        return $this->dbOrigen->query($query);
    }

}
