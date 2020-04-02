<?php

namespace librerias\matriculas;

use convertidores\CSVHandler;

class ModificarMatriculas {

    private $ruta;
    private $esSuma;
    private $centro;
    private $db;
    private $tabla = "users_matriculas_restantes";

    public function index(Array $config) {
        global $basesdatos;
        if (!isset($config['suma'], $config['ruta'], $config['centro'])) {
            throw new \Exception("[ModificarMatriculas] No se indican los campos correctos");
        }
        if (!isset($basesdatos['catalogo'])) {
            throw new \Exception("[ModificarMatriculas] Debes configurar la base de datos del catalogo");
        }
        if ($config['suma'] !== '1' && $config['suma'] !== '0') {
            throw new \Exception("[ModificarMatriculas] -s solo admite 0 y 1 como valores");
        }
        if (!file_exists($config['ruta']) || !is_readable($config['ruta'])) {
            throw new \Exception("[ModificarMatriculas] -r debe indicar un fichero válido y legible");
        }
        $this->centro = $config['centro'];
        $this->esSuma = $config['suma'];
        $this->ruta = $config['ruta'];
        $this->db = new \Medoo\Medoo($basesdatos['catalogo']);

        $this->execute();
    }

    public function execute() {
        $csv = new CSVHandler();
        $matriculas = $csv->_toArray($this->ruta);
        if (!$this->esSuma) {
            $this->insertarMatriculasGratuitasExterno($matriculas);
        }
        $this->actualizaMatriculasGratuitasExterno($matriculas);
    }

    public function actualizaMatriculasGratuitasExterno(Array $matriculas) {
        foreach ($matriculas as $fila) {
            if (is_array($fila) && isset($fila[0], $fila[1])) {
                $nocliente = (int) $fila[0];
                $gratuitas = (int) $fila[1];
                $this->actualizaMatriculaGratuitaExterno($nocliente, $gratuitas);
            }
        }
    }

    public function actualizaMatriculaGratuitaExterno($nocliente, $gratuitas) {
        $where = [
            'centro' => $this->centro,
            'nocliente' => $nocliente
        ];
        $current = $this->db->select($this->tabla, "*", $where);
        if (count($current) === 0) {
            $data = $where;
            $data['gratuitas'] = $gratuitas;
            $data['compradas'] = 0;
            $data['totales_mes'] = $gratuitas;
            $data['gratuitas_hasta'] = $this->getGratuitasHasta();
            $this->db->insert($this->tabla, $data);
        } else {
            $data = $current[0];
            $data['gratuitas'] += $gratuitas;
            $data['totales_mes'] += $gratuitas;
            $data['gratuitas_hasta'] = $this->getGratuitasHasta();
            $this->db->update($this->tabla, $data, $where);
        }
    }

    public function insertarMatriculasGratuitasExterno(Array $matriculas) {
        foreach ($matriculas as $fila) {
            if (is_array($fila) && isset($fila[0], $fila[1])) {
                $nocliente = (int) $fila[0];
                $where = [
                    'centro' => $this->centro,
                    'nocliente' => $nocliente
                ];
                $current = $this->db->select($this->tabla, "*", $where);
                if (count($current) > 0) {
                    $data = $current[0];
                    $data['gratuitas'] = 0; // solo ponemos el contador a cero para pasar al proceso centralizado de actualización
                    $data['totales_mes'] = $data['compradas'];
                    $this->db->update($this->tabla, $data, $where);
                }
            }
        }
    }

    function getGratuitasHasta() {
        return date('Y-m-d 23:59:59', strtotime('last day of this month', time()));
    }

}
