<?php

namespace conexiones;

/**
 * Description of Request
 *
 * @author diego.gonda
 */
class Request extends \cURL\Request {

    private $id;

    public function __construct($url = null) {
        parent::__construct($url);
    }

    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

}
