<?php

require_once __DIR__ . '/../vendor/autoload.php';

register_shutdown_function('Util::shutdown');

Util::protect_call_using('tick', $_GET['key'] ?? null, function ()
{
    foreach (Check::get_all() as $check => $classname)
    {
        $instance = new $classname;
        $instance->run();
    }
}, false);
