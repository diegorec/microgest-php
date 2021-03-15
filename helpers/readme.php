<?php

function readme(string $directory, string $filename = "readme.md") {
    $commands = commands_list($directory);
    _var_dump($commands);
}