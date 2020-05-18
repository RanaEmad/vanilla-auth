<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";


require "bootstrap.php";

use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\Pagination;
use VanillaAuth\Core\Request;
use VanillaAuth\Middleware\HttpMiddleware;

class PaginationTest extends TestCase
{
    protected $pagination;
    protected function setUp(): void
    {
        $_GET = [];
        $_REQUEST = [];
        $this->pagination = new Pagination("users", 30, 3);
    }

    public function testGetOffset()
    {
        $_GET["page"] = 3;
        $offset = $this->pagination->getOffset();
        $this->assertEquals(6, $offset);
    }
    public function testGetOffsetPageNotSet()
    {
        $offset = $this->pagination->getOffset();
        $this->assertEquals(0, $offset);
    }
    public function testGetCurrentPage()
    {
        $_GET["page"] = 3;
        $page = $this->pagination->getCurrentPage();
        $this->assertEquals(3, $page);
    }
    public function testGetCurrentPageNotSet()
    {
        $page = $this->pagination->getCurrentPage();
        $this->assertEquals(1, $page);
    }

    public function testGetRows()
    {
        $rows = $this->pagination->getRows();
        $this->assertEquals(3, $rows);
    }

    /**
     * @dataProvider getPaginationLinksProvider
     */
    public function testGetPaginationLinks($page, $expected)
    {
        $_GET["page"] = $page;
        $links = $this->pagination->getPaginationLinks();
        $this->assertEquals($expected, $links);
    }

    public function getPaginationLinksProvider()
    {
        return [
            [
                5,
                [
                    1 => "http://localhost:8888/vanilla-auth/users?page=1",
                    2 => "http://localhost:8888/vanilla-auth/users?page=2",
                    3 => "http://localhost:8888/vanilla-auth/users?page=3",
                    4 => "http://localhost:8888/vanilla-auth/users?page=4",
                    5 => "http://localhost:8888/vanilla-auth/users?page=5",
                    6 => "http://localhost:8888/vanilla-auth/users?page=6",
                    7 => "http://localhost:8888/vanilla-auth/users?page=7",
                    8 => "http://localhost:8888/vanilla-auth/users?page=8",
                    9 => "http://localhost:8888/vanilla-auth/users?page=9",
                    10 => "http://localhost:8888/vanilla-auth/users?page=10",
                    "previous" => "http://localhost:8888/vanilla-auth/users?page=4",
                    "next" => "http://localhost:8888/vanilla-auth/users?page=6",
                    "current" => "5"
                ]
            ],
            [
                1,
                [
                    1 => "http://localhost:8888/vanilla-auth/users?page=1",
                    2 => "http://localhost:8888/vanilla-auth/users?page=2",
                    3 => "http://localhost:8888/vanilla-auth/users?page=3",
                    4 => "http://localhost:8888/vanilla-auth/users?page=4",
                    5 => "http://localhost:8888/vanilla-auth/users?page=5",
                    10 => "http://localhost:8888/vanilla-auth/users?page=10",
                    "previous" => "",
                    "next" => "http://localhost:8888/vanilla-auth/users?page=2",
                    "current" => "1"
                ]
            ],
            [
                10,
                [
                    1 => "http://localhost:8888/vanilla-auth/users?page=1",
                    6 => "http://localhost:8888/vanilla-auth/users?page=6",
                    7 => "http://localhost:8888/vanilla-auth/users?page=7",
                    8 => "http://localhost:8888/vanilla-auth/users?page=8",
                    9 => "http://localhost:8888/vanilla-auth/users?page=9",
                    10 => "http://localhost:8888/vanilla-auth/users?page=10",
                    "previous" => "http://localhost:8888/vanilla-auth/users?page=9",
                    "next" => "",
                    "current" => "10"
                ]
            ]
        ];
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_REQUEST = [];
        $this->pagination = null;
    }
}
