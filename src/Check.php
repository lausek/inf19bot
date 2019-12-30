<?php

require_once __DIR__ . '/../vendor/autoload.php';

// A check will be run periodically using the `tick` script

abstract class Check extends Command
{
    abstract function run($update = null) : String;

    static function get_classname(string $name)
    {
        return ucwords($name) . "Check";
    }

    static function get_all()
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
