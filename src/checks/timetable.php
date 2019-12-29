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

            if (null === $this->cache->last_hash
            || $this->cache->last_hash !== $remote_hash)
            {
                // TODO: notify
                $this->cache->last_hash = $remote_hash;
                $this->cache->last_update = date();
            }
        }
        return '';
    }
}
