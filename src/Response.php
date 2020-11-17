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

    function is_nerds() : bool
    {
        return $this->chat_id == Cache::get_nerds_id();
    }

    function is_empty() : bool
    {
        return empty($this->entities);
    }

    function add_document(string $name, string $url)
    {
        $this->entities[] = new Document($name, $url);
    }

    function add_keyboard(string $topic, callable $callback) : Keyboard
    {
        $keyboard = new Keyboard($topic, $callback);
        $this->entities[] = $keyboard;
        return $keyboard;
    }

    function add_message(string $content, string $markup='markdown')
    {
        $this->entities[] = new Message($content, $markup);
    }

    function send()
    {
        $client = Util::get_client();

        foreach ($this->entities as $entity)
        {
            $request = null;

            if ($entity instanceof Document)
            {
                $request = $client->sendDocument($this->chat_id, $entity->url, null, $entity->name);
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

            if ($entity instanceof Message)
            {
                $request = $client->sendMessage($this->chat_id, $entity->content, $entity->markup);
            }

            if ($request !== null && $request->ok !== true)
            {
                throw new Exception("sending response failed" . var_export($request, true));
                return;
            }
        }
    }
}
