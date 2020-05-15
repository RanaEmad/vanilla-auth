<?php

use VanillaAuth\Core\Router;

Router::get("users/register", "UserController@create");
Router::post("users/register", "UserController@store");
Router::get("users/login", "AuthController@login");
Router::post("users/login", "AuthController@authenticate");

Router::get("users", "UserController@index");
