<?php

require_once __DIR__ . '/../vendor/autoload.php';

// A `Check` will be run periodically by `api/tick.php`. Use these
// classes to perform checks for updates inside online services.

abstract class Check extends Command
{
    abstract function run($update = null) : string;

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
}
