<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Cache
{
    public $id;
    private $path;
    private $initialized = false;
    private $inner = [];

    private function ensure_cache_dir()
    {
        $cache_dir = Util::path('../cache');
        if (!is_dir($cache_dir))
        {
            if (!mkdir($cache_dir, 0755))
            {
                etrace('fs: cache exists but is not dir');
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

    function __set($name, $value)
    {
        $this->initialize();
        $this->inner[$name] = $value;
    }

    function __get($name)
    {
        $this->initialize();
        return $this->inner[$name];
    }

    function __isset($name)
    {
        $this->initialize();
        return isset($this->inner[$name]);
    }

    function __unset($name)
    {
        $this->initialize();
        unset($this->inner[$name]);
    }

    function __destruct()
    {
        $this->save();
    }
}
