<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\Router;
use VanillaAuth\Models\User;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        Router::$routes = null;
    }

    public function testGet()
    {
        Router::get("users", "UserController@index");
        $this->assertIsArray(Router::$routes["get"]);
        $this->assertEquals("UserController@index", Router::$routes["get"]["users"]);
    }
    public function testPost()
    {
        Router::post("users", "UserController@index");
        $this->assertIsArray(Router::$routes["post"]);
        $this->assertEquals("UserController@index", Router::$routes["post"]["users"]);
    }
    public function testPut()
    {
        Router::put("users", "UserController@index");
        $this->assertIsArray(Router::$routes["put"]);
        $this->assertEquals("UserController@index", Router::$routes["put"]["users"]);
    }
    public function testDelete()
    {
        Router::delete("users", "UserController@index");
        $this->assertIsArray(Router::$routes["delete"]);
        $this->assertEquals("UserController@index", Router::$routes["delete"]["users"]);
    }

    public function testCheckRouteConflict()
    {
        Router::$routes["get"]["users"] = "UserController@index";
        try {
            Router::checkRouteConflict("users", "get");
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertStringContainsStringIgnoringCase("Route conflict for users with users and needs to be changed", $e->getMessage());
        }
    }

    /**
     * @dataProvider getUriProvider
     */
    public function testGetUri($baseUrl, $fullUri, $expected)
    {
        $uri = Router::getUri($baseUrl, $fullUri);
        $this->assertEquals($expected, $uri);
    }

    public function getUriProvider()
    {
        return [
            ["http://localhost:8888/vanilla-auth/", "http://localhost:8888/vanilla-auth/users/auth/login", "users/auth/login"],
            ["http://dummydomain.com/", "http://dummydomain.com/users/auth/login", "users/auth/login"],
            ["http://dummydomain.com", "http://dummydomain.com/users/123", "users/123"],
        ];
    }

    protected function tearDown(): void
    {
        Router::$routes = null;
    }
}
