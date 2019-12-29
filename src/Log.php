<?php

class Log
{
    public const INFO = 'INFO';
    public const WARN = 'WARN';
    public const ERROR = 'ERROR';

    static function format($msg, $level, $display_callstack=false)
    {
        $stamp = date('Y-m-d h:i:s');

        if ($display_callstack)
        {
            $stack = "<error>";

            foreach (debug_backtrace() as $frame)
            {
                if (__FILE__ !== $frame['file'])
                {
                    var_dump($frame);
                    $stack = "${frame['file']}:${frame['line']}";
                    $stack .= " ${frame['function']}(";
                    $stack .= implode(" ", $frame['args']);
                    $stack .= ")";
                    break;
                }
            }
            
            return "[$stamp][$level] $msg called as $stack";
        }

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

        $formatted = Log::format($msg, Log::ERROR, true);

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
