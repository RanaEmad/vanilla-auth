<?php

namespace VanillaAuth\Services;

use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Core\Session;

class Csrf
{
    public static function generateCsrf()
    {
        Session::setKey("csrf", bin2hex(random_bytes(24)));
    }
    public static function verifyCsrf()
    {
        $csrf = Request::post("csrf");
        if (strtolower($_SERVER["REQUEST_METHOD"]) == "put") {
            $csrf = Request::put("csrf");
        }
        if (!$csrf || !Session::getKey("csrf") || $csrf != Session::getKey("csrf")) {
            Loader::view("errors/msg", ["msg" => "Access Denied"]);
            exit();
        }
        return true;
    }
    public static function getCsrfField()
    {
        self::generateCsrf();
        return '<input type="hidden" name="csrf" value="' . Session::getKey("csrf") . '" />';
    }
}
