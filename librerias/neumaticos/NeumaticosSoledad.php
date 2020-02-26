<?php

use grcURL\FTP;
use soledad2microgest\SoledadConvertidor;
use convertidores\JSONHandler;
use convertidores\CSVHandler;
use convertidores\PRNHandler;

/**
 * Description of NeumaticosSoledad
 *
 * @author diego.gonda
 */
class NeumaticosSoledad {

    public $ftp;
    public $ftpstock;
    public $url = "ftp://servicios.gruposoledad.net/articles/MasterArticles.csv";
    public $useragent = "UA::PRUEBASLIBRERIA";
    public $usuario;
    public $contrasena;
    public $ficheroOrigenMasters;
    public $ficheroDestinoMasters;
    public $ficheroDestinoMastersMicrogest;
    public $ficheroDestinoMastersMicrogestDetalles;
    public $ficheroOrigenStock;
    public $ficheroLog;
    public $csv2json;
    public $prnHandler;
    public $convertidorNeumaticos;
    public $comandoBase = "cd /home/u/vigo/micro/ && /usr/rmcobol/runcobol";

    public function __construct() {
        $this->ficheroLog = _getRutaLog();
        $this->csv2json = new CSVHandler();
        $this->prnHandler = new PRNHandler();
        $this->convertidorNeumaticos = new SoledadConvertidor();
    }

    public function _generarMasters($comandos) {
        $this->extraerConfiguracion((int) $comandos['centro'], "masters");
        $this->obtenerFicheroMasters();
        $neumaticosSoledad = $this->csv2json->_toJSON($this->ficheroOrigenMasters);

        $this->generarPRNDetalles($neumaticosSoledad);
        $this->generarPRNNeumaticos($neumaticosSoledad);

        $centro = str_pad($comandos['centro'], 2, 0, STR_PAD_LEFT);
        $comandoFicheroMasters = str_pad($this->ficheroDestinoMastersMicrogest, 50);
        $comandoFicheroMastersDetalles = str_pad($this->ficheroDestinoMastersMicrogestDetalles, 50);
        $this->ejecutarCobol("$this->comandoBase PWEBS149.COB A=\"1$comandoFicheroMasters$centro\"");
        $this->ejecutarCobol("$this->comandoBase PWEBS149.COB A=\"2$comandoFicheroMastersDetalles$centro\"");
    }

    public function _generarPrecios($comandos) {
        $this->extraerConfiguracion((int) $comandos['centro'], "stock");
        $this->ftp->_URL = "ftp://servicios.gruposoledad.net/articles/stock.csv";
        $this->obtenerFicheroStock();
        $preciosSoledad = $this->csv2json->_toJSON($this->ficheroOrigenStock);
        $porcentajeRecalvi = intval($comandos ['porcentaje']);
        $this->generarPRNPrecios($preciosSoledad, $porcentajeRecalvi);

        $centro = str_pad($comandos['centro'], 2, 0, STR_PAD_LEFT);
        $comando = str_pad($this->ficheroDestinoPreciosMicrogest, 50);
        $this->ejecutarCobol("$this->comandoBase PWEBS150.COB A=\"$comando$centro\" > temp111");
    }

    public function extraerConfiguracion($centro, $tipo) {
        $this->configuracion = (new JSONHandler)->_toArray(RUTA_PARAMS . "soledad.json");
        $config = $this->configuracion->default;
        foreach ($this->configuracion as $c) {
            if ($c->id === $centro) {
                $config = $c;
                break;
            }
        }
        $this->ftp = new FTP("ftp://servicios.gruposoledad.net/articles/MasterArticles.csv", $this->ficheroLog);
        $this->ftp->_USERAGENT = $this->useragent;
        $this->ftp->usuario = $config->{"usuario_$tipo"};
        $this->ftp->contrasena = $config->{"contrasena_$tipo"};
        $this->ficheroOrigenMasters = RUTA_FICHEROSTEMPORALES . "/$config->fichero_copia";
        $this->ficheroDestinoMastersMicrogest = RUTA_FICHEROSTEMPORALES . "/$config->prn_cabecera";
        $this->ficheroDestinoMastersMicrogestDetalles = RUTA_FICHEROSTEMPORALES . "/$config->prn_detalles";
        $this->ficheroOrigenStock = RUTA_FICHEROSTEMPORALES . "/$config->fichero_stock";
        $this->ficheroDestinoPreciosMicrogest = RUTA_FICHEROSTEMPORALES . "/$config->prn_precio";
    }

    public function generarPRNNeumaticos(Array $neumaticosSoledad) {
        $neumaticosRecalvi = $this->convertidorNeumaticos->convertir($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoMastersMicrogest, $neumaticosRecalvi);
    }

    public function generarPRNDetalles(Array $neumaticosSoledad) {
        $neumaticosRecalviDetalles = $this->convertidorNeumaticos->convertirNeumaticosDetalles($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoMastersMicrogestDetalles, $neumaticosRecalviDetalles);
    }

    public function generarPRNPrecios(Array $neumaticosSoledad, $porcentajeIncrementoPrecio = 0) {
        $this->convertidorNeumaticos->setPorcentajeIncrementoPrecio($porcentajeIncrementoPrecio);
        $neumaticosRecalviDetalles = $this->convertidorNeumaticos->convertirPrecios($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoPreciosMicrogest, $neumaticosRecalviDetalles);
    }

    private function generarFicheroCobol($fichero, Array $datos) {
        $formatoCobol = $this->convertidorNeumaticos->adaptarCobol($datos);
        $this->prnHandler->array2file($fichero, $formatoCobol, false);
    }

    public function obtenerFicheroMasters() {
        _echo("NeumaticosSoledad:obtenerFicheroMasters");
        $this->obtenerFichero($this->ficheroOrigenMasters);
    }

    public function obtenerFicheroStock() {
        _echo("NeumaticosSoledad:obtenerFicheroStock");
        $this->obtenerFichero($this->ficheroOrigenStock);
    }

    public function obtenerFichero($fichero) {
        if (is_file($fichero)) {
            _echo("Borramos el fichero: $fichero");
            unlink($fichero);
        }
        $this->ftp->fichero = $fichero;
        _echo($fichero);
        $this->ftp->copiaLocal();
    }

    public function ejecutarCobol($comando) {
        _echo($comando);
        shell_exec($comando);
    }

}
