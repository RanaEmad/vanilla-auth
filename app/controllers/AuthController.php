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
        if (Session::loggedIn()) {
            return redirect("users/" . Session::loggedIn());
        }
        Loader::view("users/login");
    }
    public function authenticate()
    {
        if (Session::loggedIn()) {
            return redirect("users/" . Session::loggedIn());
        }
        $validator = new Validator();
        $validation = $validator->make(Request::post(), [
            "email" => "required|email",
            "password" => "required"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            return redirect("users/auth/login");
        } else {
            $user = $this->userModel->getUserByEmail(Request::post("email"));
            if (!$user) {
                //set error
                Session::setKey("error", "Invalid Credentials");
                return redirect("users/auth/login");
            } elseif ($user->disabled === 1) {
                Session::setKey("error", "Your account is disabled");
                return redirect("users/auth/login");
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
                return redirect("users/$user->id");
            }
            //set error invalid credentials
            Session::setKey("error", "Invalid Credentials");
            return redirect("users/auth/login");
        }
    }

    public function logout()
    {
        Session::destroy();
        return redirect("users/auth/login");
    }
}
