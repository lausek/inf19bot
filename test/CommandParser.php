<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class CommandParserTest extends TestCase
{
    public function testParsing()
    {
        $expected = [
            'name' => 'help',
            'args' => []
        ];
        $got = CommandParser::parse('/help');
        $this->assertEquals($expected, $got);
    }

    public function testParsingArgs()
    {
        $expected = [
            'name' => 'help',
            'args' => ['abc', 'def']
        ];
        $got = CommandParser::parse('/help abc def');
        $this->assertEquals($expected, $got);
    }

    public function testProcessing()
    {
        $this->assertEquals(false, CommandParser::process(''));
        $this->assertEquals(false, CommandParser::process('hi'));

        $this->assertInstanceOf(
            HelpCommand::class,
            CommandParser::process('/help')
        );

        $this->assertInstanceOf(
            HelpCommand::class,
            CommandParser::process('    /help')
        );

        $this->assertInstanceOf(
            HelpCommand::class,
            CommandParser::process('/help   ')
        );
    }
}
