<?php

namespace VanillaAuth\Tests;

require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use Exception;
use PDO;
use PHPUnit\Framework\TestCase;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\MysqlModel;
use VanillaAuth\Core\RouteParser;
use VanillaAuth\Core\Router;
use VanillaAuth\Models\User;

class MysqlModelTest extends TestCase
{
    protected $db;
    protected $model;
    protected function setUp(): void
    {
        require "app/config/database.php";
        $this->db =  MysqlConnection::getConnection();
        $this->model = new MysqlModel($this->db, "users");
    }

    public function testInsert()
    {
        $data = [
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => "john@doe.com",
            "password" => password_hash("123456", PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $id = $this->model->insert($data);
        $data["id"] = $id;

        $query = "SELECT * FROM `users` WHERE id='$id' ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0], $data);
    }

    public function testUpdate()
    {
        $data = [
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => "john@doe.com",
            "password" => password_hash("123456", PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $id = $this->model->insert($data);

        $update = [
            "firstname" => "loremepsum1",
            "lastname" => "loremepsum2",
            "email" => "loremepsum3",
            "password" => password_hash("loremepsum4", PASSWORD_DEFAULT),
            "disabled" => 1
        ];

        $this->model->update($id, $update);


        $query = "SELECT * FROM `users` WHERE id='$id' ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]["firstname"], "loremepsum1");
        $this->assertEquals($result[0]["lastname"], "loremepsum2");
        $this->assertEquals($result[0]["email"], "loremepsum3");
        $this->assertEquals($result[0]["disabled"], 1);
    }

    public function testGetOne()
    {
        $data = [
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => "john@doe.com",
            "password" => password_hash("123456", PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $id = $this->model->insert($data);
        $data["id"] = $id;

        $user = $this->model->getOne($id);

        $query = "SELECT * FROM `users` WHERE id='$id' ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        $this->assertNotEmpty($user);
        $this->assertIsObject($user);
        $this->assertEquals($result[0], $user);
    }

    public function testGetAll()
    {
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = [
                "firstname" => "John",
                "lastname" => "Doe",
                "email" => "john@doe.com",
                "password" => password_hash("123456", PASSWORD_DEFAULT),
                "disabled" => 0
            ];
            $id = $this->model->insert($user);
            $user["id"] = $id;

            $users[] = (object) $user;
        }


        $all = $this->model->getAll();

        $this->assertNotEmpty($all);
        $this->assertIsArray($all);
        $this->assertEquals(count($users), count($all));
        $this->assertEquals($users, $all);
    }

    public function testGetAllOffset()
    {
        $users = [];
        for ($i = 0; $i < 5; $i++) {
            $user = [
                "firstname" => "John",
                "lastname" => "Doe",
                "email" => "john@doe.com",
                "password" => password_hash("123456", PASSWORD_DEFAULT),
                "disabled" => 0
            ];
            $id = $this->model->insert($user);
        }
        for ($i = 0; $i < 5; $i++) {
            $user = [
                "firstname" => "John",
                "lastname" => "Doe",
                "email" => "john@doe.com",
                "password" => password_hash("123456", PASSWORD_DEFAULT),
                "disabled" => 0
            ];
            $id = $this->model->insert($user);
            $user["id"] = $id;

            $users[] = (object) $user;
        }

        $all = $this->model->getAll(5, 5);

        $this->assertNotEmpty($all);
        $this->assertIsArray($all);
        $this->assertEquals(count($users), count($all));
        $this->assertEquals($users, $all);
    }

    public function testDelete()
    {
        $data = [
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => "john@doe.com",
            "password" => password_hash("123456", PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $id = $this->model->insert($data);
        $data["id"] = $id;

        $this->model->delete($id);

        $query = "SELECT * FROM `users` ;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals(count($result), 0);
    }


    protected function tearDown(): void
    {
        $query = "TRUNCATE TABLE users;";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
    }
}
