<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Traits\GuzzleAuthTrait;

class ResetPasswordTest extends TestCase
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

    public function testLoadResetPasswordPage()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

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

    public function testResetPasswordSuccess()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "oldPassword" => $user["plainPassword"],
                "newPassword" => "loremepsum",
                "matchPassword" => "loremepsum",
                "csrf" => $this->getCsrf("/users/auth/resetPassword/" . $user["id"]),
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("Password updated successfully!", $content);
    }
    public function testResetPasswordNoCsrf()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "oldPassword" => $user["plainPassword"],
                "newPassword" => "loremepsum",
                "matchPassword" => "loremepsum"
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
    }
    public function testResetPasswordRequiredParameters()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "oldPassword" => "",
                "newPassword" => "",
                "matchPassword" => "",
                "csrf" => $this->getCsrf("/users/auth/resetPassword/" . $user["id"]),
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The OldPassword is required", $content);
        $this->assertStringContainsStringIgnoringCase("The newPassword is required", $content);
        $this->assertStringContainsStringIgnoringCase("The matchPassword is required", $content);
    }

    public function testResetPasswordPasswordMismatch()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "oldPassword" => $user["plainPassword"],
                "newPassword" => "loremepsum",
                "matchPassword" => "mismatch",
                "csrf" => $this->getCsrf("/users/auth/resetPassword/" . $user["id"]),
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The MatchPassword must be same with newPassword", $content);
    }
    public function testResetPasswordOldPasswordMismatch()
    {
        $user = $this->logUserIn();
        $uri = $this->baseUrl . "/users/auth/resetPassword/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            "form_params" => [
                "oldPassword" => "loremepsum",
                "newPassword" => "loremepsum",
                "matchPassword" => "loremepsum",
                "csrf" => $this->getCsrf("/users/auth/resetPassword/" . $user["id"]),
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("Your old password didn't match", $content);
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
