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

    public function getUsersByEmail (string $email, int $centro) {
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

    public function updateUser(int $id, Array $data, Array $info) {
        $database = $this->db;
        return $database->action(function($database) use ($id, $data, $info) {
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

    public function createUser(Array $user, Array $info) {
        $database = $this->db;
        return $database->action(function($database) use ($user, $info) {
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
            return true;
        });
    }

    public function selectAccounts(Array $user) {
        return $this->db->debug()->select('v_users', "*", $user);
    }

}