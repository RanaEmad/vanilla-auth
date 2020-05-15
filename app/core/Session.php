<?php

namespace VanillaAuth\Core;

class Session
{

    public static function start()
    {
        session_start();
    }
    public function set($data)
    {
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
    public function setKey($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public function clear()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
    }
    public function destroy()
    {
        session_destroy();
    }
}
