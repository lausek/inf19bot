<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser;

class Lesson
{
    public $day;
    public $from, $to;
    public $title;
    public $docent;
    public $location;
}

function parse(string $text) : array
{
    $lessons = [];
    $parsing = new Lesson;
    foreach (explode("\n", $text) as $line)
    {
        // match location
        if (1 === preg_match('(.+/.+)\S\s+', $line))
        {
            $parsing->location = $line;
        }
    }
    return $lessons;
}

class NextCommand extends Command
{
    function run() : String
    {
        $schedule_url = Util::get_config('schedule_url');
        $msg = "" . Language::get('CMD_NEXT_GENERAL');
        $msg .= "\n" . Language::get('CMD_NEXT_DATE') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_FROM') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_TILL') . ":";
        $msg .= "\n[open document]($schedule_url)";
        return $msg;
    }
}
