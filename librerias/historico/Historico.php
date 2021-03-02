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

    public function convertirAlbaranes(array $config) {
        if (!isset($config['fichero'], $config['centro'])) {
            throw new \Exception("[Historico::convertirAlbaranes] No se indican los campos correctos");
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
        $time = time();
        _echo("Time: $time");
        foreach ($albaranes as $key => $f) {
            $albaran = (object) $f;
            $idCliente =  $this->insertarCliente($albaran);
            $idAlbaran =  $this->insertarAlbaran($idCliente, $albaran);
            $hash = $hashids->encode($idCliente, $idAlbaran); // Se elimina el time para que el hash no cambie a la hora de regenerar el documento
            // $hash = $hashids->encode($idCliente, $idAlbaran, $time);
            $this->insertarHash($idCliente, $idAlbaran, $hash, 'albaranes');
            $uri = "$url/albaran/$hash";
            $f->descarga = "$uri.pdf?descargar=1";
            $f->previsualizar = "$uri.pdf";
            $f->excel = "$uri.xlsx";
            $albaranes[$key] = $f;
        }
        $stream = $csv->toStream($albaranes);
        file_put_contents($filename, $stream);
    }

    protected function insertarCliente(\stdClass $data) {
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

    function insertarHash(int $idCliente, int $idDocumento, string $hash, string $tipo) {
        $this->db->update($tipo, [
                'hash' => $hash,
                'ver_hasta' => $this->verHasta
            ],
            [
                'id' => $idDocumento,
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
