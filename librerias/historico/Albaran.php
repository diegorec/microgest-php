<?php

namespace historico;

use GestorDocumental\{
    Database,
    // PDF 
    HTML\PDFManager,
    // HTML
    HTML\Albaran as AlbaranHtml,
    HTML\Factura,
    HTML\Facturas\CartaVencimientos,
    HTML\Facturas\CartaSEPA,
    HTML\Tools\FormatoPagina,
    HTML\Tools\PageSize,
    HTML\Tools\Albaran\Tipos as TiposAlbaran,
    // EXCEL
    Excel\Albaran as AlbaranExcel,
    Excel\Factura as FacturaExcel
};


class Albaran {

    private $manager = null;

    public function __construct() {
        global $basesdatos;
        Database::getInstance()->onInit($basesdatos['historico']);
        $this->manager = new PDFManager("wkhtmltopdf", RUTA_FICHEROSTEMPORALES);
    }

    public function convertirPDF(array $config) {
        $formatoPagina = FormatoPagina::A4;
        $tipoAlbaran = TiposAlbaran::ALBARAN;
        $tipoCabecera = 'logo';

        $json = file_get_contents($config['fichero']);
        $list = json_decode($json);
        if(!is_array($list) || !isset($list[0])) {
            throw new \Exception("[historico\Albaran::convertirPDF] Albaran incorrecto"); 
        }
        $albaran = (object) $list[0];
        $albaran->cabecera_preimpresa = "http://192.168.1.198:8082/assets/corporativo//recalvi/recalvi-certifica.jpg";

        _echo_info("Formato página:" . $formatoPagina);
        _echo_info("Tipo albaran:" . $tipoAlbaran);
        _echo_info("Tipo cabecera:" . $tipoCabecera);

        $objeto = new AlbaranHtml("albaran-completo", 1, $formatoPagina, $tipoAlbaran, $tipoCabecera);
        _echo_info("Se lanza la generación del albaran");

        $html = $objeto->generate($albaran);
        
        $this->manager->convertHtml($html, $config['fichero-destino']); 
    }
}
