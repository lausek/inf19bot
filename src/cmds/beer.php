<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class BeerCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_BEER_HELP');
    }

    function add_callback_id(ChatMessageId $cmid)
    {
        $id = (string) $cmid;
        $this->cache[$id] = [];
        Log::trace("beer poll $id created");
    }

    function callback_on(ChatMessageId $cmid, $update = null)
    {
        $id = (string) $cmid;
        if (!isset($this->cache[$id]))
        {
            Log::etrace("answer on $id for BeerCommand but message is not cached.");
            return;
        }
        $this->cache[$id] = array_replace((array) $this->cache[$id], [
            $update->callback_query->from->id => $update->callback_query->data
        ]);
    }

    function run($update = null)
    {
        $keyboard = new Keyboard(Language::get('CMD_BEER_QUESTION'), [$this, 'add_callback_id']);

        $positive_answers = Language::get_array('CMD_BEER_ANSWERS_YES');
        $negative_answers = Language::get_array('CMD_BEER_ANSWERS_NO');

        $yes_idx = array_rand($positive_answers);
        $no_idx = array_rand($negative_answers);
        $keyboard->add_button($positive_answers[$yes_idx], 1);
        $keyboard->add_button($negative_answers[$no_idx], 0);

        return $keyboard;
    }
}
