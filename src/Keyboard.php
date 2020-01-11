<?php

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

    public function set_message_id($id)
    {
        $cache = new Cache('Callback');

        $cache[$id] = get_class($this->callback[0]);

        if (null !== $this->callback)
        {
            call_user_func($this->callback, $id);
        }
    }
}
