<?php

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait LogTrait {

    public function addError(string $error) {
        $name = "clientes";
        $logger = new Logger($name);
        $logger->pushHandler(new StreamHandler($this->rutaLog, Logger::INFO));
        $logger->pushHandler(new FirePHPHandler());
        $logger->addError("[$error]");
    }

}