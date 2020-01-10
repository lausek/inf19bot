<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Display all commands that implement `HasHelp`.

class BeerCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_BEER_HELP');
    }

    function run($update = null) : string
    {
        return 'beer here';
    }
}
