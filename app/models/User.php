<?php

namespace VanillaAuth\Models;

use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\MysqlModel;

class User extends MysqlModel
{
    public function __construct()
    {
        parent::__construct(MysqlConnection::getConnection(), "users");
    }

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email='" . $email . "' LIMIT 1;";
        $result = $this->fetchOne($query);
        return $result;
    }
}
