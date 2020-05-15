<?php

namespace VanillaAuth\Core;

use function PHPUnit\Framework\matches;

class Router
{
    private static $routes;
    public function __construct()
    {
        self::$routes["get"] = [];
        self::$routes["post"] = [];
        self::$routes["put"] = [];
        self::$routes["delete"] = [];
    }
    public static function get($route, $function)
    {
        self::$routes["get"][$route] = $function;
    }
    public static function post($route, $function)
    {
        self::$routes["post"][$route] = $function;
    }
    public static function put($route, $function)
    {
        self::$routes["put"][$route] = $function;
    }
    public static function delete($route, $function)
    {
        self::$routes["delete"][$route] = $function;
    }


    public static function loadRoute()
    {
        $scriptName = trim(trim($_SERVER["SCRIPT_NAME"], basename($_SERVER["SCRIPT_NAME"])), "/");
        $uri = trim($_SERVER["REQUEST_URI"], "/");
        $uri = trim(str_replace($scriptName, "", $uri), "/");

        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);
        foreach (self::$routes[$requestMethod] as $route => $function) {

            $pattern =  RouteParser::construcRoutetPattern($route);

            if (preg_match($pattern, $uri, $macthes)) {
                $controller = RouteParser::getController($function);
                $method = RouteParser::getMethod($function);
                $args = RouteParser::getArgs($route, $uri);
                $className = "\\VanillaAuth\\Controllers\\$controller";
                $cont = new $className;
                if (count($args) > 0) {
                    $cont->{$method}(...$args);
                } else {
                    $cont->{$method}();
                }
            }
        }
    }
}
