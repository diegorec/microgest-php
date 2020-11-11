<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

if (!function_exists('log_error')) {

    function log_error($message, $context = []) {
        $date = date('YW');
        $name = 'error';
        $logger = new Logger($name);
        $logger->pushHandler(new StreamHandler(RUTA_LOG_BASE . "$name-$date.log", Logger::ERROR));
        $logger->pushHandler(new FirePHPHandler());
        $logger->error("[$message]", $context);
    }

}

if (!function_exists('log_info')) {

    function log_info($name, $message, $context = []) {
        $date = date('YW');
        $logger = new Logger($name);
        $logger->pushHandler(new StreamHandler(RUTA_LOG_BASE ."$name-$date.log", Logger::INFO));
        $logger->pushHandler(new FirePHPHandler());
        $logger->addInfo("[$message]", $context);
    }

}
