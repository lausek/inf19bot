<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Response
{
    private $entities = [];

    function is_empty() : bool
    {}

    function add_message(string $content, string $markup=null)
    {}

    function add_keyboard(string $content) : Keyboard
    {}
}
