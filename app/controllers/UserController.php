<?php

namespace VanillaAuth\Controllers;

use Rakit\Validation\Validator;
use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Pagination;
use VanillaAuth\Core\Request;
use VanillaAuth\Models\User;
use GuzzleHttp\Client;
use VanillaAuth\Core\Session;

class UserController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new User();
    }
    public function index()
    {
        Session::checkLogin();
        $count = $this->userModel->countUsers()->count;
        $pagination = new Pagination("users", $count, 3);
        $users = $this->userModel->getAll($pagination->getOffset(), $pagination->getRows());
        $data = [
            "users" => $users,
            "links" => $pagination->getPaginationLinks()
        ];
        Loader::view("users/index", $data);
    }

    public function show($id)
    {
        Session::checkLogin();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        Loader::view("users/profile", compact("user"));
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
            Session::setKey("validationErrors", $errors->firstOfAll());
            redirect("users/auth/register");
        } else {
            $data = [
                "firstname" => Request::post("firstname"),
                "lastname" => Request::post("lastname"),
                "email" => Request::post("email"),
                "password" => password_hash(Request::post("password"), PASSWORD_DEFAULT)
            ];
            $id = $this->userModel->insert($data);
            //set session
            $userData = [
                "id" => $id,
                "firstname" => $data["firstname"],
                "lastname" => $data["lastname"],
                "email" => $data["email"],
                "logged" => true
            ];
            Session::set($userData);

            Session::setKey("success", "Account created successfully!");
            redirect("users/$id");
        }
    }

    public function edit($id)
    {
        Session::checkLogin();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        Loader::view("users/edit", compact("user"));
    }

    public function update($id)
    {
        $validator = new Validator();
        $validation = $validator->make(Request::put(), [
            "firstname" => "required|max:20",
            "lastname" => "required|max:20",
            "email" => "required|email|max:50"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            redirect("users/$id/edit");
        } else {
            $data = [
                "firstname" => Request::put("firstname"),
                "lastname" => Request::put("lastname"),
                "email" => Request::put("email")
            ];
            $this->userModel->update($id, $data);
            Session::setKey("success", "Data updated successfully!");
            redirect("users/$id");
        }
    }

    public function toggleAccount($id, $state)
    {
        $disabled = 0;
        if ($state == "disable") {
            $disabled = 1;
        }
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        if ($user->disabled === $disabled) {
            echo "account already {$state}d";
            die;
        } else {
            $data = [
                "user" => $user,
                "state" => $state,
                "disabled" => $disabled
            ];
            Loader::view("users/toggleAccount", $data);
        }
    }

    public function updateToggleAccount($id)
    {
        $data = [
            "disabled" => Request::put("disabled")
        ];
        $this->userModel->update($id, $data);
        Session::setKey("success", "Account updated successfully!");
        redirect("users");
    }

    public function resetPassword($id)
    {
        Loader::view("users/resetPassword", compact("id"));
    }

    public function updateResetPassword($id)
    {
        $validator = new Validator();
        $validation = $validator->make(Request::put(), [
            "oldPassword" => "required",
            "newPassword" => "required",
            "matchPassword" => "required|same:newPassword"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            redirect("users/$id");
        } else {
            $user = $this->userModel->getOne($id);
            if (password_verify(Request::put("oldPassword"), $user->password)) {

                $data = [
                    "password" => password_hash(Request::put("newPassword"), PASSWORD_DEFAULT)
                ];
                $this->userModel->update($user->id, $data);
                Session::setKey("success", "Password updated successfully!");
                redirect("users/profile");
            } else {
                Session::setKey("error", "Your old password didn't match");
                redirect("users/auth/resetPassword/$id");
            }
        }
    }
}
