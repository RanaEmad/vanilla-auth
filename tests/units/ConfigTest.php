<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Exception;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\Config;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        Config::setAll([]);
    }
    public function testSet()
    {
        Config::set('key', "loremepsum");
        $this->assertEquals(1, count(Config::getAll()));
        $this->assertEquals("loremepsum", Config::get("key"));
    }
    public function testGet()
    {
        Config::set('key', "loremepsum");
        $this->assertEquals("loremepsum", Config::get("key"));
    }
    public function testGetException()
    {
        $this->expectException(Exception::class);
        Config::get('key', "loremepsum");
    }
    public function testSetAll()
    {
        $data = [
            "key" => "loremepsum",
            "key2" => "loremepsum2",
            "key3" => "loremepsum3",
        ];
        Config::setAll($data);
        $this->assertEquals(3, count(Config::getAll()));
        $this->assertEquals("loremepsum", Config::get("key"));
        $this->assertEquals("loremepsum2", Config::get("key2"));
        $this->assertEquals("loremepsum3", Config::get("key3"));
    }
    public function testGetAll()
    {
        $data = [
            "key" => "loremepsum",
            "key2" => "loremepsum2",
            "key3" => "loremepsum3",
        ];
        Config::setAll($data);
        $all = Config::getAll();
        $this->assertEquals(3, count($all));
        $this->assertArrayHasKey("key", $all);
        $this->assertArrayHasKey("key2", $all);
        $this->assertArrayHasKey("key3", $all);
    }

    protected function tearDown(): void
    {
        Config::setAll([]);
    }
}
