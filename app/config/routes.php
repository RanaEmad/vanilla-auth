<?php

use VanillaAuth\Core\Router;

Router::get("users/auth/register", "UserController@create");
Router::post("users/auth/register", "UserController@store");
Router::get("users/auth/login", "AuthController@login");
Router::post("users/authlogin", "AuthController@authenticate");
Router::get("users/auth/resetPassword/:id:", "UserController@resetPassword");
Router::put("users/auth/resetPassword/:id:", "UserController@updateResetPassword");

Router::get("users", "UserController@index");
Router::get("users/:id:", "UserController@edit");
Router::put("users/:id:", "UserController@update");
Router::get("users/toggleAccount/:id:/:state:", "UserController@toggleAccount");
Router::put("users/toggleAccount/:id:", "UserController@updateToggleAccount");

Router::get("countries", "CountryController@index");
