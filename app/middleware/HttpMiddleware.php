<?php

namespace VanillaAuth\Middleware;

use GuzzleHttp\Client;
use VanillaAuth\Core\Request;

class HttpMiddleware
{
    public static function handleCustomMethod()
    {
        if (array_key_exists("_method", $_REQUEST)) {
            if (strtolower($_REQUEST["_method"]) == "put") {
                $_SERVER["REQUEST_METHOD"] = "PUT";
            }
        }
    }
}
