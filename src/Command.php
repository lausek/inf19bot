<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Marker interface. Commands will only be displayed in help if
// this interface was implemented.
interface HasHelp
{
    function help() : string;
}

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

    abstract function run($update = null) : string;

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
            $found[$cmd_name] = Command::get_classname($cmd_name);
        }
    
        return $found;
    }
}
