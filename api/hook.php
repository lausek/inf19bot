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
            $output->set_message_id($request->result->message_id);
        }
        return;
    }
    $client->sendMessage($chat_id, $output, 'markdown');
}

function main()
{
    $client = Util::get_client();
    $update = $client->getUpdate();
    
    Log::trace(var_export($update, true));
    
    // TODO: refactor
    if (isset($update->callback_query))
    {
        $cache = new Cache('Callback');
        $message_id = $update->callback_query->message->message_id;

        if (isset($cache[$message_id]))
        {
            $cmd = new $cache[$message_id]();
            $cmd->callback_on($message_id, $update);
        }
        else
        {
            Log::trace("$message_id is not in `Callback` cache");
        }
        
        $client->answerCallbackQuery($update->callback_query->id);
    }
    else if (isset($update->message))
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
}

main();
