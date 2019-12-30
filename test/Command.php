<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function testGet()
    {
        $got = Command::get_all();
        $this->assertEquals('HelpCommand', $got['help']);
        $this->assertEquals('NextCommand', $got['next']);
        $this->assertEquals(3, count($got));
    }
}
