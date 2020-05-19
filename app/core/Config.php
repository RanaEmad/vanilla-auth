<?php

namespace VanillaAuth\Core;

use Exception;

class Config
{
    private static $config;
    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }
    public static function get($key)
    {
        if (array_key_exists($key, self::$config)) {
            return self::$config[$key];
        } else {
            throw new Exception("Config key doesn't exist");
        }
    }

    public static function setAll($data)
    {
        self::$config = $data;
    }
    public static function getAll()
    {
        return self::$config;
    }
}
