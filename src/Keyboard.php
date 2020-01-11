<?php

class ChatMessageId
{
    public $chat_id, $message_id;

    static function from($chat_id, $message_id)
    {
        $that = new ChatMessageId;
        $that->chat_id = $chat_id;
        $that->message_id = $message_id;
        return $that;
    }

    static function from_raw($raw)
    {
        $parts = explode(':', $raw);
        $that = new ChatMessageId;
        $that->chat_id = $parts[0];
        $that->message_id = $parts[1];
        return $that;
    }

    function __toString()
    {
        return "$this->chat_id:$this->message_id";
    }
}

class Keyboard
{
    public $topic;
    private $buttons = [];
    private $callback = null;

    public function __construct(string $topic, callable $callback)
    {
        $this->topic = $topic;
        $this->callback = $callback;
    }

    public function add_button(string $text, $data)
    {
        $this->buttons[] = [
            'text' => $text,
            'callback_data' => $data
        ];
    }

    public function get()
    {
        return [
            'inline_keyboard' => [$this->buttons],
        ];
    }

    public function set_id(ChatMessageId $id)
    {
        $cache = new Cache('Callback');

        $cache[(string) $id] = get_class($this->callback[0]);

        if (null !== $this->callback)
        {
            call_user_func($this->callback, $id);
        }
    }
}
