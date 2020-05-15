<?php

namespace VanillaAuth\Controllers;

use Rakit\Validation\Validator;
use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Pagination;
use VanillaAuth\Core\Request;
use VanillaAuth\Models\User;

class UserController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function index()
    {
        $count = $this->userModel->countUsers()->count;
        $pagination = new Pagination("users", $count, 3);
        $users = $this->userModel->getAll($pagination->getOffset(), $pagination->getRows());
        $data = [
            "users" => $users,
            "links" => $pagination->getPaginationLinks()
        ];
        Loader::view("users/index", $data);
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
