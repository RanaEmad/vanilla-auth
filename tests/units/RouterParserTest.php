<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\RouteParser;
use VanillaAuth\Core\Router;
use VanillaAuth\Models\User;

class RouterParserTest extends TestCase
{

    /**
     * @dataProvider getContructRoutePatternProvider
     */
    public function testConstructRoutePattern($route, $expected)
    {
        $pattern = RouteParser::construcRoutetPattern($route);
        $this->assertEquals($expected, $pattern);
    }
    /**
     * @dataProvider getControllerProvider
     */
    public function testGetController($string, $expected)
    {
        $controller = RouteParser::getController($string);
        $this->assertEquals($expected, $controller);
    }

    /**
     * @dataProvider getMethodProvider
     */
    public function testGetMethod($string, $expected)
    {
        $method = RouteParser::getMethod($string);
        $this->assertEquals($expected, $method);
    }
    /**
     * @dataProvider getArgsProvider
     */
    public function testGetArgs($route, $uri, $expected)
    {
        $args = RouteParser::getArgs($route, $uri);
        $this->assertEquals($expected, $args);
    }

    public function getControllerProvider()
    {
        return [
            ["UserController@index", "UserController"],
            ["AuthController@login", "AuthController"],
            ["CountryController@index", "CountryController"],
            ["", ""],
            ["@index", ""],
        ];
    }
    public function getMethodProvider()
    {
        return [
            ["UserController@index", "index"],
            ["AuthController@login", "login"],
            ["CountryController@index", "index"],
            ["", "index"],
            ["UserController@", ""],
        ];
    }
    public function getArgsProvider()
    {
        return [
            ["users/:id:", "users/123", [123]],
            ["users/:id:/edit", "users/123/edit", [123]],
            ["/", "/", []],
            ["users/login", "users/login", []],
            ["users/:id:/:string:", "users/123/string", [123, "string"]],
        ];
    }
    public function getContructRoutePatternProvider()
    {
        return [
            ["users/auth/login",  "#^users/auth/login$#i"],
            ["users/:id:/login",  "#^users/[a-z0-9]+/login$#i"],
            ["users/:id:/login/:string:",  "#^users/[a-z0-9]+/login/[a-z0-9]+$#i"],
        ];
    }
}
