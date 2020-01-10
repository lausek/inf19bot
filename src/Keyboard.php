<?php

class Keyboard
{
    private $buttons;

    public function __construct(array $buttons = null)
    {
        if (null !== $buttons)
        {
            $this->buttons = $buttons;
        }
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
            'inline_keyboard' => $this->buttons,
        ];
    }
}
