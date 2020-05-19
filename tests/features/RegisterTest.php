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

class RegisterTest extends TestCase
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
    public function testRegisterSuccess()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => $user["email"],
                'password' => $user["plainPassword"],
                'matchPassword' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("first name", $content);
        $this->assertStringContainsStringIgnoringCase("last name", $content);
        $this->assertStringContainsStringIgnoringCase("email", $content);
    }
    public function testRegisterNoCsrf()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => $user["email"],
                'password' => $user["plainPassword"],
                'matchPassword' => $user["plainPassword"]
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("access denied", $content);
    }
    public function testRegisterRequiredParameters()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => "",
                'lastname' => "",
                'email' => "",
                'password' => "",
                'matchPassword' => "",
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The firstname is required", $content);
        $this->assertStringContainsStringIgnoringCase("The lastname is required", $content);
        $this->assertStringContainsStringIgnoringCase("The password is required", $content);
        $this->assertStringContainsStringIgnoringCase("The matchpassword is required", $content);
    }
    public function testRegisterMaxCharParameters()
    {
        $uri = $this->baseUrl . "/users/auth/register";
        $faker = Factory::create();
        $password = $faker->lexify(str_repeat("?", 51));
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $faker->lexify(str_repeat("?", 21)),
                'lastname' => $faker->lexify(str_repeat("?", 21)),
                'email' => $faker->lexify(str_repeat("?", 51)) . "@dummy.com",
                'password' => $password,
                'matchPassword' => $password,
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The Firstname maximum is 20", $content);
        $this->assertStringContainsStringIgnoringCase("The lastname maximum is 20", $content);
        $this->assertStringContainsStringIgnoringCase("The email maximum is 50", $content);
        $this->assertStringContainsStringIgnoringCase("The password maximum is 50", $content);
    }
    public function testRegisterInvalidEmail()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => "loremepsum",
                'password' => $user["plainPassword"],
                'matchPassword' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The Email is not valid email", $content);
    }
    public function testRegisterDuplicateEmail()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $duplicateUser = UserFactory::create();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => $duplicateUser["email"],
                'password' => $user["plainPassword"],
                'matchPassword' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("Email " . $duplicateUser["email"] . " already exists", $content);
    }
    public function testRegisterPasswordMismatch()
    {
        $uri = $this->baseUrl . "/users/auth/register";

        $user = UserFactory::make();
        $response = $this->client->request('POST', $uri, [
            'form_params' => [
                'firstname' => $user["firstname"],
                'lastname' => $user["lastname"],
                'email' => $user["email"],
                'password' => "loremepsum",
                'matchPassword' => "mismatch",
                'csrf' => $this->getCsrf("/users/auth/register")
            ],
            'cookies' => $this->jar
        ]);
        $content = $response->getBody()->getContents();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsStringIgnoringCase("The MatchPassword must be same with password", $content);
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
