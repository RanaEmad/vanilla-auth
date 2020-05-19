<?php

use VanillaAuth\Core\Router;

//To match dynamic variables in the uri they have to be inclosed between 2 colons ex: "user/:id:"
//The controller preceds the method and they are both separated by @

Router::get("/", "AuthController@login");

Router::get("users/auth/register", "UserController@create");
Router::post("users/auth/register", "UserController@store");
Router::get("users/auth/login", "AuthController@login");
Router::get("users/auth/logout", "AuthController@logout");
Router::post("users/auth/login", "AuthController@authenticate");
Router::get("users/auth/resetPassword/:id:", "UserController@resetPassword");
Router::put("users/auth/resetPassword/:id:", "UserController@updateResetPassword");

Router::get("users", "UserController@index");
Router::get("users/:id:", "UserController@show");
Router::get("users/:id:/edit", "UserController@edit");
Router::put("users/:id:", "UserController@update");
Router::get("users/toggleAccount/:id:/:state:", "UserController@toggleAccount");
Router::put("users/toggleAccount/:id:", "UserController@updateToggleAccount");

Router::get("countries", "CountryController@index");
