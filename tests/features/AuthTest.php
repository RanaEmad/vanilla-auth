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

class Authest extends TestCase
{
    protected $baseUrl;
    protected $db;
    protected function setUp(): void
    {
        $this->baseUrl = getenv("BASE_URL");
        require "app/config/database.php";
        $this->db =  MysqlConnection::getConnection();
        $this->client = new Client(['cookies' => true]);
        $this->jar = new CookieJar();
    }
    public function testLoadLogin()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $response = $this->client->request('GET', $uri);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testLoginSuccess()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => $user["plainPassword"]
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($user["firstname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["lastname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["email"], $content);
    }
    public function testLoginInvalidCredentials()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => "loremepsum"
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("invalid credentials", $content);
    }
    public function testLoginMissingResource()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => "email@dummy.com",
                'password' => "loremepsum"
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("invalid credentials", $content);
    }
    protected function tearDown(): void
    {
        $query = "TRUNCATE TABLE users;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $this->client = null;
        $this->jar = null;
    }
}
