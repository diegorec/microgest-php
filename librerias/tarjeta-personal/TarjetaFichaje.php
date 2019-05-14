<?php

use convertidores\CSVHandler;
use Picqer\Barcode\BarcodeGeneratorPNG;

class TarjetaFichaje {

    public $barcodeGenerator;
    public $tarjetaXpagina = 12;
    public $nombreMaxLength = 15;
    public $code;

    public function __construct() {
        $this->barcodeGenerator = new BarcodeGeneratorPNG();
        $this->code = BarcodeGeneratorPNG::TYPE_CODE_128;
    }

    public function generar($comandos) {
        $usuarios = $this->getUsuarios($comandos['ruta']);
        $tarjetas = [];
        $codigosBarras = [];
        $style = (new TarjetaFichajeVista)->generarCSS();
        $i = 0;
        foreach ($usuarios as $clave => $usuario) {
            if (isset($usuario->nombre)) {
                $vista = new TarjetaFichajeVista();
                $vista->centro = $usuario->centro;
                $vista->nombre = $usuario->nombre;
                $vista->pie = $this->getAlmacenFormateado($usuario->idalmacen, $usuario->almacen);
                $vista->telefono = $this->getTelefonoFormateado($usuario->telefono);
                $vista->codigoBarras = $this->barcodeGenerator->getBarcode($usuario->etiqueta, $this->code);
                $tarjetas [] = $vista->generarTarjeta();
                $i++;
                if ($this->tarjetaXpagina === $i) {
                    $tarjetas [] = "<div class=\"pagina\">&nbsp;</div>";
                    $i = 0;
                }
            }
        }
        $body = implode('', $tarjetas);
        $filenamePDF = $comandos['fichero-destino'];
        $filenameHTML = "$filenamePDF.html";
        file_put_contents($filenameHTML, "<!DOCTYPE html>
                <html>
                <head lang=\"es\">
                    <meta charset=\"UTF-8\" />
                    <title>Tarjetas personal</title>
                    <style type=\"text/css\">
                        $style
                    </style>
                </head>
                <body>
                    $body
                </body>
            <html>");
        shell_exec("/usr/bin/wkhtmltopdf.sh  -L 0 -R 0 -T 0 -B 0 -O landscape $filenameHTML $filenamePDF");
//        $this->borrarTemporal($filenameHTML);
//        $this->borrarTemporales($codigosBarras);
    }

    public function getUsuarios($filename) {
        $usuarios = [];
        if (is_file($filename) && is_readable($filename)) {
            $csvHandler = new CSVHandler();
            $csvHandler->codificarUTF8 = false;
            $usuarios = $csvHandler->_toJSON($filename);
        }
        return $usuarios;
    }

    public function borrarTemporales($temporales) {
        foreach ($temporales as $temporal) {
            $this->borrarTemporal($temporal);
        }
    }

    public function borrarTemporal($temporal) {
        return unlink($temporal);
    }

    public function getTelefonoFormateado($telefono) {
        $string = "&nbsp;";
        if (strlen($telefono) > 0) {
            $string = $telefono;
        }
        return $string;
    }

    public function getAlmacenFormateado($idalmacen, $almacen) {
        return "$idalmacen $almacen";
    }

    public function getNombreFormateado($nombre) {
        if (strlen($nombre) > $this->nombreMaxLength) {
            $exploded = explode(" ", $nombre);
            unset($exploded[count($exploded) - 1]);
            $imploded = implode(" ", $exploded);
            $nombre = $this->getNombreFormateado($imploded);
        }
        return $nombre;
    }

}
