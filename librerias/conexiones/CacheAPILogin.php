<?php

class CacheAPILogin {


    public function __construct() {
        global $basesdatos;
        $this->db = new \Medoo\Medoo($basesdatos['catalogo']);        
    }
    public function limpiar ($comandos) {
        _var_dump($comandos);

        $where = [
            "c.nombre" => $comandos['centro']
        ];
        if ($comandos['centro'] === 'recalvi') { // realmente hay que borrar o los del 50 o los de prisauto. No more.
            _echo_info("Borramos los datos del 50");
            $where = [
                "c.nombre[!]" => 'prisauto'
            ];
        } else {
            _echo_info("Borramos los datos de prisauto");
        }

        $users = $this->db->select("users_info(ui)", [
            "[><]centros(c)" => ['ui.centro' => 'id_']
        ], "id_users", $where);
        
        _echo_info("Se borran los token de " . count($users) . " usuarios");
        $this->db->delete("cache_api_login", [
            "id_users" => $users
        ]);
    }
}