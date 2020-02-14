<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Webmozart\PathUtil\Path;
use TuriBot\Client;

// Collection of homeless functions.

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

    static function load_secret_from_json($fpath)
    {
        $content = @file_get_contents($fpath);
        if (false === $content)
        {
            Log::etrace("no secret at $fpath");
            return [];
        }
        return (array) json_decode($content);
    }

    static function get_asset_url(string $filename)
    {
        $secret = Util::load_secret_from(Util::path('secret/asset'));
        $key = hash('sha256', $secret);
        $path = urlencode($filename);

        $prefix = 0 !== strpos($_SERVER['HTTP_HOST'], 'http') ? 'https://' : '';

        return "$prefix{$_SERVER['HTTP_HOST']}/asset.php?key=$key&path=$path";
    }
    
    // no type for $received because we cannot have nullable yet...
    static function is_authorized(string $secret_name, $received, $hashbase=null)
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

        // if we expect a hashed value, we need to hash the secret as well
        if (null !== $hashbase)
        {
            $key = hash($hashbase, $key);
        }
        
        return $received === $key;
    }

    // check if content of secret $secret_name matches the one $received by the script. only process $execute with the request is authorized.
    // no type for $received because we cannot have nullable yet...
    static function protect_call_using(string $secret_name, $received, callable $execute, $log=true, $hashbase=null)
    {
        if (Util::is_authorized($secret_name, $received, $hashbase))
        {
            if ($log)
            {
                Log::etrace("access granted for ${_SERVER['REQUEST_URI']}");
            }
            $execute();
        }
        else
        {
            if ($log)
            {
                Log::etrace("access denied for ${_SERVER['REQUEST_URI']}");
            }
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
        if (isset($cache['forward_err_to']))
        {
            if (!in_array($id, $cache['forward_err_to']))
            {
                $cache['forward_err_to'] = array_merge($cache['forward_err_to'], [$id]);
            }
        }
        else
        {
            $cache['forward_err_to'] = [$id];
        }
        Log::etrace("forwarding errors to $id now");
    }

    static function inform_nerds($msg, $markup='markdown') : bool
    {
        $chat_id = Cache::get_nerds_id();
        if (null === $chat_id)
        {
            Log::etrace("no group id configured");
            return false;
        }

        $client = Util::get_client();

        $send_response = $client->sendMessage($chat_id, $msg, $markup);
        if (true !== $send_response['ok'])
        {
            Log::trace(var_export($send_response, true));
            return false;
        }

        return true;
    }

    // callback if php error happens
    static function shutdown()
    {
        $err = error_get_last();
        if (E_ERROR === $err['type'])
        {
            $msg = var_export($err, true);
            Log::forward($msg);
        }
    }
}
