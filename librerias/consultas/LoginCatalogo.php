<?php

use clientecatalogo\Login;
use clientecatalogo\objetos\Usuario;
use clientecatalogo\objetos\OpcionesExtra;

class LoginCatalogo {

    public $tokenPublico = CLAVELOGIN;
    public $url = SERVIDOR . "privado/cesta";
    public $urlBase = SERVIDOR;

    public function generar($parametros) {
        $usuario = new Usuario();
        $usuario->centro = $parametros["centro"];
        $usuario->identidad = $parametros["correo"];
        $usuario->contrasena = $parametros["pass"];
        $usuario->empresa = $this->getNombreEmpresaExterna(intval($parametros["empresa"]));
        $usuario->anadirExtra(OpcionesExtra::$CERRAR_DESPUES_PEDIR);
        $usuario->anadirExtra(OpcionesExtra::$VER_CABECERA);
        $usuario->anadirExtra(OpcionesExtra::$VER_PEDIDO);
        $this->tokenPublico = $this->getTokenEmpresaExterna(intval($parametros["empresa"]));
        _echo("Usamos token público: $this->tokenPublico");
        $login = new Login($usuario, $this->tokenPublico);
        $login->_url = $this->urlBase;
        $string = $login->getLogin();

        _echo("token l: $string");
        if (isset($parametros["ruta"])) {
            _echo("Guardamos en: " . $parametros["ruta"]);
            file_put_contents($parametros["ruta"], "$this->url?$string");
        }
        return $string;
    }

    public function getTokenEmpresaExterna($id) {
        $empresa = $this->getEmpresa($id);
        if (is_object($empresa) && isset ($empresa->token)) {
            $token = $empresa->token;
        } else {
            throw new Exception("No existe la empresa");
        }
        return $token;
    }
    public function getNombreEmpresaExterna($id) {
        $empresa = $this->getEmpresa($id);
        if (is_object($empresa) && isset ($empresa->nombre)) {
            $nombre = $empresa->nombre;
        } else {
            throw new Exception("No existe la empresa");
        }
        return $nombre;
    }
    
    public function getEmpresa($id) {
        $fichero = file_get_contents(RUTA_PARAMS . "empresas_externas.json");
        $empresas = json_decode($fichero);
        $empresaTag = "empresa-$id";
        return (isset ($empresas->$empresaTag)) ? $empresas->$empresaTag: null;
    }

}
