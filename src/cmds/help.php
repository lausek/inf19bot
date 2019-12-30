<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class HelpCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_HELP_HELP');
    }

    function run($update = null) : string
    {
        $output = Language::get('CMD_HELP_GREET') . "\n";
        foreach (self::load_all() as $name => $classname)
        {
            $cmd = new $classname;
            if ($cmd instanceof HasHelp)
            {
                $output .= "- /$name: " . $cmd->help() . "\n";
            }
        }
        $output .= "\n" . Language::get('CMD_HELP_END');
        return $output;
    }
}
