<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Document
{
    public $name;
    public $url;

    function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }
}
