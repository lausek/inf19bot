<?php

require_once __DIR__ . '/../vendor/autoload.php';

// A `Check` will be run periodically by `api/tick.php`. Use these
// classes to perform checks for updates inside online services.

abstract class Check extends Command
{
    abstract function run(Response $response, $update = null);

    static function get_classname(string $name)
    {
        return ucwords($name) . "Check";
    }

    // returns a list of all checks
    static function get_all() : array
    {
        $found = [];
    
        foreach (scandir(__DIR__ . '/checks') as $fname)
        {
            if ('.' === $fname || '..' === $fname)
            {
                continue;
            }
            $cmd_name = pathinfo($fname, PATHINFO_FILENAME);
            $found[$cmd_name] = Check::get_classname($cmd_name);
        }
    
        return $found;
    }
    
    static function tick(Response $response)
    {
        foreach (Check::get_all() as $check => $classname)
        {
            $instance = new $classname;
            $instance->run($response);
        }
    }
}
