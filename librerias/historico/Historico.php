<?php

namespace historico;

use convertidores\CSVHandler;
use Hashids\Hashids;
use DateInterval;
use DateTime;

class Historico {

    public $db;
    public $verHasta;

    public function __construct() {
        global $basesdatos;
        $this->db = new \Medoo\Medoo($basesdatos['historico']);
    }

    public function convertirFactura(array $config) {
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
        $salt = $this->retrieveSalt($config['centro']);
        $this->verHasta = $fecha->format('Y-m-d H:i:s');

        _var_dump("Visible hasta: " . $this->verHasta);
        _echo("Salt: $salt");
        _echo("URL: $url");

        $hashids = new Hashids($salt, 50);

        $csv = new CSVHandler();
        $facturas = $csv->_toJSON($config['fichero']);
        foreach ($facturas as $key => $f) {
            $factura = (object) $f;
            $idCliente =  $this->insertarCliente($factura);
            $idFactura =  $this->insertarFactura($idCliente, $factura);
            $hash = $hashids->encode($idCliente, $idFactura);
            $this->insertarHash($idCliente, $idFactura, $hash);
            $f->descarga = "$url/factura/$hash.pdf";
            $f->previsualizar = "$url/ver/factura/$hash.pdf";
            $facturas[$key] = $f;
        }
        $stream = $csv->toStream($facturas);
        file_put_contents($filename, $stream);
    }

    private function insertarCliente(\stdClass $data) {
        $where = [
            'centro' => $data->centro,
            'nocliente' => $data->nocliente,
            'subdivision' => $data->subdivision,
            'cliente_de' => $data->cliente_de,
            'empresa' => $data->empresa
        ];
        if (!$this->db->has('clientes', $where)) {
            $this->db->insert('clientes', $where);
        }
        return (int)$this->db->get('clientes', ['id'], $where)['id'];
    }

    function insertarHash(int $idCliente, int $idFactura, string $hash) {
        $this->db->update(
            'facturas',
            [
                'hash' => $hash
            ],
            [
                'id' => $idFactura,
                'cliente' => $idCliente,
            ]
        );
    }

    function insertarFactura(int $idCliente, \stdClass $factura) {
        $where = [
            'cliente' => $idCliente,
            'anho' => $factura->factura_anho,
            'serie' => $factura->factura_serie,
            'numero' => $factura->factura_numero
        ];
        if (!$this->db->has('facturas', $where)) {
            $where['ver_hasta'] = $this->verHasta;
            $this->db->insert('facturas', $where);
        }
        return (int)$this->db->get('facturas', ['id'], $where)['id'];
    }

    public function retrieveURL(string $centro): string {
        return $this->retrieveConfig("servicio_documentos", $centro);
    }

    public function retrieveSalt(string $centro): string {
        return $this->retrieveConfig("salt", $centro);
    }

    public function retrieveVerHasta(string $centro) {
        return $this->retrieveConfig("ver_hasta", $centro);
    }

    public function retrieveConfig(string $tag, string $centro) {
        $where = [
            'clave' => "$tag" . "_" . "$centro"
        ];
        if (!$this->db->has('config', $where)) {
            $where['clave'] = "$tag" . "_" . "default";
        }
        return $this->db->get('config', '*', $where)['valor'];
    }
}
