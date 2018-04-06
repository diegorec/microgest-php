<?php

use grcURL\FTP;
use soledad2microgest\SoledadConvertidor;
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
    public $usuario = "recalvi107827";
    public $contrasena = "re107827";
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
        $this->ficheroLog = RUTA_LOG . date("YW") . ".log";
        $this->ftp = new FTP("ftp://servicios.gruposoledad.net/articles/MasterArticles.csv", $this->ficheroLog);
        $this->ftp->_USERAGENT = $this->useragent;
        $this->ftp->usuario = "recalvi107827";
        $this->ftp->contrasena = "re107827";
        $this->ficheroOrigenMasters = RUTA_FICHEROSTEMPORALES . "/masters.csv";
        $this->ficheroDestinoMastersMicrogest = RUTA_FICHEROSTEMPORALES . "/m-mg.prn";
        $this->ficheroDestinoMastersMicrogestDetalles = RUTA_FICHEROSTEMPORALES . "/m-mg-d.prn";
        $this->ficheroOrigenStock = RUTA_FICHEROSTEMPORALES . "/stock.csv";
        $this->ficheroDestinoStockMicrogest = RUTA_FICHEROSTEMPORALES . "/stock.prn";
        $this->csv2json = new CSVHandler();
        $this->prnHandler = new PRNHandler();
        $this->convertidorNeumaticos = new SoledadConvertidor();
    }

    public function _generarMasters($comandos) {
        $this->obtenerFicheroMasters();
        $neumaticosSoledad = $this->csv2json->_toJSON($this->ficheroOrigenMasters);
        
        $this->generarPRNDetalles($neumaticosSoledad);
        $this->generarPRNNeumaticos($neumaticosSoledad);
        
        $this->ejecutarCobol("$this->comandoBase PWEBS149.COB A=\"1$this->ficheroDestinoMastersMicrogest\"");
        $this->ejecutarCobol("$this->comandoBase PWEBS149.COB A=\"2$this->ficheroDestinoMastersMicrogestDetalles\"");
    }

    public function _generarStock($comandos) {
        $this->ftp->_URL = "ftp://servicios.gruposoledad.net/articles/stock.csv";
        $this->obtenerFicheroStock();
        $stockSoledad = $this->csv2json->_toJSON($this->ficheroOrigenStock);
        $porcentajeRecalvi = intval($comandos ['porcentaje']);
        $this->generarPRNStock($stockSoledad, $porcentajeRecalvi);

        $this->ejecutarCobol("$this->comandoBase PWEBS150.COB A=\"$this->ficheroDestinoStockMicrogest\" > temp111");
    }

    public function generarPRNNeumaticos(Array $neumaticosSoledad) {
        $neumaticosRecalvi = $this->convertidorNeumaticos->convertir($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoMastersMicrogest, $neumaticosRecalvi);
    }

    public function generarPRNDetalles(Array $neumaticosSoledad) {
        $neumaticosRecalviDetalles = $this->convertidorNeumaticos->convertirNeumaticosDetalles($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoMastersMicrogestDetalles, $neumaticosRecalviDetalles);
    }

    public function generarPRNStock(Array $neumaticosSoledad, $porcentajeIncrementoPrecio = 0) {
        $this->convertidorNeumaticos->setPorcentajeIncrementoPrecio($porcentajeIncrementoPrecio);
        $neumaticosRecalviDetalles = $this->convertidorNeumaticos->convertirStocks($neumaticosSoledad);
        $this->generarFicheroCobol($this->ficheroDestinoStockMicrogest, $neumaticosRecalviDetalles);
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
    
    public function ejecutarCobol ($comando) {
        _echo($comando);
        shell_exec($comando);
    }

}
