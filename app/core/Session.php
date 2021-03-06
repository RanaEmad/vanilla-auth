<?php

namespace VanillaAuth\Core;

class Session
{


    public static function start()
    {
        session_start();
    }
    public static function set($data)
    {
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
    public static function setKey($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    public static function get($data)
    {
        return $_SESSION;
    }
    public static function getKey($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return false;
    }
    public static function unsetKey($key)
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
        return false;
    }
    public static function clear()
    {
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
    }
    public static function destroy()
    {
        if ($_SESSION) {
            session_destroy();
        }
    }

    public static function checkLogin()
    {
        if (!$_SESSION || !isset($_SESSION["logged"]) || !isset($_SESSION["id"])) {
            self::setKey("error", "Please, log in first");
            return redirect("users/auth/login");
            exit();
        }
    }
    public static function loggedIn()
    {
        if ($_SESSION && isset($_SESSION["logged"]) && isset($_SESSION["id"])) {
            return $_SESSION["id"];
        }
        return false;
    }
}
