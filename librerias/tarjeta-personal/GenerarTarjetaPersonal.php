<?php

use documentos\TarjetaPersonal as TarjetaPersonal;

class GenerarTarjetaPersonal {
    private $tarjetaPersonal;

    public function __construct() {
         $this->tarjetaPersonal = new TarjetaPersonal();
    }

    public function _get($comandos) {
        _var_dump($comandos);

        $texto = $comandos['string'];
        $temp = explode("|", $texto);
        $email = $temp[0];
        $empresa = $temp[1];
        $dni = $temp[2];
        _var_dump($email, $empresa, $dni);

        $claveEncriptacion = "B-<3ec.~4zC|^{.}Z/(b|7,gERz(] 7v67k:Nd{_d4;|HT=TW7[82u/EFii=SI-$";

        $ruta = $comandos['ruta'];

        
        $this->tarjetaPersonal->setPersonalDNI($dni);
        $this->tarjetaPersonal->setPersonalEmail($email);
        $this->tarjetaPersonal->setPersonalEmpresa($empresa);

        $this->tarjetaPersonal->setUrlBase(DOCUMENTO_PERSONAL);
        
        $this->tarjetaPersonal->setRutaGeneral($ruta);
        $this->tarjetaPersonal->inicializarRutas($dni);

        $this->tarjetaPersonal->setClaveEncriptacion($claveEncriptacion);

        $this->tarjetaPersonal->generarImagenBarcode($texto);

        $this->tarjetaPersonal->imprimirRPV($email);
    }

}
