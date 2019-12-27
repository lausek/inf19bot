<?php

require_once __DIR__ . '/../vendor/autoload.php';

// A check will be run periodically using the `tick` script

abstract class Check extends Command
{
    abstract function run() : String;

    static function get_available()
    {
        $found = [];
    
        foreach (scandir(__DIR__ . '/checks') as $fname)
        {
            if ('.' === $fname || '..' === $fname)
            {
                continue;
            }
            $cmd_name = pathinfo($fname, PATHINFO_FILENAME);
            $found[$cmd_name] = realpath(__DIR__ . "/checks/$fname");
        }
    
        return $found;
    }
}
