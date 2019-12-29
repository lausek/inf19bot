<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Language
{
    public static $loaded = null;

    public static function load(string $file)
    {
        self::$loaded = Util::load_json_file($file);
    }

    public static function get(string $key)
    {
        if (null === self::$loaded)
        {
            $location = Util::get_config('language');
            self::load(Util::path($location));
        }
        if (!isset(self::$loaded[$key]))
        {
            Log::trace("language key $key requested but not found");
            return null;
        }
        return self::$loaded[$key];
    }
}
