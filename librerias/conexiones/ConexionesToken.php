<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace conexiones;

/**
 * Description of ConexionesToken
 *
 * @author diego.gonda
 */
class ConexionesToken extends \conexiones\Conexiones {

    private $centro;
    private $email;
    private $contrasena;
    private $ttl;
    private $clave;
    private $empresa;
    private $string;
    private $token;

    public function __construct() {
        parent::__construct();
        $this->centro = 'recalvi';
        $this->email = 'diegogonda@recalvi.es';
        $this->contrasena = 'chari';
        $this->ttl = date('Y-m-d h:i:s');
        $this->clave = "GFTFDR@@5584UYHNOLI#!2314PPR6543";
        $this->empresa = "soledad";

        $this->string = "$this->centro\\$this->email|$this->contrasena|$this->ttl";

        $this->token = $this->encode($this->string, $this->clave);
    }

    public function blocking1x1($urls, $contentType = 'application/json') {
        foreach ($urls as $clave => $valor) {
            $valor .= "?t=$this->token&e=$this->empresa";
            $urls[$clave] = $valor;
            _echo("Llamando a: " . $valor);
        }
        return parent::blocking1x1($urls, $contentType);
    }

    /*
     * Los pasos para descifrar serían:
     *    1.- Pasar urldecode.
     *    2.- Pasar decodebase64.
     *    3.- Y finalmente XOR con la clave GFTFDR@@5584UYHNOLI#!2314PPR6543
     */

    public function decode($token, $clave) {
        if (is_string($token) && is_string($clave)) {
            $urldecode = urldecode($token);
            $encoded_data = base64_decode($token);
            $datos = $this->xor_string($encoded_data, $clave);
            return $datos;
        }
        return null;
    }

    /**
     * Los pasos para cifrar han sido:
     *    1.- Pasar XOR con la clave indicada GFTFDR@@5584UYHNOLI#!2314PPR6543
     *    2.- Hacer un encode en base64
     *    3.- Pasar un urlencode para que no de problemas al ser llamada por GET.
     */
    public function encode($string, $clave) {
        if (is_string($string) && is_string($clave)) {
            $xored = $this->xor_string($string, $clave);
            $base64 = base64_encode($xored);
            $token = urlencode($base64);
            return $token;
        }
        return null;
    }

    public function xor_string($string, $key) {
        for ($i = 0; $i < strlen($string); $i++) {
            $string[$i] = ($string[$i] ^ $key[intval($i % strlen($key))]);
        }
        return $string;
    }

}
