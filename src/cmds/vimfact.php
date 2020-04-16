<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Sends a random vim fact

class VimfactCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_VIMFACT_HELP');
    }

    function run(Response $response, $update = null)
    {
        $facts = $this->data('facts.json');
        $fact = Language::get('CMD_VIMFACT_NOTHING');

        if (null !== $facts)
        {
            $idx = array_rand($facts);
            $ridx = $idx + 1;
            $fact = "*Vim Fact #$ridx*\n" . $facts[$idx];
        }

        return $fact;
    }
}
