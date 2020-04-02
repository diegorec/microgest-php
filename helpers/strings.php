<?php

function _echo($string) {
    if (MENSAJESTERMINAL || MODO_VERBOSE) {
        echo $string . PHP_EOL;
    }
}

function _var_dump($mixed) {
    if (MENSAJESTERMINAL || MODO_VERBOSE) {
        var_dump($mixed);
    }
}
