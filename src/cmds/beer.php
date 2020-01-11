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
        $this->cache[$id] = [];
        Log::trace("beer poll $id created");
    }

    function callback_on($message_id, $update = null)
    {
        if (!isset($this->cache[$message_id]))
        {
            Log::etrace("answer on $message_id for BeerCommand but message is not cached.");
            return;
        }
        // $this->cache[$message_id][$update->callback_query->from->id] = $update->callback_query->data;
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
