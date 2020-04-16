<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Smalot\PdfParser;

// WIP: Get the next scheduled lesson. Requires parsing pdf.

class Lesson
{
    public $day;
    public $from, $to;
    public $title;
    public $docent;
    public $location;
}

class NextCommand extends Command // implements HasHelp
{
    function help() : string
    {
        return Language::get('CMD_NEXT_HELP');
    }

    function run(Response $response, $update = null)
    {
        $timetable_url = Util::get_config('timetable_url');
        $msg = "" . Language::get('CMD_NEXT_GENERAL');
        $msg .= "\n" . Language::get('CMD_NEXT_DATE') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_FROM') . ":";
        $msg .= "\n" . Language::get('CMD_NEXT_TIME_TILL') . ":";
        $msg .= "\n[" . Language::get('GEN_OPEN_ORIGINAL') . "]($timetable_url)";

        $response->add_message($msg);
    }
}
