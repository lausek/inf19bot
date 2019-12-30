<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Helper utility for determining the `Command` to trigger.

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
        $cmds = Command::get_all();

        Log::trace("searching for command " . $cmd['name']);
    
        if (!array_key_exists($cmd['name'], $cmds))
        {
            return false;
        }
    
        $classname = $cmds[$cmd['name']];
        $instance = new $classname;
        $instance->set_args($cmd['args']);
        return $instance;
    }
}
