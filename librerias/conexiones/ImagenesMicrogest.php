<?php

namespace conexiones;

/**
 * Description of ImagenesMicrogest
 * Librería encargada en realizar la transformación de las rutas de petición a este webservice y de devolver las imágenes correctas
 * y almacenadas en microgest
 *
 * NOTA: Esta librería se desarrolla especificamente para las necesidades de este servidor.
 * @author diego.gonda
 * @version 0.1
 */
class ImagenesMicrogest {

    private $IPMicrogest = IMAGENESGESTIONIP;
    private $urlMicrogest = IMAGENESGESTION;
    private $urlPrisauto = IMAGENESGESTIONPRISAUTO;
    private $peticion;
    private $mime_type;
    private $ruta;

    public function __construct($peticion = null) {
        if (!is_null($peticion)) {
            $this->setPeticion($peticion);
            $this->descomponerInformacion();
        }
    }

    /**
     * Dada la petición de consulta al WS REST nos devuelve la dirección del catálogo online que debemos consultar para obtener un documento
     * @param array $peticion
     * @return string
     */
    public function peticion2rutaMicrogest($peticion) {
        return $this->IPMicrogest . implode('/', array_slice($peticion, 1));
    }

    /**
     * Dada la petición URL de Microgest, traducimos a la URL del servidor REST
     * @param string $peticion
     * @return string
     */
    public function rutaMicrogest2rutaREST($peticion) {
        $explode = explode('/', $peticion);
        $imagen = $explode[count($explode) - 1];
        $centro = (strpos($peticion, 'prisauto') > 0) ? 'prisauto' : 'microgest';
        return "imagenes/$centro/$imagen";
    }

    /**
     * 
     * @param mixed $consulta 
     *  Si es array, se entiende que es la petición al WS por lo que hará una llamada previa al método estático peticion2rutaMicrogest
     */
    public function pedirImagenesMicrogest($consulta = null) {
        if (is_null($consulta) && is_null($this->getRuta())) {
            return '';
        } else if (is_null($consulta)) {
            $consulta = $this->getRuta();
        } else {
            $this->setPeticion($consulta);
            $this->descomponerInformacion();
            $consulta = $this->getRuta();
        }
        return file_get_contents($consulta);
    }

    public function descomponerInformacion() {
        if (!is_null($this->getPeticion())) {
            $peticion = $this->getPeticion();
            $this->setRuta($this->peticion2rutaMicrogest($peticion));
            $this->setMime_type(HTTP::getMimeTypeExtension($this->getRuta()));
        }
    }

    function getIPMicrogest() {
        return $this->IPMicrogest;
    }

    function getPeticion() {
        return $this->peticion;
    }

    function setIPMicrogest($IPMicrogest) {
        $this->IPMicrogest = $IPMicrogest;
    }

    function setPeticion($peticion) {
        $this->peticion = $peticion;
    }

    function getMime_type() {
        return $this->mime_type;
    }

    function setMime_type($mime_type) {
        $this->mime_type = $mime_type;
    }

    public function __toString() {
        return json_encode(
                array(
                    'ruta' => $this->getRuta(),
                    'mime_type' => $this->getMime_type()
                )
        );
    }

    function getRuta() {
        return $this->ruta;
    }

    function setRuta($ruta) {
        $this->ruta = $ruta;
    }

}
