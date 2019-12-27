<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class HelpCommand extends Command
{
    function run() : String
    {
        $output = Language::get('CMD_HELP_GREET') . "\n";
        foreach (self::get_all() as $name => $location)
        {
            $output .= "- /$name\n";
        }
        $output .= "\n" . Language::get('CMD_HELP_END');
        return $output;
    }
}
