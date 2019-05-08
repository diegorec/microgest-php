<?php
class TarjetaFichajeVista {

    public $nombre;
    public $centro;
    public $pie;
    public $telefono;
    public $codigoBarras;

    public function generarTarjeta() {
        return "<div class=\"tarjeta\">
                <div class=\"nombre\">
                    <h3>$this->nombre</h3>
                </div>
                <div class=\"texto\">
                    Esta tarjeta es propiedad de $this->centro y su uso es personal e intransferible.<br />
                    $this->telefono
                </div>
                <div class=\"pie\">
                    <strong>$this->pie</strong></div>
                <img class=\"codigo-barras\" src=\"$this->codigoBarras\"/>
            </div>";
    }

    public function generarCSS() {
        return " body {
              font-family: Helvetica;
}
.tarjeta {
  height: 67mm;
  width: 100mm;
  background-color: white;
  background-image: url('http://documentos.recalvi.es:8000/default/Fondo.png');
  background-size: contain;
  -webkit-background-size: cover;
  float: left;
  margin-top: 0mm;
  margin-right: 20mm;
  margin-bottom: 1mm;
  margin-left: 0mm;
  font-size: 3.5mm;
}
.nombre, .texto {
  position: relative;
  top: 25%;
  left: 23.5%;
}
.texto {
  font-size: 3mm;
  max-width: 50mm;
}

.pie {
  position: relative;
  top: 48%;
  left: 5mm;
  font-size: 4.5mm;
}
.codigo-barras {
  width: 64mm;
  position: relative;
  left: 60%;
  bottom: 13mm;
  transform: rotate(270deg);
  -webkit-transform: rotate(270deg);
}
.pagina {
    page-break-before: always;
}";
    }

}
