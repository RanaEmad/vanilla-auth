<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\Config;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\Session;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Models\User;

class CountryTest extends TestCase
{
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
    protected function logUserIn()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => $user["plainPassword"]
            ],
            'cookies' => $this->jar
        ]);
        return $user;
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
