<?php

require_once __DIR__ . '/../vendor/autoload.php';

Util::protect_call_using('tick', $_GET['key'] ?? null, function ()
{
    foreach (Util::load_all(Check::get_all()) as $check => $path)
    {
        echo $path;
    }
});
