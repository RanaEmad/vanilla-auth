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

    protected function logUserIn()
    {
        $uri = $this->baseUrl . "/users/auth/login";

        $user = UserFactory::create();
        $this->client->request('POST', $uri, [
            'form_params' => [
                'email' => $user["email"],
                'password' => $user["plainPassword"],
                'csrf' => $this->getCsrf("/users/auth/login")
            ],
            'cookies' => $this->jar
        ]);
        return $user;
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

        UserFactory::$id = 1;
        $this->client = null;
        $this->jar = null;
    }
}
