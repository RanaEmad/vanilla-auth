<?php

namespace VanillaAuth\Controllers;

use Rakit\Validation\Validator;
use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Core\Session;
use VanillaAuth\Models\User;

class AuthController
{

    protected $authModel;
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function login()
    {
        Loader::view("users/login");
    }
    public function authenticate()
    {
        $validator = new Validator();
        $validation = $validator->make(Request::post(), [
            "email" => "required|email",
            "password" => "required"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            redirect("users/auth/login");
        } else {
            $user = $this->userModel->getUserByEmail(Request::post("email"));
            if (!$user) {
                //set error
                Session::setKey("error", "Invalid Credentials");
                redirect("users/auth/login");
            } elseif ($user->disabled === 1) {
                Session::setKey("error", "Your account is disabled");
                redirect("users/auth/login");
            }
            if (password_verify(Request::post("password"), $user->password)) {
                //set session
                $userData = [
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email,
                    "logged" => true
                ];
                Session::set($userData);
                redirect("users/$user->id");
            }
            pd($user);
            //set error invalid credentials
            Session::setKey("error", "Invalid Credentials");
            redirect("users/auth/login");
        }
    }
}
