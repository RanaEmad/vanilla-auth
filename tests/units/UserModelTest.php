<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use PDO;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Models\User;

class UserModelTest extends TestCase
{
    protected $baseUrl;
    protected $db;
    protected $userModel;
    protected function setUp(): void
    {
        $this->baseUrl = getenv("BASE_URL");
        require "app/config/database.php";
        $this->db =  MysqlConnection::getConnection();
        $this->userModel = new User();
    }

    public function testGetUserByEmail()
    {
        $password = password_hash("loremepsum", PASSWORD_DEFAULT);
        $query = "INSERT INTO `users`  ( `firstname`, `lastname`, `email`, `password`, `disabled`) VALUES ( 'john', 'doe', 'john@doe.com', '$password', '0');";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $this->userModel->getUserByEmail("john@doe.com");
        $this->assertIsObject($result);
        $this->assertEquals("john@doe.com", $result->email);
    }
    public function testGetUserByEmailNonExisting()
    {
        $password = password_hash("loremepsum", PASSWORD_DEFAULT);
        $query = "INSERT INTO `users`  ( `firstname`, `lastname`, `email`, `password`, `disabled`) VALUES ( 'john', 'doe', 'john@doe.com', '$password', '0');";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $this->userModel->getUserByEmail("test@test.com");
        $this->assertEmpty($result);
    }

    public function testCountUsers()
    {
        $password = password_hash("loremepsum", PASSWORD_DEFAULT);
        $query = "INSERT INTO `users`  ( `firstname`, `lastname`, `email`, `password`, `disabled`) VALUES ( 'john', 'doe', 'john@doe.com', '$password', '0');";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $query = "INSERT INTO `users`  ( `firstname`, `lastname`, `email`, `password`, `disabled`) VALUES ( 'john', 'doe', 'john@doe.com', '$password', '0');";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $result = $this->userModel->countUsers();
        $this->assertIsObject($result);
        $this->assertEquals(2, $result->count);
    }
    public function testCountUsersEmpty()
    {
        $result = $this->userModel->countUsers();
        $this->assertIsObject($result);
        $this->assertEquals(0, $result->count);
    }

    protected function tearDown(): void
    {
        $query = "TRUNCATE TABLE users;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }
}
