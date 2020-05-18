<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";


// require "bootstrap.php";

use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\Session;

class SessionTest extends TestCase
{
    protected $pagination;
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testSet()
    {
        $session = [
            "testKey" => "loremepsum",
            "testKey2" => "loremepsum2",
        ];
        Session::set($session);
        $this->assertNotEmpty($_SESSION);
        $this->assertEquals(2, count($_SESSION));
        $this->assertArrayHasKey("testKey", $_SESSION);
        $this->assertArrayHasKey("testKey2", $_SESSION);
        $this->assertEquals("loremepsum", $_SESSION["testKey"]);
        $this->assertEquals("loremepsum2", $_SESSION["testKey2"]);
    }
    public function testSetKey()
    {
        Session::setKey("test", "loremepsum");
        $this->assertNotEmpty($_SESSION);
        $this->assertEquals(1, count($_SESSION));
        $this->assertArrayHasKey("test", $_SESSION);
        $this->assertEquals("loremepsum", $_SESSION["test"]);
    }
    /**
     * @dataProvider getKeyProvider
     */
    public function testGetKey($key, $expected)
    {
        $_SESSION["test"] = "loremepsum";
        $value = Session::getKey($key);
        $this->assertEquals($expected, $value);
    }

    public function testUnsetKey()
    {
        $_SESSION["test"] = "loremepsum";
        Session::unsetKey("test");
        $this->assertEquals(0, count($_SESSION));
    }
    public function testClear()
    {
        $_SESSION["test"] = "loremepsum";
        Session::clear();
        $this->assertEquals(0, count($_SESSION));
    }

    public function getKeyProvider()
    {
        return [
            ["test", "loremepsum"],
            ["nonexistent", false]
        ];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
}
