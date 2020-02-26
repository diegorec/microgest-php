<?php

use convertidores\CSVHandler;
use convertidores\JSONHandler;
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
        $this->basedatos = new Medoo($basesdatos['rest']);
    }

    public function _generar($comandos) {
        $config = $this->extraerConfiguracion((int) $comandos['centro']);
        $csv2json = new CSVHandler();
        $neumaticosSoledad = $csv2json->_toJSON($this->ficheroOrigenStock);
        $neumaticos = new SoledadConvertidor();
        $this->stockRecalvi = $neumaticos->convertirStocks($neumaticosSoledad);
        $this->insertarBaseDatos($this->stockRecalvi, $config);
    }

    public function insertarBaseDatos(Array $stock, $config) {
        $this->basedatos->delete($this->tablaStock, []);
        foreach ($stock as $valor) {
            $this->basedatos->debug()->insert($this->tablaStock, array(
                'codigo' => $valor->getCodigo(),
                'stock' => $valor->getStock(),
                'ancho' => $valor->getAncho(),
                'perfil' => $valor->getPerfil(),
                'diametro' => $valor->getDiametro(),
                'centro' => $config->tabla_centro
            ));
        }
    }

    public function extraerConfiguracion($centro) {
        $this->configuracion = (new JSONHandler)->_toArray(RUTA_PARAMS . "soledad.json");
        $config = $this->configuracion->default;
        foreach ($this->configuracion as $c) {
            if ($c->id === $centro) {
                $config = $c;
                break;
            }
        }
        $this->ficheroDestinoStock = RUTA_FICHEROSTEMPORALES . "/$config->prn_precio";
        return $config;
    }

}
