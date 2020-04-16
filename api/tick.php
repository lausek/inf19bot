<?php

require_once __DIR__ . '/../vendor/autoload.php';

register_shutdown_function('Util::shutdown');

Util::protect_call_using('tick', $_GET['key'] ?? null, function ()
{
    $response = new Response(Cache::get_nerds_id());
    Check::tick($response);
    $response->send();
}, false);
