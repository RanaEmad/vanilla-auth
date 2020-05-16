<?php

use VanillaAuth\Core\Router;

Router::get("users/register", "UserController@create");
Router::post("users/register", "UserController@store");
Router::get("users/login", "AuthController@login");
Router::post("users/login", "AuthController@authenticate");
Router::get("users/resetPassword/:id:", "UserController@resetPassword");
Router::put("users/resetPassword/:id:", "UserController@updateResetPassword");

Router::get("users", "UserController@index");
Router::get("users/:id:", "UserController@edit");
Router::put("users/:id:", "UserController@update");
Router::get("users/toggleAccount/:id:/:state:", "UserController@toggleAccount");
Router::put("users/toggleAccount/:id:", "UserController@updateToggleAccount");

Router::get("countries", "CountryController@index");
