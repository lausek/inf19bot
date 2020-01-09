<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Log
{
    public const INFO = 'INFO';
    public const WARN = 'WARN';
    public const ERROR = 'ERROR';

    // if an error happens while writing, die()
    private static $writing = false;

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

    static function forward($msg)
    {
        static $ids = null;

        if (null === $ids)
        {
            $cache = new Cache('Ids');
            $ids = isset($cache->forward_err_to) ? $cache->forward_err_to : [];
        }

        if (!empty($ids))
        {
            $client = Util::get_client();
            foreach ($ids as $forward_to)
            {
                $client->sendMessage($forward_to, $msg, 'markdown');
            }
        }
    }

    static function write($msg)
    {
        if (self::$writing)
        {
            echo "error while writing trace";
            die();
        }

        self::$writing = true;
        $location = Util::get_config('tracefile');
        if (null !== $location)
        {
            $fname = Util::path($location);
            file_put_contents($fname, $msg . "\n", FILE_APPEND);
        }
        self::$writing = false;
    }

    static function trace($msg)
    {
        Log::write(Log::format($msg, Log::INFO));
    }

    static function etrace($msg)
    {
        $formatted = Log::format($msg, Log::ERROR, true);

        // report error to chosen Telegram users
        Log::forward($formatted);

        Log::write($formatted);
    }
}
