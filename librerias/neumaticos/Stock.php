<?php

use convertidores\CSVHandler;
use soledad2microgest\SoledadConvertidor;
use Medoo\Medoo;

/**
 * Description of Stock
 *
 * @author diego.gonda
 */
class Stock {

    private $ficheroOrigenStock;
    private $ficheroDestinoStock;
    private $stockRecalvi;
    private $basedatos;
    private $tablaStock = 'neumaticos_soledad';

    public function __construct() {
        global $basesdatos;
        $this->ficheroOrigenStock = RUTA_FICHEROSTEMPORALES . "/stock.csv";
        $this->ficheroDestinoStock = RUTA_FICHEROSTEMPORALES . "/stock.prn";
        $this->basedatos = new Medoo($basesdatos['rest']);
    }

    public function _generar() {
        $csv2json = new CSVHandler();
        $neumaticosSoledad = $csv2json->_toJSON($this->ficheroOrigenStock);
        $neumaticos = new SoledadConvertidor();
        $this->stockRecalvi = $neumaticos->convertirStocks($neumaticosSoledad);
        $this->insertarBaseDatos($this->stockRecalvi);
    }

    public function insertarBaseDatos(Array $stock) {
        $this->basedatos->delete($this->tablaStock, []);
        foreach ($stock as $valor) {
            $this->basedatos->insert($this->tablaStock, array(
                'codigo' => $valor->getCodigo(),
                'stock' => $valor->getStock(),
                'ancho' => $valor->getAncho(),
                'perfil' => $valor->getPerfil(),
                'diametro' => $valor->getDiametro(),
            ));
        }
    }

}
