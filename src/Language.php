<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Wrapper for language files in `data/lang`. Supports lazy-loading.

class Language
{
    public static $loaded = null;

    public static function load(string $file)
    {
        self::$loaded = Util::load_json_file($file);
    }

    public static function get(string $key) : string
    {
        if (null === self::$loaded)
        {
            $location = Util::get_config('language');
            self::load(Util::path($location));
        }
        if (!isset(self::$loaded[$key]))
        {
            Log::etrace("language key $key requested but not found");
            return null;
        }
        return self::$loaded[$key];
    }

    public static function get_array(string $key) : array
    {
        return ['yes', 'yeah'];
    }
}
