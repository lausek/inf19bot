<?php

require_once __DIR__ . '/../vendor/autoload.php';

abstract class Command
{
    public $args = [];

    function set_args($args = [])
    {
        $this->args = $args;
    }

    abstract function run() : String;

    static function get_available()
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
}
