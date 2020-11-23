<?php

trait UsersTrait {

    public function getCentroId(string $name) {
        $id = $this->db->get("centros", "id_", [
            "nombre" => $name
        ]);
        if (is_numeric($id)) {
            return (int) $id;
        }
    }

    public function getUsersByEmail(string $email, int $centro) {
        $users = $this->db->select("users", "id", [
            "email" => $email
        ]);
        // log si tiene más de 1 id asociado

        $info = $this->db->select("users_info", "id_users(id)[Int]", [
            "centro" => $centro,
            "id_users" => $users
        ]);
        // log si tiene más de 1 id asociado
        if (is_array($info) && count($info) > 0) {
            return $info;
        }
    }

    public function getUsersByRepresentant(int $representante, int $centro) {
        $info = $this->db->select("users_info", [
            'id_users(id)[Int]',
            'nocliente[Int]',
            'subdivision[Int]',
            'empresa[Int]',
            'cliente_de_cliente(clientede)[Int]',
            'n_operador(operador)[Int]',
            'n_representante(representante)[Int]'
        ], [
            "centro" => $centro,
            "n_representante" => $representante
        ]);
        // log si tiene más de 1 id asociado
        if (is_array($info) && count($info) > 0) {
            return $info;
        }
    }

    public function updateUser(int $id, array $data, array $info) {
        $database = $this->db;
        return $database->action(function ($database) use ($id, $data, $info) {
            $this->db->update("users", $data, [
                "id" => $id
            ]);
            $this->db->update("users_info", $info, [
                "id_users" => $id
            ]);
            if (!$database->has("v_users", ["id_users" => $id])) {
                _echo_error("Fallo en la actualización para el usuario con id: $id");
                return false;
            }
            return true;
        });
    }

    public function createUser(array $user, array $info) {
        $database = $this->db;
        return $database->action(function ($database) use ($user, $info) {
            $database->insert("users", $user);

            $id = $database->id();
            _echo_info("Creado usuario '" . $user['email'] . "' con id: $id");

            $database->insert("users_groups", [
                "user_id" => $id,
                "group_id" => 2 // group por defecto
            ]);

            $info["id_users"] = $id;
            $database->insert("users_info", $info);

            if (!$database->has("v_users", ["id_users" => $id])) {
                _echo_error("Fallo en la creación para el usuario con email '" . $user['email'] . "' e id: $id");
                return false;
            }
            return $id;
        });
    }

    public function selectAccounts(array $user) {
        return $this->db->select('v_users', "*", $user);
    }

    public function insertarRepresentados(int $id, array $representados) {
        if ($this->db->has("users_representados", ["id_users" => $id])) {
            $this->db->delete("users_representados", [
                "id_users" => $id
            ]);
        }
        if ($this->db->has("users_representa", ["id_users" => $id])) {
            $this->db->delete("users_representa", [
                "id_users" => $id
            ]);
        }
        $map = array_map(function ($u) {
            $u->id = $u->codigo;
            $u->nocliente = $u->codigo;
            $u->nombre = str_replace(['\"', '\''], ['&#34;', '&#39;'], $u->nombre);
            unset($u->codigo);
            $u->centro = "recalvi";
            return $u;

        }, $representados);
        $this->db->insert("users_representados", [
            "id_users" => $id,
            "valor[JSON]" => $map
        ]);
    }

    public function desactivar(int $id) {
        $this->db->update("users", [
            "active" => 0
        ], [
            "id" => $id
        ]);
        return $this->db->has("v_users", ["id_users" => $id]);
    }

    public function limpiar(int $usersId) {
        $this->db->delete("users_representados", [
            "id_users" => $usersId
        ]);
        $this->db->delete("users_representa", [
            "id_users" => $usersId
        ]);
    }
}
