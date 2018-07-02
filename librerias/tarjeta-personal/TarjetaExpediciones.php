<?php

use fpdf\PDF_Code128;

class TarjetaExpediciones {

    private $pdf;

    public function __construct() {
        $this->pdf = new PDF_Code128('P', 'mm', array(150, 100));
        $this->pdf->AliasNbPages();
        $this->pdf->SetFillColor(0, 0, 0);
        $this->pdf->SetTextColor(0, 0, 0);
    }

    function generar($comandos) {
        $ficheroOrigen = $comandos['fichero-origen'];
        $ficheroDestino = $comandos['fichero-destino'];
        
        $entradas = $this->obtenerFichero($ficheroOrigen);
        if (is_object($entradas) && isset($entradas->expediciones)) {
            $expediciones = $entradas->expediciones;
            $this->generarDocumento($expediciones);
            $this->pdf->Output('F', $ficheroDestino);
        }
    }

    function obtenerFichero($filename) {    
        $contenido = file_get_contents($filename);
        $json = json_decode($contenido);
        return $json;
    }

    function generarDocumento(Array $expediciones) {
        $bultos = count($expediciones);
        $i = 1;
        foreach ($expediciones as $expedicion) {
            $idExpedicion = intval($expedicion->id);
            $ubicacion = $expedicion->ubicacion;
            $fecha = date("d/m/Y", strtotime($expedicion->fecha));
            $idProveedor = "";
            $nombreProveedor = "";
            if (isset($expedicion->proveedor, $expedicion->proveedor->codigo, $expedicion->proveedor->nombre)) {
                $idProveedor = intval($expedicion->proveedor->codigo);
                $nombreProveedor = $expedicion->proveedor->nombre;
            }
            $this->generarHoja($idExpedicion, $ubicacion, $fecha, $i, $bultos, $idProveedor, $nombreProveedor);
        }
    }

    function generarHoja($idExpedicion, $ubicacion, $fecha, $i, $bultos, $idProveedor, $nombreProveedor) {
        $this->pdf->AddPage();
        $this->pdf->Image(RUTA_ASSETS . 'images/logo.png', 15, 5, 0, 22, 'PNG');
        $this->pdf->Code128(10, 30, $idExpedicion, 80, 15);
        $this->pdf->SetFont('Arial', '', 16);
        $this->pdf->SetXY(10, 46);
        $this->pdf->Cell(80, 5, $idExpedicion, 0, 0, 'C', 0);
        $this->pdf->SetFont('courier', 'B', 22);
        $this->pdf->SetXY(10, 55);
        $this->pdf->Cell(80, 15, utf8_decode($ubicacion), 1, 0, 'C', 0);
        $this->pdf->SetFont('courier', 'B', 22);
        $this->pdf->SetXY(10, 70);
        $this->pdf->Cell(80, 15, $fecha, 1, 0, 'C', 0);
        $this->pdf->SetFont('courier', 'B', 40);
        $this->pdf->SetXY(10, 85);
        $this->pdf->Cell(80, 20, $i++ . "/" . $bultos, 1, 0, 'C', 0);
        $this->pdf->SetFont('Arial', 'B', 8);
        $this->pdf->Text(11, 58, utf8_decode("UBICACIÓN:"));
        $this->pdf->Text(11, 73, "FECHA:");
        $this->pdf->Text(11, 88, utf8_decode("CAJA Nº:"));
        $this->pdf->Text(11, 110, "PROVEEDOR: $idProveedor");
        $this->pdf->SetFont('Arial', '', 17);
        $this->pdf->Text(11, 120, "$nombreProveedor");
    }

}
