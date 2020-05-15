<?php
require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

use VanillaAuth\Core\Session;

//loading the .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

Session::start();

require "app/config/constants.php";
require "app/helpers/generalHelper.php";
require "app/config/database.php";
require "app/config/routes.php";
