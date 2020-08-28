<?php

namespace historico;

use convertidores\CSVHandler;
use Hashids\Hashids;
use DateTime;

class Historico {

    public $db;
    public $verHasta;

    public function __construct() {
        global $basesdatos;
        $this->db = new \Medoo\Medoo($basesdatos['historico']);
    }

    public function convertirAlbaran(array $config) {
        if (!isset($config['fichero'], $config['centro'])) {
            throw new \Exception("[Historico::convertirAlbaran] No se indican los campos correctos");
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
        $albaranes = $csv->_toJSON($config['fichero']);
        foreach ($albaranes as $key => $f) {
            $albaran = (object) $f;
            $idCliente =  $this->insertarCliente($albaran);
            $idAlbaran =  $this->insertarAlbaran($idCliente, $albaran);
            $hash = $hashids->encode($idCliente, $idAlbaran);
            $this->insertarHash($idCliente, $idAlbaran, $hash, 'albaranes');
            $url = "$url/albaran/$hash";
            $f->descarga = "$url.pdf?descargar=1";
            $f->previsualizar = "$url.pdf";
            $f->excel = "$url.xlsx";
            $facturas[$key] = $f;
        }
        $stream = $csv->toStream($facturas);
        file_put_contents($filename, $stream);
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
            unset($factura->cartavto);
            unset($factura->cartasepa);
            unset($factura->firmar);
            unset($factura->copia);
            _echo("Id factura: {$idFactura}");
            $hash = $hashids->encode($idCliente, $idFactura);
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

    function insertarHash(int $idCliente, int $idFactura, string $hash, string $tipo) {
        $this->db->update($tipo, [
                'hash' => $hash,
                'ver_hasta' => $this->verHasta
            ],
            [
                'id' => $idFactura,
                'cliente' => $idCliente,
            ]
        );
    }

    function insertarAlbaran(int $idCliente, \stdClass $albaran) {
        $where = [
            'cliente' => $idCliente,
            'numero' => $albaran->numero
        ];
        if (!$this->db->has('albaranes', $where)) {
            $where['ver_hasta'] = $this->verHasta;
            $this->db->insert('albaranes', $where);
        }
        return (int)$this->db->get('albaranes', ['id'], $where)['id'];
    }

    function insertarFactura(int $idCliente, \stdClass $factura) {
        $cartavto = isset($factura->cartavto) && $factura->cartavto === 'si';
        $cartasepa = isset($factura->cartasepa) && $factura->cartasepa === 'si';
        $cifrado = isset($factura->firmar) && $factura->firmar === 'si';
        $copia = isset($factura->copia) && $factura->copia === 'si';
        _echo ($cartavto);
        _echo ($cartasepa);
        _echo ($cifrado);
        _echo ($copia);
        $where = [
            'cliente' => $idCliente,
            'anho' => $factura->factura_anho,
            'serie' => $factura->factura_serie,
            'numero' => $factura->factura_numero
        ];
        if (!$this->db->has('facturas', $where)) {
            $where['ver_hasta'] = $this->verHasta;
            $where['carta_vencimientos'] = (int) $cartavto;
            $where['carta_sepa'] = (int) $cartasepa;
            $where['cifrado'] = (int) $cifrado;
            $where['copia'] = (int) $copia;
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
