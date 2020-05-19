<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Faker\Factory;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Factories\UserFactory;
use VanillaAuth\Traits\GuzzleAuthTrait;

class EditUserTest extends TestCase
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
    public function testEditSuccess()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => "loremepsum",
                'lastname' => $user["lastname"],
                'email' => $user["email"],
                'csrf' => $this->getCsrf("/users/" . $user["id"] . "/edit")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("Data updated successfully!", $content);
    }
    public function testEditNoCsrf()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => "loremepsum",
                'lastname' => $user["lastname"],
                'email' => $user["email"]
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
    }
    public function testEditRequiredParameters()
    {
        $this->logUserIn();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => "",
                'lastname' => "",
                'email' => "",
                'csrf' => $this->getCsrf("/users/" . $user["id"] . "/edit")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The firstname is required", $content);
        $this->assertStringContainsStringIgnoringCase("The lastname is required", $content);
    }
    public function testEditMaxCharParameters()
    {
        $this->logUserIn();
        $faker = Factory::create();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => $faker->lexify(str_repeat("?", 21)),
                'lastname' => $faker->lexify(str_repeat("?", 21)),
                'email' => $faker->lexify(str_repeat("?", 51)) . "@dummy.com",
                'csrf' => $this->getCsrf("/users/" . $user["id"] . "/edit")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The Firstname maximum is 20", $content);
        $this->assertStringContainsStringIgnoringCase("The lastname maximum is 20", $content);
        $this->assertStringContainsStringIgnoringCase("The email maximum is 50", $content);
    }
    public function testEditInvalidEmail()
    {
        $this->logUserIn();
        $faker = Factory::create();
        $user = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => "loremepsum",
                'csrf' => $this->getCsrf("/users/" . $user["id"] . "/edit")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The Email is not valid email", $content);
    }
    public function testEditDuplicateEmail()
    {
        $this->logUserIn();
        $faker = Factory::create();
        $user = UserFactory::create();
        $duplicateUser = UserFactory::create();
        $uri = $this->baseUrl . "/users/" . $user["id"];

        $response = $this->client->request('PUT', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => $duplicateUser["email"],
                'csrf' => $this->getCsrf("/users/" . $user["id"] . "/edit")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("Email " . $duplicateUser["email"] . " already exists", $content);
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
