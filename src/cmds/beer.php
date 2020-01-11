<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class BeerCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_BEER_HELP');
    }

    function add_chat_id($id)
    {
        Log::trace("beer poll $id created");
    }

    function run($update = null)
    {
        $keyboard = new Keyboard(Language::get('CMD_BEER_QUESTION'), [$this, 'add_chat_id']);

        $yes = array_rand(Language::get_array('CMD_BEER_ANSWER_YES'));
        $no = array_rand(Language::get_array('CMD_BEER_ANSWER_NO'));
        $keyboard->add_button($yes, 1);
        $keyboard->add_button($no, 0);

        return $keyboard;
    }
}
