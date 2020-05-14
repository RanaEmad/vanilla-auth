<?php

namespace VanillaAuth\Core;

class RouteParser
{
    public static function construcRoutetPattern($route)
    {
        $variablePattern = "#:[a-z]+:#si";
        $route = preg_replace($variablePattern, "[a-z0-9]+", $route);
        $routePattern = "#^$route$#i";
        return $routePattern;
    }
    public static function getController($string)
    {
        $segments = explode("@", $string);
        return $segments[0];
    }

    public static function getMethod($string)
    {
        $segments = explode("@", $string);
        if (count($segments) > 1) {
            return $segments[1];
        }
        return "index";
    }

    public static function getArgs($route, $uri)
    {
        $args = [];
        $routeSegments = explode("/", $route);
        $uriSegments = explode("/", $uri);
        foreach ($routeSegments as $index => $value) {
            if ($value[0] == ":") {
                $args[] = $uriSegments[$index];
            }
        }
        return $args;
    }
}
