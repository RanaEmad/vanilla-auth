<?php
require "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

//loading the .env variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
