<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class CommandTest extends TestCase
{
    public function testGet()
    {
        $got = Command::get_all();
        $this->assertEquals('help.php', basename($got['help']));
        $this->assertEquals('next.php', basename($got['next']));
        $this->assertEquals(2, count($got));
    }

    public function testLoading()
    {
        $got = Command::load_all();
        $this->assertEquals('HelpCommand', $got['help']);
        $this->assertEquals('NextCommand', $got['next']);
        $this->assertEquals(2, count($got));
    }
}
