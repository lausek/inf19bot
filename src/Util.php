<?php

require_once __DIR__ . '/../vendor/autoload.php';

use TuriBot\Client;

class Util
{
    static function path($postfix)
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if ('api' == basename($root))
        {
            $root = dirname($root);
        }

        $root .= '/data';

        if (strpos($postfix, '/') === 0)
        {
            return "$root$postfix";
        }
        else
        {
            return "$root/$postfix";
        }
    }
    
    static function load_secret_from($fpath)
    {
        $secret = null;
        $handle = @fopen($fpath, 'r');
        if (false !== $handle)
        {
            $secret = trim(fgets($handle));
            fclose($handle);
        }
        else
        {
            Log::trace("no secret at $fpath");
        }
        return $secret;
    }
    
    static function is_authorized()
    {
        if (!isset($_POST['key']))
        {
            return false;
        }
        
        $given = $_POST['key'];
        $key = self::load_secret_from(Util::path('/secret/key'));
    
        if (null === $key)
        {
            return false;
        }
        
        return $given === $key;
    }
    
    static function critical_section()
    {
        Log::trace("access requested for ${_SERVER['REQUEST_URI']}");
        if (!Util::is_authorized())
        {
            Log::trace('access denied');
            die();
        }
        Log::trace('access granted');
    }
    
    static function get_client()
    {
        static $client = null;
    
        if (null === $client)
        {
            $token = self::load_secret_from(Util::path('/secret/tgtoken'));
            
            if (null === $token)
            {
                die();
            }
            
            $client = new Client($token);
        }
    
        return $client;
    }
    
    static function get_config($key)
    {
        static $config = null;
    
        if (null === $config)
        {
            $raw = file_get_contents(Util::path('config.json'));
            if (false === $raw)
            {
                Log::trace('no config found');
                $config = [];
            }
            else
            {
                $parsed = json_decode($raw);
                if (null === $parsed)
                {
                    Log::trace('json error: ' . json_last_error_msg());
                }
                $config = (array) $parsed;
            }
        }
    
        if (array_key_exists($key, $config))
        {
            return $config[$key];
        }
    
        Log::trace("config value $key requested but not found");
        return null;
    }
    
    static function load_json_file($path)
    {
        $content = file_get_contents($path);
        if (false === $content)
        {
            Log::trace("file $path not found");
            return null;
        }
        return (array) json_decode($content);
    }
}
