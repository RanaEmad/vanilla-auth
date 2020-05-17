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

class UserTest extends TestCase
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
    public function testLoadUsersPage()
    {
        $this->logUserIn();
        $uri = $this->baseUrl . "/users";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);

        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("users", $content);
        $this->assertStringContainsStringIgnoringCase("table", $content);
    }
    public function testLoadUsersPageNotLoggedIn()
    {
        $uri = $this->baseUrl . "/users";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);

        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testLoadRegisterPage()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $response = $this->client->request('GET', $uri);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("register", $content);
        $this->assertStringContainsStringIgnoringCase("firstname", $content);
        $this->assertStringContainsStringIgnoringCase("lastname", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
        $this->assertStringContainsStringIgnoringCase("matchPassword", $content);
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
        $uri = $this->baseUrl . "/users/1";

        $response = $this->client->request('GET', $uri, ['http_errors' => false]);
        $content = $response->getBody()->getContents();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("resource not found", $content);
    }
    public function testLoadEditPage()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/" . $user["id"] . "/edit";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($user["firstname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["lastname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["email"], $content);
    }
    public function testLoadEditPageNotLoggedIn()
    {
        $user = UserFactory::create();

        $uri = $this->baseUrl . "/users/" . $user["id"] . "/edit";

        $response = $this->client->request('GET', $uri);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }
    public function testLoadEditPageMissingResource()
    {
        $uri = $this->baseUrl . "/users/1/edit";

        $response = $this->client->request('GET', $uri, ['http_errors' => false]);
        $content = $response->getBody()->getContents();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("resource not found", $content);
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
        $uri = $this->baseUrl . "/users/toggleAccount/{$user["id"]}/enable";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }

    public function testLoadResetPasswordPage()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/{$user["id"]}";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("reset password", $content);
        $this->assertStringContainsStringIgnoringCase("old password", $content);
        $this->assertStringContainsStringIgnoringCase("new password", $content);
        $this->assertStringContainsStringIgnoringCase("match password", $content);
    }
    public function testLoadResetPasswordPageMissingResource()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/100";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("resource not found", $content);
    }
    public function testLoadResetPasswordPageNotLoggedIn()
    {
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user['id'];

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("login", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
        $this->assertStringContainsStringIgnoringCase("password", $content);
    }

    public function testLoadResetPasswordPageUnauthorized()
    {
        $user = $this->logUserIn();
        $user2 = UserFactory::create();
        $uri = $this->baseUrl . "/users/auth/resetPassword/{$user2['id']}";

        $response = $this->client->request('GET', $uri, [
            'cookies' => $this->jar,
            'http_errors' => false
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("unauthorized access", $content);
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
    }
}
