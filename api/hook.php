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
        $id = $client->sendMessage($chat_id, $output->topic, 'markdown', null, null, null, $keyboard);
        $output->set_message_id($id);
        return;
    }
    $client->sendMessage($chat_id, $output, 'markdown');
}

$client = Util::get_client();
$update = $client->getUpdate();

// Log::trace(var_export($update, true));

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
