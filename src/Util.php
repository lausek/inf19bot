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
    
    // no type for $received because we cannot have nullable yet...
    static function is_authorized(string $secret_name, $received)
    {
        if (!isset($received))
        {
            return false;
        }
        
        $key = Util::load_secret_from(Util::path("/secret/$secret_name"));
    
        if (null === $key)
        {
            return false;
        }
        
        return $received === $key;
    }

    // check if content of secret $secret_name matches the one $received by the script. only process $execute with the request is authorized.
    // no type for $received because we cannot have nullable yet...
    static function protect_call_using(string $secret_name, $received, callable $execute)
    {
        Log::trace("access requested for ${_SERVER['REQUEST_URI']}");
        if (Util::is_authorized($secret_name, $received))
        {
            Log::trace('access granted');
            $execute();
        }
        else
        {
            Log::trace('access denied');
            die();
        }
    }

    static function set_webhook_active(bool $active) : string
    {
        $url = $active ? Util::get_config('webhook_url') : '';
        $cons = Util::get_config('webhook_connections') ?? 20;
        $subscribe_to = Util::get_config('webhook_subscribe') ?? [];
    
        $client = Util::get_client();
        $result = $client->setWebhook($url, null, $cons, $subscribe_to);
    
        if (true === $result->ok)
        {
            Log::trace("webhook enabled: $active");
            return "webhook enabled: $active";
        }
        else
        {
            Log::trace($result->description);
            return $result->description;
        }
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
