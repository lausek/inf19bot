<?php

if (!isset($argv[2]))
{
    echo 'no argument given';
    die();
}

$_SERVER['DOCUMENT_ROOT'] = __DIR__;
require_once __DIR__ . '/vendor/autoload.php';

$name = $argv[2];

include("src/checks/$name.php");
$cls = ucwords($name) . 'Check';
$obj = new $cls();

$response = new Response(0);
$obj->run($response);

var_dump($response);
