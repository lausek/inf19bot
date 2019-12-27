<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

final class CheckTest extends TestCase
{
    public function testGet()
    {
        $got = Check::get_all();
        $this->assertEquals('timetable.php', basename($got['timetable']));
        $this->assertEquals(1, count($got));
    }

    public function testLoading()
    {
        $got = Check::load_all();
        $this->assertEquals('TimetableCheck', $got['timetable']);
        $this->assertEquals(1, count($got));
    }
}
