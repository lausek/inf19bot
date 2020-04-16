<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// forces a pull on the checks

class WhatsupCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_WHATSUP_HELP');
    }

    function run(Response $response, $update = null)
    {
        Check::tick($response);

        if ($response->is_empty())
        {
            $possible = Language::get_array('CMD_WHATSUP_DEFAULT');
            $idx = array_rand($possible);
            $response->add_message($possible[$idx]);
        }
    }
}
