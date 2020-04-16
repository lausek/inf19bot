<?php

require_once __DIR__ . '/../vendor/autoload.php';

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

    function add_message(string $content, string $markup='markdown')
    {
        $this->entities[] = new Message($content, $markup);
    }

    function add_keyboard(string $topic, callable $callback) : Keyboard
    {
        $keyboard = new Keyboard($topic, $callback);
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
                $request = $client->sendMessage($this->chat_id, $entity->content, $entity->markup);
            }

            if ($entity instanceof Keyboard)
            {
                $keyboard = $entity->get();
                $request = $client->sendMessage($this->chat_id, $entity->topic, 'markdown', null, null, null, $keyboard);
                if (true === $request->ok)
                {
                    $id = ChatMessageId::from($this->chat_id, $request->result->message_id);
                    $entity->set_id($id);
                }
            }
        }
    }
}
