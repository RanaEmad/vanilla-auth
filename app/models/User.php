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
}
