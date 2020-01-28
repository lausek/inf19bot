<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// forces a pull on the checks

class WhatsupCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_WHATSUP_HELP');
    }

    function run($update = null)
    {
        Check::tick();
        return '';
    }
}
