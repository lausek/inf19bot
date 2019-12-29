<?php

require_once __DIR__ . '/../vendor/autoload.php';

$client = Util::get_client();
$update = $client->getUpdate();

Log::trace('message received. info: '.var_export($update, true));

// file_put_contents('request', json_encode($update));
if ('group' === $update->message->chat->type)
{
    if (null === Util::get_nerds_id())
    {
        Util::set_nerds_id($update->message->chat->id);
    }
}

if (isset($update->message))
{
    $chat_id = $client->easy->chat_id;
    $cmd = CommandParser::process($update->message->text);
    if (false !== $cmd)
    {
        $output = $cmd->run();
        $client->sendMessage($chat_id, $output, 'markdown');
    }
}
