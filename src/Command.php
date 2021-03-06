<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Marker interface. Commands will only be displayed in `help` if
// this interface was implemented. Note that all commands **can 
// be executed** anyway.

interface HasHelp
{
    function help() : string;
}

// A `Command` can be performed by all users of a bot. Some commands
// are not listed inside `help`.

abstract class Command
{
    public $args = [];
    public $cache;

    function __construct()
    {
        $this->cache = new Cache(get_class($this));
    }

    function callback_on(ChatMessageId $cmid, $update = null)
    {}

    function data($fname = null)
    {
        $clsname = get_class($this);
        $path = Util::path("/cmds/$clsname/$fname");
        return Util::load_json_file($path);
    }

    function set_args($args = [])
    {
        $this->args = $args;
    }

    abstract function run(Response $response, $update = null);

    static function get_classname(string $name)
    {
        return ucwords($name) . "Command";
    }

    // returns a list of all commands
    static function get_all() : array
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

            // allows importing classes without `composer update`
            require_once __DIR__ . '/cmds/' . $fname;
        }
    
        return $found;
    }
}
