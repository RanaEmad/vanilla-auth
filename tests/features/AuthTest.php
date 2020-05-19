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

class AuthTest extends TestCase
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
                'password' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase($user["firstname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["lastname"], $content);
        $this->assertStringContainsStringIgnoringCase($user["email"], $content);
    }
    public function testLoginNoCsrf()
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
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
    }
    public function testLoginInvalidCredentials()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => "loremepsum",
                'csrf' => $this->getCsrf("/users/auth/login")
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
                'password' => "loremepsum",
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("invalid credentials", $content);
    }
    public function testLoginRequiredParameters()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => "",
                'password' => "",
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The email is required", $content);
        $this->assertStringContainsStringIgnoringCase("The password is required", $content);
    }
    public function testLoginInvalidEmail()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => "loremepsum",
                'password' => "loremepsum",
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The Email is not valid email", $content);
    }
    protected function getCsrf($uri)
    {
        $uri = $this->baseUrl . $uri;

        $response = $this->client->request('GET', $uri, ['cookies' => $this->jar]);
        $content = $response->getBody()->getContents();
        $pattern = '#<input type="hidden" name="csrf" value="(.*?)"#si';
        preg_match($pattern, $content, $matches);
        $token = $matches[1];
        return $token;
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
