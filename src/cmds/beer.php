<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class BeerCommand extends Command implements HasHelp
{
    const ANSWER_YES = 1;
    const ANSWER_NO = 0;

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

        $count = BeerCommand::count_yn($cmid);
        Util::get_client()->editMessageText(
            $cmid->chat_id,
            $cmid->message_id,
            null,
            $this->generate_message($count),
            'markdown',
            null,
            (array) $update->callback_query->message->reply_markup
        );
    }

    function run($update = null)
    {
        $keyboard = new Keyboard($this->generate_message(), [$this, 'add_callback_id']);

        $positive_answers = Language::get_array('CMD_BEER_ANSWERS_YES');
        $negative_answers = Language::get_array('CMD_BEER_ANSWERS_NO');

        $yes_idx = array_rand($positive_answers);
        $no_idx = array_rand($negative_answers);
        $keyboard->add_button($positive_answers[$yes_idx], BeerCommand::ANSWER_YES);
        $keyboard->add_button($negative_answers[$no_idx], BeerCommand::ANSWER_NO);

        return $keyboard;
    }

    function generate_message(array $count = null)
    {
        $msg = Language::get('CMD_BEER_QUESTION');

        if (null !== $count)
        {
            $msg .= "\n";
            $msg .= "\n" . Language::get('CMD_BEER_COUNT_YES') . $count[BeerCommand::ANSWER_YES];
            $msg .= "\n" . Language::get('CMD_BEER_COUNT_NO') . $count[BeerCommand::ANSWER_NO];
        }

        return $msg;
    }

    function count_yn(ChatMessageId $cmid)
    {
        $id = (string) $cmid;
        $yes = 0;
        $no = 0;

        foreach ($this->cache[$id] as $answer)
        {
            switch ($answer)
            {
            case BeerCommand::ANSWER_YES:
                $yes += 1;
                break;
            case BeerCommand::ANSWER_NO:
                $no += 1;
                break;
            }
        }
        return [BeerCommand::ANSWER_YES => $yes, BeerCommand::ANSWER_NO => $no];
    }
}
