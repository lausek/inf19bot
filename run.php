<?php

$check = null;
$send = false;

if (isset($argv[2]))
{
    $check = $argv[2];
}

if (isset($_GET['check']))
{
    $check = $_GET['check'];
}

if (isset($_GET['send']))
{
    $send = true;
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

$response = $send ? new Response(Cache::get_dev_ids()[0]) : new Response(null);
$obj->run($response);

var_dump($response);

if ($send)
{
    $response->send();
}
