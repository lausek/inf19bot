<?php

require_once __DIR__ . '/../vendor/autoload.php';

// A check will be run periodically using the `tick` script

abstract class Check extends Command
{
    abstract function run() : String;

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
            $found[$cmd_name] = realpath(__DIR__ . "/checks/$fname");
        }
    
        return $found;
    }

    static function load_all() : array
    {
        $loaded = [];
        foreach (self::get_all() as $name => $path)
        {
            require_once($path);
            $classprefix = ucwords($name); 
            $loaded[$name] = "${classprefix}Check";
        }
        return $loaded;
    }
}
