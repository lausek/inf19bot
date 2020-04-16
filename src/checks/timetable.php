<?php

require_once __DIR__ . '/../../vendor/autoload.php';

// Check if timetable was updated. Create a notification
// message if so.

class TimetableCheck extends Check
{
    function run(Response $response, $update = null)
    {
        $url = Util::get_config('timetable_url');
        if (null !== $url)
        {
            $remote_hash = hash_file('sha256', $url);

            if (null === $this->cache['last_hash']
            || $this->cache['last_hash'] !== $remote_hash)
            {
                $msg = Language::get('CHK_TIMETABLE_UPDATED');
                $msg .= " [";
                $msg .= Language::get('GEN_OPEN_ORIGINAL');
                $msg .= "]($url)";

                Util::inform_nerds($msg);

                $this->cache['last_hash'] = $remote_hash;
                $this->cache['last_update'] = date('Y-m-d h:i:s');
            }
        }
        return '';
    }
}
