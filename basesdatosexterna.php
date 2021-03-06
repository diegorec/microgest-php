<?php

$basesdatos = array();
$basesdatos['rest'] = array(
    'database_type' => 'mysql',
    'database_name' => 'restful',
    'server' => '192.168.1.8',
    'username' => 'diego',
    'password' => '.#diego#.'
);

$basesdatos['catalogo'] = array(
    'database_type' => 'mysql',
    'database_name' => 'catalogo_online',
    'server' => '192.168.1.8',
    'username' => 'diego',
    'password' => '.#diego#.'
);

$basesdatos['historico'] = array(
    'database_type' => 'mysql',
    'database_name' => 'historico',
    'server' => '192.168.1.8',
    'username' => 'diego',
    'password' => '.#diego#.',
    'prefix' => 'his_',
    'option' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 1,
        PDO::ATTR_STRINGIFY_FETCHES => false,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ],
);

$basesdatos['cloud'] = array(
    'database_type' => 'mysql',
    'database_name' => 'microgest',
    'prefix' => 'mgc_',
    'server' => '192.168.1.197',
    'username' => 'diego',
    'password' => '.#diego#.'
);

$basesdatos['cloud_norte'] = array(
    'database_type' => 'mysql',
    'database_name' => 'microgest_norte',
    'prefix' => 'mgc_',
    'server' => '192.168.1.197',
    'username' => 'diego',
    'password' => '.#diego#.'
);

$basesdatos['cloud_canarias'] = array(
    'database_type' => 'mysql',
    'database_name' => 'microgest_canarias',
    'prefix' => 'mgc_',
    'server' => '192.168.1.197',
    'username' => 'diego',
    'password' => '.#diego#.'
);

$basesdatos['cloud_prisauto'] = array(
    'database_type' => 'mysql',
    'database_name' => 'microgest',
    'prefix' => 'mgc_',
    'server' => '212.170.108.4',
    'username' => 'admin',
    'password' => '.#kas_do#.'
);
