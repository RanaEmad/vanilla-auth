<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Traits\GuzzleAuthTrait;

class CountriesTest extends TestCase
{
    use GuzzleAuthTrait;
    protected $baseUrl;
    protected $db;
    protected $client;
    protected $jar;
    protected $faker;
    protected function setUp(): void
    {
        $this->baseUrl = getenv("BASE_URL");
        require "app/config/database.php";
        $this->db =  MysqlConnection::getConnection();
        $this->client = new Client(['cookies' => true]);
        $this->jar = new CookieJar();
    }
    public function testCountriesPage()
    {
        $this->logUserIn();
        $uri = $this->baseUrl . "/countries";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);

        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("countries", $content);
        $this->assertStringContainsStringIgnoringCase("name", $content);
        $this->assertStringContainsStringIgnoringCase("region", $content);
        $this->assertStringContainsStringIgnoringCase("currency", $content);
        $this->assertStringContainsStringIgnoringCase("currency code", $content);
        $this->assertStringContainsStringIgnoringCase("flag", $content);
    }
    public function testCountriesPageNotLoggedIn()
    {
        $uri = $this->baseUrl . "/countries";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);

        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }

    protected function tearDown(): void
    {
        $query = "TRUNCATE TABLE users;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        UserFactory::$id = 1;
        $this->client = null;
        $this->jar = null;
    }
}
