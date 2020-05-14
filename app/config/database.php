<?php

use VanillaAuth\Core\Config;

Config::set("db_host", getenv("DB_HOST"));
Config::set("db_name", getenv("DB_NAME"));
Config::set("db_user", getenv("DB_USER"));
Config::set("db_password", getenv("DB_PASSWORD"));
Config::set("db_port", getenv("DB_PORT"));
