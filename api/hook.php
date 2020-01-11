<?php

require_once __DIR__ . '/../vendor/autoload.php';

register_shutdown_function('Util::shutdown');

function handle_cmd_output($client, $output)
{
    $chat_id = $client->easy->chat_id;

    if (is_array($output))
    {
        foreach ($output as $o)
        {
            handle_cmd_output($client, $o);
        }
        return;
    }
    if ($output instanceof Keyboard)
    {
        $keyboard = $output->get();
        $client->sendMessage($chat_id, 'question', 'markdown', null, null, null, $keyboard);
        return;
    }
    $client->sendMessage($chat_id, $output, 'markdown');
}

$client = Util::get_client();
$update = $client->getUpdate();

if (isset($update->message))
{
    if ('group' === $update->message->chat->type)
    {
        if (null === Cache::get_nerds_id())
        {
            Cache::set_nerds_id($client->easy->chat_id);
        }
    }

    $cmd = CommandParser::process($update->message->text);
    if (false !== $cmd)
    {
        handle_cmd_output($client, $cmd->run($update));
    }
}
