<?php

require __DIR__ . '/../vendor/autoload.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Consultas
 *
 * @author diego.gonda
 */
class Consultas {

    private $request;

    public function __construct($url) {
        $this->request = new \cURL\Request($url);
        $this->request->getOptions()
                ->set(CURLOPT_RETURNTRANSFER, true)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_HTTPAUTH, CURLAUTH_BASIC)
                ->set(CURLOPT_TIMEOUT, 10)
                ->set(CURLOPT_CONNECTTIMEOUT, 10);
    }

    public function lanzarConsulta() {
        return json_decode($this->request->send()->getContent());
    }

    public function get($cabeceras = null) {
        $this->request->getOptions()
                ->set(CURLOPT_HTTPHEADER, $cabeceras);
        return $this->lanzarConsulta();
    }
    public function delete($cabeceras = null, $parametros = null, $url = null) {
        foreach ($parametros as $parametro){
            foreach ($parametro as $datos => $valor) {
                $url .= '/' . $datos .'/'. $valor;
            }
        }
        $this->request = new \cURL\Request($url);
        $this->request->getOptions()
                ->set(CURLOPT_RETURNTRANSFER, true)
                ->set(CURLOPT_SSL_VERIFYPEER, false)
                ->set(CURLOPT_SSL_VERIFYHOST, false)
                ->set(CURLOPT_HTTPAUTH, CURLAUTH_BASIC)
                ->set(CURLOPT_TIMEOUT, 10)
                ->set(CURLOPT_CONNECTTIMEOUT, 10)
                ->set(CURLOPT_CUSTOMREQUEST, 'DELETE')
                ->set(CURLOPT_HTTPHEADER, $cabeceras);
//        var_dump($url);
        return $this->lanzarConsulta();
    }

    public function post($cabeceras = null, $parametros = null) {
        $this->request->getOptions()
                ->set(CURLOPT_POST, true)
                ->set(CURLOPT_POSTFIELDS, json_encode($parametros))
                ->set(CURLOPT_HTTPHEADER, $cabeceras);
        return $this->lanzarConsulta();
    }

}
