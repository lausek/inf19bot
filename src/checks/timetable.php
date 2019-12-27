<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if timetable was updated

class TimetableCheck extends Check
{
    function run() : String
    {
        $url = Util::get_config('timetable_url');
        if (null !== $url)
        {
            $remote_hash = hash_file('sha256', $url);

            // TODO: compare hash with cache
            // TODO: if not equal, notify and replace hash
        }
        return '';
    }
}
