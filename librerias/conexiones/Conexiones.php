<?php

namespace conexiones;

use conexiones\Request as request;

/**
 * Description of Conexiones
 *
 * @author diego.gonda
 */
class Conexiones {

    private $queue;
    private $timeout;
    private $contentType;
    private $returnTransfer;

    /**
     * Inicializa los valores por defecto
     * Timeout: 5 segundos
     * Content-Type: application/json
     */
    public function __construct() {
        $this->setTimeout(5);
        $this->setReturnTransfer(true);
        $this->setContentType('application/json');
        $this->setQueue(new \cURL\RequestsQueue);
    }
    /**
     * Consultas Blocking que recibe un array que y aprovecha el código de blocking para consultar de 1 en 1.
     * Esto se hace porque microgest se lleva mal con las consultas en bloque.
     * @param array $urls
     * @param string $contentType tipo de contenido que vamos a DESCARGAR del servidor consultado
     * @return array
     */
    public function blocking1x1 ($urls, $contentType = 'application/json') {
        $respuesta = [];
        foreach ($urls as $url) {
            $respuestaParcial = $this->blocking($url, $contentType);
            foreach ($respuestaParcial as $valor) {
                $respuesta[] = $valor;
            }
        }
        return $respuesta;
    }
    /**
     * Consultas BLOCKING que nos permite realizar N consultas simultáneas a otros servidores.
     * Una vez lanzada la consulta, se espera por todas y cada una de las respuestas.
     * @param array $urls
     * @param string $contentType tipo de contenido que vamos a DESCARGAR del servidor consultado
     * @return array
     */
    public function blocking($urls, $contentType = 'application/json') {
        if (is_array($urls) && count($urls) > 0) {
            $this->queue->getDefaultOptions()
                    ->set(CURLOPT_USERAGENT, USER_AGENT)
                    ->set(CURLOPT_TIMEOUT, $this->getTimeout())
                    ->set(CURLOPT_RETURNTRANSFER, $this->getReturnTransfer());
            $respuesta = [];
            $this->queue->addListener('complete', function (\cURL\Event $event) use (&$respuesta, $contentType) {
                switch ($contentType) {
                    case 'application/json':
                    default:
                        $temp = json_decode($event->response->getContent());
                        if (is_object($temp)) {
                            $respuesta [] = $temp;
                        } else if (is_array($temp)) {
                            foreach ($temp as $linea) {
                                $respuesta [] = $linea;
                            }
                        }
                        break;
                }
            });
            foreach ($urls as $url) {
                $request = new \cURL\Request($url);
                $this->queue->attach($request);
            }
            $this->queue->send();
            return $respuesta;
        } if (is_string($urls)) {
            $request = new \cURL\Request($urls);
            $request->getOptions()
                    ->set(CURLOPT_USERAGENT, USER_AGENT)
                    ->set(CURLOPT_SSL_VERIFYPEER, false)
                    ->set(CURLOPT_TIMEOUT, $this->getTimeout())
                    ->set(CURLOPT_RETURNTRANSFER, $this->getReturnTransfer());
            return json_decode($request->send()->getContent(), true);
        }
        return null;
    }

    /**
     * Las consultas NON-BLOCKING se usarán para descarga de información que no necesitamos en el momento. Por lo que el listener lo debe de asumir la función que llama
     * @param type $urls debe tener, como mínimo la direccion url y un id entre los parámetros del array
     * @param type $conListener
     */
    public function nonBlocking($urls) {
        $queue = new \cURL\RequestsQueue;
        $queue->getDefaultOptions()
                ->set(CURLOPT_USERAGENT, USER_AGENT)
                ->set(CURLOPT_TIMEOUT, 30)
                ->set(CURLOPT_RETURNTRANSFER, true);
        $queue->addListener('complete', function (\cURL\Event $event) {

        });

        foreach ($urls as $url) {
            $request = new \cURL\Request($url);
            $queue->attach($request);
        }
        while ($queue->socketPerform()) {
        }
    }

    public function descargaDocumentos($consultas) {
        $this->queue->getDefaultOptions()
                ->set(CURLOPT_USERAGENT, USER_AGENT)
                ->set(CURLOPT_TIMEOUT, $this->getTimeout())
                ->set(CURLOPT_RETURNTRANSFER, $this->getReturnTransfer());


        $this->queue->addListener('complete', function (\cURL\Event $event) use ($consultas) {
            foreach ($consultas as $consulta) {
                if (strcmp($event->request->getId(), $consulta['id']) === 0) {
                    if (!is_dir($consulta['carpeta'])) {
                        mkdir($consulta['carpeta']);
                    }
                    file_put_contents($consulta['carpeta'] . $consulta['archivo'], $event->response->getContent());
                }
            }
        });
        foreach ($consultas as $url) {
            if (isset($url['url']) && isset($url['id'])) {
                $request = new request($url['url']);
                $request->setId($url['id']);
                $this->queue->attach($request);
            }
        }
        $urls = [];
        foreach ($consultas as $consulta) {
            $urls [] = array($consulta ['id'], $consulta ['url']);
        }
        while ($this->queue->socketPerform()) {
            
        };
//        $this->nonBlocking($urls);
    }

    function getTimeout() {
        return $this->timeout;
    }

    function getContentType() {
        return $this->contentType;
    }

    function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    function getReturnTransfer() {
        return $this->returnTransfer;
    }

    function setReturnTransfer($returnTransfer) {
        $this->returnTransfer = $returnTransfer;
    }

    function getQueue() {
        return $this->queue;
    }

    function setQueue($queue) {
        $this->queue = $queue;
    }

}
