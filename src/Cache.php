<?php

require_once __DIR__ . '/../vendor/autoload.php';

// `Cache` will handle serialization of `Check` and `Command`
// data. Current format is json. Performance was not measured as
// it is sufficient for now.
// 
// ## Usage
//
// *id* usually equals the classname. Every instance of `Command` will
// have a `Cache` instance as well. However, storage is not accessed 
// until a read/write request to `Cache` attributes occurs.
//     
//     $cache = new Cache(<id>); // <id>.json was not opened
//     $cache['hashval'] = ...;    // <id>.json is loaded
//
// The destructor will persist the changes if the cache was initialized.

class Cache implements ArrayAccess
{
    public $id;
    private $path;
    private $initialized = false;
    private $inner = [];

    // which chat_id belongs to the nerd chat? this is important if we
    // want to forward messages like `TimetableCheck`.
    static function set_nerds_id($id)
    {
        $cache = new Cache('Ids');
        $cache['nerds'] = $id;
        Log::etrace("new nerds group chat id is $id");
    }

    static function get_nerds_id()
    {
        $cache = new Cache('Ids');
        return $cache['nerds'];
    }

    private function ensure_cache_dir()
    {
        $cache_dir = Util::path('../cache');
        if (!is_dir($cache_dir))
        {
            if (!mkdir($cache_dir, 0755))
            {
                Log::etrace('fs: cache exists but is not dir');
                exit();
            }
        }
    }

    private function initialize()
    {
        if (!$this->initialized)
        {
            $this->ensure_cache_dir();
            $load_result = Util::load_json_file($this->path);
            if (null !== $load_result)
            {
                $this->inner = $load_result;
            }
            $this->initialized = true;
        }
    }

    function save()
    {
        if ($this->initialized)
        {
            Util::write_json_file($this->path, $this->inner);
        }
    }

    function __construct($id)
    {
        $this->id = $id;
        $this->path = Util::path("../cache/$this->id.json");
    }

    function offsetSet($name, $value)
    {
        $this->initialize();
        $this->inner[$name] = $value;
    }

    function offsetGet($name)
    {
        $this->initialize();
        return $this->inner[$name];
    }

    function offsetExists($name)
    {
        $this->initialize();
        return isset($this->inner[$name]);
    }

    function offsetUnset($name)
    {
        $this->initialize();
        unset($this->inner[$name]);
    }

    function __destruct()
    {
        $this->save();
    }
}
