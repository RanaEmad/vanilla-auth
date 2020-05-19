<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use PHPUnit\Framework\TestCase;
use VanillaAuth\Services\Csrf;

class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_SERVER["REQUEST_METHOD"] = "";
    }

    public function testGenerateCsrf()
    {
        $csrf = Csrf::generateCsrf();
        $this->assertArrayHasKey("csrf", $_SESSION);
    }
    public function testVerifyCsrf()
    {
        $_POST["csrf"] = "loremepsum";
        $_SESSION["csrf"] = "loremepsum";
        $_SERVER["REQUEST_METHOD"] = "POST";
        $verify = Csrf::verifyCsrf();
        $this->assertEquals(true, $verify);
    }
    public function testGetCsrfField()
    {
        $field = Csrf::getCsrfField();
        $expected = '<input type="hidden" name="csrf" value="' . $_SESSION["csrf"] . '" />';
        $this->assertEquals($expected, $field);
        $this->assertEquals("", "");
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
        $_SERVER["REQUEST_METHOD"] = "";
    }
}
