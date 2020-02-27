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
        if (isset($comandos['truncar-basedatos']) && $comandos['truncar-basedatos'] === "S") {
            $this->basedatos->delete($this->tablaStock, []);
        }
        $config = $this->extraerConfiguracion((int) $comandos['centro']);
        $csv2json = new CSVHandler();
        $neumaticosSoledad = $csv2json->_toJSON($this->ficheroOrigenStock);
        $neumaticos = new SoledadConvertidor();
        $this->stockRecalvi = $neumaticos->convertirStocks($neumaticosSoledad);
        $this->insertarBaseDatos($this->stockRecalvi, $config);
    }

    public function insertarBaseDatos(Array $stock, $config) {
        foreach ($stock as $valor) {
            $has = $this->basedatos->has($this->tablaStock, [
                'codigo' => $valor->getCodigo(),
            ]);
            $data = [
                'codigo' => $valor->getCodigo()
            ];
            if (!$has) {
                $data ['ancho'] = $valor->getAncho();
                $data ['perfil'] = $valor->getPerfil();
                $data ['diametro'] = $valor->getDiametro();
                $data[$config->columna_stock] = $valor->getStock();
                $this->basedatos->insert($this->tablaStock, $data);
            } else {
                $update = [];
                $update[$config->columna_stock] = $valor->getStock();

                $this->basedatos->update($this->tablaStock, $update, $data);
            }
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
