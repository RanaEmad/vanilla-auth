<?php

use VanillaAuth\Core\Router;

Router::get("users/register", "UsersController@create");
Router::post("users/register", "UsersController@store");
Router::get("users/login", "AuthController@login");
Router::post("users/login", "AuthController@authenticate");
