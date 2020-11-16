<?php

class CacheRest {

    public function __construct() {
        global $basesdatos;
        $this->rutaLog = _getRutaLog("rest-");
        $this->db = new \Medoo\Medoo($basesdatos['rest']);
    }

    public function limpiarReferencias($comandos) {
        _var_dump($comandos);

        $list = $this->db->select('combos_referencia', [
            "clave",
            "tipo[Int]",
            "valor[JSON]"
        ], [
            "LIMIT" => $comandos['limit']
        ]);
        _var_dump(count($list));
        $detalles = 0;
        foreach($list as $key => $a) {
            $a = (object) $a;
            _echo_info("$key) Borrando equivalencias de $a->clave");
            foreach($a->valor as $k => $ref) {
                $detalles++;
                $informacion = $ref->caracteristicas->informacion;
                _echo_info("$key.$k) Borrando referencia de " . $informacion->articulo->nombre . " de la marca " . $informacion->marca->id);
            
                $this->db->delete('combos_referencia_detalles', [
                    "clave" => $informacion->articulo->nombre,
                    "marca" => $informacion->marca->id,
                ]);
            }
        }

        $list = $this->db->delete('combos_referencia', [
            "LIMIT" => $comandos['limit']
        ]);

        _echo_info("Equivalencias borradas: " . count($list));
        _echo_info("Detalles: " . $detalles);
    }

}