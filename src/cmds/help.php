<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Display all commands that implement `HasHelp`.

class HelpCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_HELP_HELP');
    }

    function run($update = null)
    {
        $output = Language::get('CMD_HELP_GREET') . "\n\n";
        foreach (self::get_all() as $name => $classname)
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
