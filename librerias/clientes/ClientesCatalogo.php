<?php

use grcURL\Request;
use phpcli\Colors;

class ClientesCatalogo {

    public $uri = 'microgest/cliente-catalogo';
    private $colors;
    private $rutaLog;

    use UsersTrait;
    use MG2CatalogoTrait;
    use LogTrait;

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
            throw new \Exception("[ClientesCatalogo] Centro \"" . $comandos['centro'] ."\" no encontrado.");
        }

        $usuarios = $this->retrieveUsuarios($comandos);
        if (!$usuarios) {
            throw new \Exception("[ClientesCatalogo] No se han encontrado cuentas asociadas. | " . json_encode($comandos));
        }

        _echo_info("ID Centro: $idCentro");

        foreach($usuarios as $u) {
            $email  = $u->principal->email;
            $usersId = $this->getUsersByEmail($email, $idCentro);
            $principal = $this->extractUser2BD($u->principal);
            $info = $this->extractInfo2BD($u->info);
            if($usersId) {
                foreach ($usersId as $id) {
                    _echo_info ("Actualizando usuario: $id");
                    $modified = $this->updateUser($id, $principal, $info);
                    if(!$modified) {
                        $msg = "[ClientesCatalogo] No se ha podido actualizar el cliente. | $email | " . json_encode($comandos);
                        _echo_error($msg);
                        $this->addError($msg);                    
                    }
                }
            } else {
                _echo_info ("# Creando usuario: $email");
                $principal['email'] = $email;
                $info["centro"] = $idCentro;
                $info["nocliente"] = $u->info->nocliente;
                $created = $this->createUser($principal, $info);
                if(!$created) {
                    $msg = "[ClientesCatalogo] No se ha podido crear el cliente. | $email | " . json_encode($comandos);                    
                    _echo_error($msg);
                    $this->addError($msg);                    
                }
            }        
        }
    }

    public function eliminar($comandos) {
        _var_dump($comandos);

        $params = [];
        foreach ($comandos as $clave => $valor) {
            $params [] = "$clave/$valor";
        }
        $paramsString = implode('/', $params);
        $urlCatalogo = SERVIDOR . "$this->uri/$paramsString?$this->loginToken";
        _echo_info($urlCatalogo);
        $request = new Request($urlCatalogo, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $respuesta = $request->delete();
        _var_dump($respuesta);
    }

    public function retrieveUsuarios(Array $comandos) {
        $centro = $comandos['centro'];
        $nocliente = $comandos['nocliente'];
        $subdivision = $comandos['subdivision'];
        $empresa = $comandos['empresa'];
        $cliente = $comandos['clientede'];
        $tipoCatalogo = 0;
        $urlCatalogo = REST_API . "$this->uri/$centro/$nocliente/$subdivision/$cliente/$empresa/$tipoCatalogo";
        _echo_info("Consultando usuarios: $urlCatalogo");
        $request = new Request($urlCatalogo, $this->rutaLog);
        $request->_USERAGENT = USER_AGENT;

        $data = $request->get($comandos);
        if (isset($data->contenido) && is_array($data->contenido)) {
            return $data->contenido;
        }
    }

}
