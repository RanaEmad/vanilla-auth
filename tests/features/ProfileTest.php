<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Traits\GuzzleAuthTrait;

class ProfileTest extends TestCase
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
    public function testLoadProfilePage()
    {
        $user = $this->logUserIn();

        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($user["firstname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["lastname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["email"], $content);
    }
    public function testLoadProfilePageNotLoggedIn()
    {
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/{$user['id']}";

        $response = $this->client->request('GET', $uri);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testLoadProfilePageMissingResource()
    {
        $this->logUserIn();
        $uri = $this->baseUrl . "/users/100";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("resource not found", $content);
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
