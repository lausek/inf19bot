<?php

require_once __DIR__ . '/../vendor/autoload.php';

class CommandParser
{
    static function parse($query)
    {
        $cmd = [];
        $parts = explode(' ', substr($query, 1));
        $cmd['name'] = $parts[0];
        $cmd['args'] = array_slice($parts, 1);
        return $cmd;
    }
    
    static function process($raw)
    {
        $query = trim($raw);
    
        // commands must start with /
        if (0 !== strpos($query, '/'))
        {
            return false;
        }
    
        $cmd = self::parse($query);
        $cmds = Command::get_available();
    
        if (!array_key_exists($cmd['name'], $cmds))
        {
            return false;
        }
    
        require_once($cmds[$cmd['name']]);
    
        $classprefix = ucwords($cmd['name']); 
        $classname = "${classprefix}Command";
        $instance = new $classname;
        $instance->set_args($cmd['args']);
        return $instance;
    }
}
