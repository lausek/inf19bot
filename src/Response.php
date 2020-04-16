<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

class Response
{
    private $chat_id;
    private $entities = [];

    function __construct($chat_id)
    {
        $this->chat_id = $chat_id;
    }

    function is_empty() : bool
    {
    }

    function add_message(string $content, string $markup=null)
    {
        $this->entities[] = new Message();
    }

    function add_keyboard(string $topic) : Keyboard
    {
        $keyboard = new Keyboard($topic);
        $this->entities[] = $keyboard;
        return $keyboard;
    }

    function send()
    {
        $client = Util::get_client();

        foreach ($this->entities as $entity)
        {
            if ($entity instanceof Message)
            {
                $request = $client->sendMessage($this->chat_id, $entity->content, 'markdown');
            }

            if ($entity instanceof Keyboard)
            {
                $keyboard = $entity->get();
                $request = $client->sendMessage($this->chat_id, $output->topic, 'markdown', null, null, null, $keyboard);
                if (true === $request->ok)
                {
                    $id = ChatMessageId::from($this->chat_id, $request->result->message_id);
                    $entity->set_id($id);
                }
            }
        }
    }
}
