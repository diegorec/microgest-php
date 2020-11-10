<?php

use phpcli\Colors;

function _echo_info ($string) {
    _echo_colors("info", $string);
}

function _echo_aviso ($string) {
    _echo_colors("aviso", $string);
}

function _echo_error ($string) {
    _echo_colors("error", $string);
}

function _echo_colors(string $color, $string) {
    $colors = new Colors();
    _echo($colors->$color($string));
}

function _echo($string) {
    if (MENSAJESTERMINAL || MODO_VERBOSE) {
        echo $string . PHP_EOL;
    }
}

function _var_dump(...$mixed) {
    if (MENSAJESTERMINAL || MODO_VERBOSE) {
        var_dump($mixed);
    }
}
