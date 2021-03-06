<?php

namespace VanillaAuth\Core;

use PDO;
use PDOException;
use VanillaAuth\Core\Config;
use VanillaAuth\Interfaces\DatabaseConnection;

class MysqlConnection implements DatabaseConnection
{

    private static $conn;
    private function __construct()
    {
    }

    public static function getConnection()
    {
        if (!self::$conn) {
            try {
                $port = "";
                if (Config::get('db_port')) {
                    $port = ":" . Config::get("db_port");
                }
                self::$conn =  new PDO("mysql:host=" . Config::get("db_host") . $port . ";dbname=" . Config::get("db_name"), Config::get("db_user"), Config::get("db_password"));
            } catch (PDOException $exception) {
                echo "Database connection error: " . $exception->getMessage();
                die;
            }
        }
        return self::$conn;
    }
}
