<?php

require_once __DIR__ . '/../vendor/autoload.php';

abstract class Command
{
    public $args = [];
    public $cache;

    function __construct()
    {
        $this->cache = new Cache(get_class($this));
    }

    function set_args($args = [])
    {
        $this->args = $args;
    }

    abstract function run() : string;

    static function get_classname(string $name)
    {
        return ucwords($name) . "Command";
    }

    static function get_all()
    {
        $found = [];
    
        foreach (scandir(__DIR__ . '/cmds') as $fname)
        {
            if ('.' === $fname || '..' === $fname)
            {
                continue;
            }
            $cmd_name = pathinfo($fname, PATHINFO_FILENAME);
            $found[$cmd_name] = realpath(__DIR__ . "/cmds/$fname");
        }
    
        return $found;
    }

    static function load_all() : array
    {
        $loaded = [];
        foreach (self::get_all() as $name => $path)
        {
            require_once($path);
            $loaded[$name] = self::get_classname($name);
        }
        return $loaded;
    }
}
