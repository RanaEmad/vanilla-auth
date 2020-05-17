<?php

use VanillaAuth\Core\Config;

if(getenv("APP_ENV")=="development" || getenv("APP_ENV")=="production"){
    Config::set("db_host", getenv("DB_HOST"));
    Config::set("db_name", getenv("DB_NAME"));
    Config::set("db_user", getenv("DB_USER"));
    Config::set("db_password", getenv("DB_PASSWORD"));
    Config::set("db_port", getenv("DB_PORT"));
}
else{
    Config::set("db_host", getenv("TEST_DB_HOST"));
    Config::set("db_name", getenv("TEST_DB_NAME"));
    Config::set("db_user", getenv("TEST_DB_USER"));
    Config::set("db_password", getenv("TEST_DB_PASSWORD"));
    Config::set("db_port", getenv("TEST_DB_PORT"));
}
