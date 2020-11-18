<?php

$check = null;

if (isset($argv[2]))
{
    $check = $argv[2];
}

if (isset($_GET['check']))
{
    $check = $_GET['check'];
}

if (null === $check)
{
    echo 'no argument given';
    die();
}

$_SERVER['DOCUMENT_ROOT'] = __DIR__;
require_once __DIR__ . '/vendor/autoload.php';

include("src/checks/$check.php");
$cls = ucwords($check) . 'Check';
$obj = new $cls();

$response = new Response(0);
$obj->run($response);

var_dump($response);
