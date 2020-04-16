<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Message
{
    public $content;
    public $markup;

    function __construct(string $content, string $markup = 'markdown')
    {
        $this->content = $content;
        $this->markup = $markup;
    }
}
