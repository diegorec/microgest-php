<?php

class Representantes  extends Cliente {

    use UsersTrait;
    use APITrait;

    public function crear(array $comandos) {
        _var_dump($comandos);
        $idCentro = $this->getCentroId($comandos['centro']);
        if (!$idCentro) {
            throw new \Exception("[ClientesCatalogo] Centro \"" . $comandos['centro'] . "\" no encontrado.");
        }

        $representantes = $this->getUsersByRepresentant((int) $comandos['representante'], (int) $idCentro);
        if(!$representantes) {
            throw new \Exception("[Representantes] No se han encontrado usuarios el id de representante: " .$comandos['representante']);
        }
        foreach($representantes as $representante) {
            $representante['centro'] = $comandos['centro'];
            $representados = $this->retrieveRepresentantes($representante);
            _echo_info("insertando " . count($representados) . " representados al usuario con id: " . $representante['id']);
            $this->insertarRepresentados($representante['id'], $representados);
        }
    }

}
