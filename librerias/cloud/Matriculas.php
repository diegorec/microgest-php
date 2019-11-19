<?php

namespace cloud;

class Matriculas {

    private $dbOrigen;
    private $dbDestino;
    private $tablaDestino = "catalogo_matriculas";

    public function __construct() {
        global $basesdatos;
        $this->dbOrigen = new \Medoo\Medoo($basesdatos['catalogo']);
        $this->dbDestino = new \Medoo\Medoo($basesdatos['cloud']);
    }

    public function migrar() {
        _echo("Inicio migración");
        $this->dbDestino->truncate($this->tablaDestino);
        $count = $this->dbOrigen->count("v_consultas_matriculas");
        $limit = 10000;
        $copiados = 0;
        _echo("Se copiarán: $count registros");
        for ($i = 0; $i <= $count; $i += $limit) {
            _echo("Copiando $limit registros desde el registro $i");
            $matriculas = $this->selectData($i, $limit);
            $copiados += count($matriculas);
            $this->dbDestino->insert($this->tablaDestino, $matriculas);
        }
        _echo("Se han copiado: $copiados registros");
    }

    private function selectData($start, $limit) {
        $join = [
            "[><]users(u)" => [
                "mat.id_users" => "id"
            ],
            "[><]users_info(i)" => [
                "mat.id_users" => "id_users"
            ]
        ];
        return $this->dbOrigen->select("v_consultas_matriculas(mat)", $join, [
                    "mat.id_users[Int]",
                    "u.email",
                    "mat.tipo",
                    "mat.codigo",
                    "mat.vehiculo[Int]",
                    "mat.grupo_montaje[Int]",
                    "i.almacen[Int]",
                    "i.nocliente[Int]",
                    "i.subdivision[Int]",
                    "mat.fecha"
                        ], [
                    "mat.fecha[>]" => '2017-12-01 00:00:00',
                    "ORDER" => [
                        "fecha" => "DESC"
                    ],
                    'LIMIT' => [$start, $limit]
        ]);
    }

}
