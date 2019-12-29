<?php

class Log
{
    public const INFO = 'INFO';
    public const WARN = 'WARN';
    public const ERROR = 'ERROR';

    static function format($msg, $level)
    {
        $stamp = date('Y-m-d h:i:s');
        return "[$stamp][$level] $msg";
    }

    static function write($msg)
    {
        $location = Util::get_config('tracefile');
        if (null !== $location)
        {
            $fname = Util::path($location);
            file_put_contents($fname, $msg . "\n", FILE_APPEND);
        }
    }

    static function trace($msg)
    {
        Log::write(Log::format($msg, Log::INFO));
    }

    static function etrace($msg)
    {
        static $ids = null;

        if (null === $ids)
        {
            $cache = new Cache('Ids');
            $ids = isset($cache->forward_err_to) ? $cache->forward_err_to : [];
        }

        $formatted = Log::format($msg, Log::ERROR);

        if (!empty($ids))
        {
            $client = Util::get_client();
            foreach ($cache->forward_err_to as $forward_to)
            {
                $client->sendMessage($forward_to, $formatted, 'markdown');
            }
        }

        Log::write($formatted);
    }
}
