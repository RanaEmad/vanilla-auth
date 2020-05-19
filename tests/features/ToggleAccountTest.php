<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Traits\GuzzleAuthTrait;

class ToggleAccountTest extends TestCase
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
    public function testLoadToggleAccountPageDisable()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}/disable";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("are you sure", $content);
        $this->assertStringContainsStringIgnoringCase("account", $content);
        $this->assertStringContainsStringIgnoringCase("disable", $content);
    }
    public function testLoadToggleAccountPageDisableNotLoggedIn()
    {
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user["id"]}/disable";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testLoadToggleAccountPageEnable()
    {
        $this->logUserIn();
        $user = UserFactory::create(["disabled" => 1]);
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}/enable";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("are you sure", $content);
        $this->assertStringContainsStringIgnoringCase("account", $content);
        $this->assertStringContainsStringIgnoringCase("enable", $content);
    }

    public function testLoadToggleAccountPageEnableNotLoggedIn()
    {
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/" . $user["id"] . "/enable";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }

    public function testToggleAccountSuccess()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}";

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "disabled" => 1,
                "csrf" => $this->getCsrf("/users/toggleAccount/" . $user['id'] . "/disable")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("account updated successfully", $content);
    }
    public function testToggleAccountNotLoggedIn()
    {
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}";

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "disabled" => 1,
                "csrf" => $this->getCsrf("/users/toggleAccount/" . $user['id'] . "/disable")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testToggleAccountNoParameter()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}";

        $response = $this->client->request('PUT', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
    }
    public function testToggleAccountNoCsrf()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/toggleAccount/{$user['id']}";

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "disabled" => 1
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
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
