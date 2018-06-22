<?php

use clientecatalogo\Login;
use clientecatalogo\objetos\Usuario;

class LoginCatalogo {

    public $tokenPublico = CLAVELOGIN;
    public $url = "http://catalogoonline.recalvi.es/privado/cesta";

    public function generar($parametros) {
        $usuario = new Usuario();
        $usuario->centro = $parametros["centro"];
        $usuario->identidad = $parametros["correo"];
        $usuario->contrasena = $parametros["pass"];
        $usuario->empresa = $parametros["empresa"];
        if (strcmp("soledad", $parametros["empresa"]) === 0) {
            $this->tokenPublico = "GFTFDR@@5584UYHNOLI#!2314PPR6543";
        }
        _echo("Usamos token público: $this->tokenPublico");
        $login = new Login($usuario, $this->tokenPublico);
        $string = $login->getLogin();
        if (isset($parametros["ruta"])) {
            _echo ("Guardamos en: " . $parametros["ruta"]);
            file_put_contents($parametros["ruta"], "$this->url?$string");
        }
        return $string;
    }

}
