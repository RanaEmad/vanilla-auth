<?php

namespace VanillaAuth\Core;

use Exception;
use VanillaAuth\Middleware\HttpMiddleware;

use function PHPUnit\Framework\matches;

class Router
{
    public static $routes;
    public static function get($route, $function)
    {
        $route = trim($route, "/");
        $function = trim($function);
        self::checkRouteConflict($route, "get");
        self::$routes["get"][$route] = $function;
    }
    public static function post($route, $function)
    {
        $route = trim($route, "/");
        $function = trim($function);
        self::$routes["post"][$route] = $function;
    }
    public static function put($route, $function)
    {
        $route = trim($route, "/");
        $function = trim($function);
        self::$routes["put"][$route] = $function;
    }
    public static function delete($route, $function)
    {
        $route = trim($route, "/");
        $function = trim($function);
        self::$routes["delete"][$route] = $function;
    }

    public static function checkRouteConflict($uri, $verb)
    {
        if (self::$routes && array_key_exists($verb, self::$routes)) {
            foreach (self::$routes[$verb] as $route => $function) {
                $uriPattern =  RouteParser::replaceRoutetPattern($uri);
                $pattern =  RouteParser::construcRoutetPattern($route);

                if (preg_match($pattern, $uriPattern, $macthes)) {
                    throw new Exception("Route conflict for $uri with $route and needs to be changed");
                }
            }
        }
    }

    public static function getUri($baseUrl,$fullUri)
    {
        $uri = trim(str_replace($baseUrl, "", $fullUri), "/");
        $queryPos = strpos($uri, "?");

        //remove query string
        if ($queryPos !== false) {
            $uri = substr($uri, 0, $queryPos);
        }

        return $uri;
    }


    public static function loadRoute()
    {
        $fullUri = array_key_exists("HTTPS", $_SERVER) ? "https" : "http";
        $fullUri .= "://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $uri = self::getUri(BASE_URL,$fullUri);

        HttpMiddleware::handleCustomMethod();

        $requestMethod = strtolower($_SERVER["REQUEST_METHOD"]);

        $found = false;
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
                $found = true;
                break;
            }
        }
        if (!$found) {
            Loader::view("errors/404");
        }
    }
}
