<?php

use convertidores\CSVHandler;
use phpcli\Colors;

class ClientesCatalogo {

    private $colors;
    private $rutaLog;

    use UsersTrait;
    use MG2CatalogoTrait;
    use LogTrait;
    use APITrait;

    public function __construct() {
        global $basesdatos;
        $this->colors = new Colors();
        $this->rutaLog = _getRutaLog("clientes-catalogo-");
        $this->db = new \Medoo\Medoo($basesdatos['catalogo']);
    }

    public function crear($comandos) {
        _echo("creamos el cliente");
        $idCentro = $this->getCentroId($comandos['centro']);
        if (!$idCentro) {
            throw new \Exception("[ClientesCatalogo] Centro \"" . $comandos['centro'] . "\" no encontrado.");
        }

        $usuarios = $this->retrieveUsuarios($comandos);
        if (!$usuarios) {
            throw new \Exception("[ClientesCatalogo] No se han encontrado cuentas asociadas. | " . json_encode($comandos));
        }

        _echo_info("ID Centro: $idCentro");

        foreach ($usuarios as $u) {
            $email  = $u->principal->email;
            $usersId = $this->getUsersByEmail($email, $idCentro);
            $principal = $this->extractUser2BD($u->principal);
            $info = $this->extractInfo2BD($u->info);
            if ($usersId) {
                foreach ($usersId as $id) {
                    _echo_info("Actualizando usuario: $id");
                    $modified = $this->updateUser($id, $principal, $info);
                    if (!$modified) {
                        $msg = "[ClientesCatalogo] No se ha podido actualizar el cliente. | $email | " . json_encode($comandos);
                        _echo_error($msg);
                        $this->addError($msg);
                    }
                }
            } else {
                _echo_info("# Creando usuario: $email");
                $principal['email'] = $email;
                $info["centro"] = $idCentro;
                $info["nocliente"] = $u->info->nocliente;
                $usersId = $this->createUser($principal, $info);
                _echo_error($usersId);
                if (!$usersId) {
                    $msg = "[ClientesCatalogo] No se ha podido crear el cliente. | $email | " . json_encode($comandos);
                    _echo_error($msg);
                    $this->addError($msg);
                }
            }
            if ($usersId && isset($u->info->operador) && $u->info->operador > 0) {
                $id = is_array($usersId) ? $usersId[0] : $usersId;
                _echo_info("# El id cliente {$id} es operador, consultando sus representados.");
                $copia = $comandos;
                $copia['operador'] = $u->info->operador;
                $representados = $this->retrieveRepresentantes($copia);
                $this->insertarRepresentados((int) $id, $representados);
            } else if ($usersId && isset($u->info->ver_subdivisiones) && $u->info->ver_subdivisiones === 1) {
                $id = is_array($usersId) ? $usersId[0] : $usersId;
                _echo_info("# El id cliente {$id} ve subdivisiones.");
                $copia = $comandos;
                $copia['operador'] = $u->info->operador;
                $subdivisiones = $this->retrieveSubdivisiones($copia);
                $this->insertarRepresentados((int) $id, $subdivisiones);
            }
        }
    }

    public function eliminar($comandos) {
        _echo_info("Desactivado de cliente");
        $cuentas = $this->selectAccounts($comandos);
        foreach ($cuentas as $value) {
            $cuenta = (object) $value;
            _echo_info("Se desactiva el id: $cuenta->id_users");
            $activo =  $this->desactivar($cuenta->id_users);
            if (!$activo) {
                $msg = "[ClientesCatalogo] No se ha podido desactivar el cliente. | $cuenta->email | " . json_encode($comandos);
                _echo_error($msg);
                $this->addError($msg);
            }
        }
    }

    public function cuentas($comandos) {
        _var_dump($comandos);
        $rutaDestino = $comandos['fichero-destino'];

        $comandos['id_centro'] = $this->getCentroId($comandos['centro']);

        unset($comandos['fichero-destino']);
        unset($comandos['centro']);
        $cuentas = $this->selectAccounts($comandos);

        $map = array_map(function ($c) {
            return [
                "email" => $c['email'],
                "centro" => $c['centro'],
                "nocliente" => $c['nocliente'],
                "subdivision" => $c['subdivision'],
                "empresa" => $c['empresa'],
                "cliente_de_cliente" => $c['cliente_de_cliente'],
                "con_tecdoc" => $c['con_tecdoc'],
                "con_matriculas" => $c['con_matriculas'],
            ];
        }, $cuentas);
        _var_dump($map);

        $csv = new CSVHandler();
        $csv->array2file($rutaDestino, $map);
    }
}
