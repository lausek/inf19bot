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
        $request = $client->sendMessage($chat_id, $output->topic, 'markdown', null, null, null, $keyboard);
        if (true === $request->ok)
        {
            $id = ChatMessageId::from($chat_id, $request->result->message_id);
            $output->set_id($id);
        }
        return;
    }
    $client->sendMessage($chat_id, $output, 'markdown');
}

function handle_callback($client, $update)
{
    $cache = new Cache('Callback');
    $cmid = ChatMessageId::from(
        $update->callback_query->message->chat->id,
        $update->callback_query->message->message_id
    );
    $id = (string) $cmid;

    if (isset($cache[$id]))
    {
        $cmd = new $cache[$id]();
        $cmd->callback_on($cmid, $update);
    }
    else
    {
        Log::trace("$id is not in `Callback` cache");
    }

    $client->answerCallbackQuery($update->callback_query->id);
}

function handle_message($client, $update)
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

function main()
{
    $client = Util::get_client();
    $update = $client->getUpdate();
    
    Log::trace(var_export($update, true));
    
    if (isset($update->callback_query))
    {
        handle_callback($client, $update);
    }
    else if (isset($update->message))
    {
        handle_message($client, $update);
    }
}

main();
