<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class BeerCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_BEER_HELP');
    }

    function run($update = null)
    {
        $keyboard = new Keyboard();
        $keyboard->add_button('yes', 1);
        $keyboard->add_button('no', 0);
        return ['hey', $keyboard];
    }
}
