<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use PHPUnit\Framework\TestCase;
use VanillaAuth\Middleware\HttpMiddleware;

class HttpMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        unset($_REQUEST["_method"]);
        unset($_SERVER["REQUEST_METHOD"]);
    }
    public function testHandleCustomMethod()
    {
        $_REQUEST["_method"] = "PUT";
        HttpMiddleware::handleCustomMethod();
        $this->assertEquals("PUT", $_SERVER["REQUEST_METHOD"]);
    }
    public function testHandleCustomMethodPost()
    {
        $_SERVER["REQUEST_METHOD"] = "POST";
        HttpMiddleware::handleCustomMethod();
        $this->assertEquals("POST", $_SERVER["REQUEST_METHOD"]);
    }

    protected function tearDown(): void
    {
        unset($_REQUEST["_method"]);
        unset($_SERVER["REQUEST_METHOD"]);
    }
}
