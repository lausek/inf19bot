<?php

require_once __DIR__ . '/../vendor/autoload.php';

register_shutdown_function('Util::shutdown');

$client = Util::get_client();
$update = $client->getUpdate();

if (isset($update->message))
{
    $chat_id = $client->easy->chat_id;

    if ('group' === $update->message->chat->type)
    {
        if (null === Cache::get_nerds_id())
        {
            Cache::set_nerds_id($chat_id);
        }
    }

    $cmd = CommandParser::process($update->message->text);
    if (false !== $cmd)
    {
        $output = $cmd->run($update);
        $client->sendMessage($chat_id, $output, 'markdown');
    }
}
