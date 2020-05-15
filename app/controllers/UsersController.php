<?php

namespace VanillaAuth\Controllers;

use Rakit\Validation\Validator;
use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Models\User;

class UsersController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function create()
    {
        Loader::view("users/register");
    }

    public function store()
    {
        $validator = new Validator();
        $validation = $validator->make(Request::post(), [
            "firstname" => "required|max:20",
            "lastname" => "required|max:20",
            "email" => "required|email|max:50",
            "password" => "required|max:50",
            "matchPassword" => "required|same:password"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            print_r($errors->firstOfAll());
        } else {
            $data = [
                "firstname" => Request::post("firstname"),
                "lastname" => Request::post("lastname"),
                "email" => Request::post("email"),
                "password" => password_hash(Request::post("password"), PASSWORD_DEFAULT)
            ];
            $this->userModel->insert($data);
            redirect("users/register");
        }
    }
}
