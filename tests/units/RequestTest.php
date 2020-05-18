<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\Request;
use VanillaAuth\Middleware\HttpMiddleware;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        $_POST = [];
        $_GET = [];
        $_REQUEST = [];
    }
    public function testPostAttribute()
    {
        $_POST["test"] = "loremepsum";
        $value = Request::post("test");
        $this->assertEquals("loremepsum", $value);
    }
    public function testPostNoAttribute()
    {
        $_POST["test"] = "loremepsum";
        $_POST["test2"] = "loremepsum2";
        $values = Request::post();
        $this->assertEquals(2, count($values));
        $this->assertIsArray($values);
        $this->assertArrayHasKey("test", $values);
        $this->assertArrayHasKey("test2", $values);
    }
    public function testGetAttribute()
    {
        $_GET["test"] = "loremepsum";
        $value = Request::get("test");
        $this->assertEquals("loremepsum", $value);
    }
    public function testGettNoAttribute()
    {
        $_GET["test"] = "loremepsum";
        $_GET["test2"] = "loremepsum2";
        $values = Request::get();
        $this->assertEquals(2, count($values));
        $this->assertIsArray($values);
        $this->assertArrayHasKey("test", $values);
        $this->assertArrayHasKey("test2", $values);
    }
    public function testPutAttributeSpoof()
    {
        $_POST["test"] = "loremepsum";
        $_REQUEST["_method"] = "PUT";
        $value = Request::put("test");
        $this->assertEquals("loremepsum", $value);
    }
    public function testPuttNoAttributeSpoof()
    {
        $_POST["test"] = "loremepsum";
        $_POST["test2"] = "loremepsum2";
        $_REQUEST["_method"] = "PUT";
        $values = Request::put();
        $this->assertEquals(2, count($values));
        $this->assertIsArray($values);
        $this->assertArrayHasKey("test", $values);
        $this->assertArrayHasKey("test2", $values);
    }
    public function testPutAttributeNoSpoof()
    {
        $_POST["test"] = "loremepsum";
        $value = Request::put("test");
        $this->assertEquals(false, $value);
    }
    public function testPuttNoAttributeNoSpoof()
    {
        $_POST["test"] = "loremepsum";
        $_POST["test2"] = "loremepsum2";
        $values = Request::put();
        $this->assertEquals(0, count($values));
    }

    protected function tearDown(): void
    {
        $_POST = [];
        $_GET = [];
        $_REQUEST = [];
    }
}
