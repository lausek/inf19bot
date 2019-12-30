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

class NextCommand extends Command implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_NEXT_HELP');
    }

    function run($update = null) : string
    {
        $timetable_url = Util::get_config('timetable_url');
        $msg = "" . Language::get('CMD_NEXT_GENERAL');
        $msg .= "\n" . Language::get('CMD_NEXT_DATE') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_FROM') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_TILL') . ":";
        $msg .= "\n[" . Language::get('GEN_OPEN_ORIGINAL') . "]($timetable_url)";
        return $msg;
    }
}
