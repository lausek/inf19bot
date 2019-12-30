<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Webmozart\PathUtil\Path;
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
        $path = strpos($postfix, '/') === 0 ? "$root$postfix" : "$root/$postfix";

        
        return Path::canonicalize($path);
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
            Log::etrace("no secret at $fpath");
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
        if (Util::is_authorized($secret_name, $received))
        {
            Log::etrace("access granted for ${_SERVER['REQUEST_URI']}");
            $execute();
        }
        else
        {
            Log::etrace("access denied for ${_SERVER['REQUEST_URI']}");
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
            Log::etrace("webhook enabled: $active, url: $url");
            return "webhook enabled: $active, url: $url";
        }
        else
        {
            Log::etrace($result->description);
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
            $config = Util::load_json_file(Util::path('config.json'));
            assert(null !== $config);
        }
    
        if (array_key_exists($key, $config))
        {
            return $config[$key];
        }
    
        Log::etrace("config value $key requested but not found");
        return null;
    }
    
    static function load_json_file($path)
    {
        $content = @file_get_contents($path);
        if (false === $content)
        {
            Log::etrace("file $path not found");
            return null;
        }
        $decoded = json_decode($content);
        if (null === $decoded)
        {
            Log::etrace('json error: ' . json_last_error_msg());
            return null;
        }
        return (array) $decoded;
    }

    static function write_json_file($path, $obj)
    {
        if (false === file_put_contents($path, json_encode($obj)))
        {
            Log::etrace("error writing file $path");
        }
    }

    static function add_forward_id($id)
    {
        $cache = new Cache('Ids');
        if (isset($cache->forward_err_to))
        {
            if (!in_array($id, $cache->forward_err_to))
            {
                $cache->forward_err_to = array_merge($cache->forward_err_to, [$id]);
            }
        }
        else
        {
            $cache->forward_err_to = [$id];
        }
        Log::etrace("forwarding errors to $id now");
    }

    static function set_nerds_id($id)
    {
        $cache = new Cache('Ids');
        $cache->nerds = $id;
        Log::etrace("new nerds group chat id is $id");
    }

    static function get_nerds_id()
    {
        $cache = new Cache('Ids');
        return $cache->nerds;
    }

    static function inform_nerds($msg)
    {
        $chat_id = Util::get_nerds_id();
        if (null === $chat_id)
        {
            Log::etrace("no group id configured");
            return;
        }
        $client = Util::get_client();
        $client->sendMessage($chat_id, $msg, 'markdown');
    }
}
