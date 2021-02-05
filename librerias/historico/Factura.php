<?php

namespace historico;

use historico\Historico;
use DateTime;
use Hashids\Hashids;
use convertidores\CSVHandler;

class Factura extends Historico {

    public function convertir(array $config) {
        if (!isset($config['fichero'], $config['centro'])) {
            throw new \Exception("[Historico::convertirPDF] No se indican los campos correctos");
        }
        $relativeDate = $this->retrieveVerHasta($config['centro']);
        $url = $this->retrieveURL($config['centro']);
        // Ver hasta (en días)
        if (isset($config['ver-hasta'])) {
            $relativeDate = "+" . $config['ver-hasta'] . " days";
        }
        $filename = $config['fichero-destino'];
        if (file_exists($filename)) {
            unlink($filename);
        }

        $fecha = new DateTime();
        $fecha->modify($relativeDate);
        $time = $fecha->getTimestamp();
        $salt = $this->retrieveSalt($config['centro']);
        $this->verHasta = $fecha->format('Y-m-d H:i:s');

        _var_dump("Visible hasta: " . $this->verHasta);
        _echo("Salt: $salt");
        _echo("URL: $url");
        _echo("Time: $time");

        $hashids = new Hashids($salt, 50);

        $csv = new CSVHandler();
        $facturas = $csv->_toJSON($config['fichero']);
        foreach ($facturas as $key => $f) {
            $factura = (object) $f;
            $idCliente =  $this->insertarCliente($factura);
            $idFactura =  $this->insertarFactura($idCliente, $factura);
            unset($factura->cartavto);
            unset($factura->cartasepa);
            unset($factura->cartasepab2b);
            unset($factura->firmar);
            unset($factura->copia);
            unset($factura->color);
            _echo("Id Cliente: {$idCliente}");
            _echo("Id factura: {$idFactura}");
            $hash = $hashids->encode($idCliente, $idFactura, $time);
            _echo("Hash: {$hash}");
            $this->insertarHash($idCliente, $idFactura, $hash, 'facturas');
            $uri = "$url/factura/$hash";
            $f->descarga = "$uri.pdf?descargar=1";
            $f->previsualizar = "$uri.pdf";
            $f->excel = "$uri.xlsx";
            $facturas[$key] = $f;
        }
        $stream = $csv->toStream($facturas);
        file_put_contents($filename, $stream);
    }

    private function insertarFactura(int $idCliente, \stdClass $factura) {
        $cartavto = isset($factura->cartavto) && $factura->cartavto === 'SI';
        $cartasepa = isset($factura->cartasepa) && $factura->cartasepa === 'SI';
        $cartasepab2b = isset($factura->cartasepab2b) && $factura->cartasepab2b === 'SI';
        $cifrado = isset($factura->firmar) && $factura->firmar === 'SI';
        $copia = isset($factura->copia) && $factura->copia === 'SI';
        $blanco_negro = !(isset($factura->color) && $factura->color === 'SI');
        _echo ($cartavto);
        _echo ($cartasepa);
        _echo ($cifrado);
        _echo ($cartasepab2b);
        _echo ($copia);
        _echo ($blanco_negro);
        $where = [
            'cliente' => $idCliente,
            'anho' => $factura->factura_anho,
            'serie' => $factura->factura_serie,
            'numero' => $factura->factura_numero
        ];
        $datos = $where;
        $datos['ver_hasta'] = $this->verHasta;
        $datos['carta_vencimientos'] = (int) $cartavto;
        $datos['carta_sepa'] = (int) $cartasepa;
        $datos['carta_sepab2b'] = (int) $cartasepab2b;
        $datos['cifrado'] = (int) $cifrado;
        $datos['es_copia'] = (int) $copia;
        $datos['blanco_negro'] = (int) $blanco_negro;
        $this->db->insert('facturas', $datos);

        return (int) $this->db->id();
    }
}