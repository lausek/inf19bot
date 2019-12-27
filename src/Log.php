<?php

class Log
{
    static function trace($msg)
    {
        $location = Util::get_config('tracefile');
        if (null !== $location)
        {
            $fname = Util::path($location);
            $stamp = date('Y-m-d h:i:s');
            file_put_contents($fname, "[$stamp] $msg\n", FILE_APPEND);
        }
    }
}
