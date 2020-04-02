<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

if (!function_exists('log_error')) {

    function log_error($filename, $message, $context = []) {
        $logger = new Logger("microgest-php");
        $logger->pushHandler(new StreamHandler($filename, Logger::ERROR));
        $logger->pushHandler(new FirePHPHandler());
        $logger->error("[$message]", $context);
    }

}