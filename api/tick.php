<?php

require_once __DIR__ . '/../vendor/autoload.php';

Util::protect_call_using('tick', $_GET['key'] ?? null, function ()
{
    foreach (Check::get_all() as $check => $classname)
    {
        $instance = new $classname;
        $instance->run();
    }
}, false);
