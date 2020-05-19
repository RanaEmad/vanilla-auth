<?php

namespace VanillaAuth\Controllers;

use Rakit\Validation\Validator;
use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Pagination;
use VanillaAuth\Core\Request;
use VanillaAuth\Models\User;
use GuzzleHttp\Client;
use VanillaAuth\Services\UniqueRule;
use VanillaAuth\Core\MysqlConnection;
use VanillaAuth\Core\Session;
use VanillaAuth\Services\Csrf;

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
        $pagination = new Pagination("users", $count);
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
        if (Session::loggedIn()) {
            return redirect("users/" . Session::loggedIn());
        }
        Loader::view("users/register");
    }

    public function store()
    {
        if (Session::loggedIn()) {
            return redirect("users/" . Session::loggedIn());
        }
        Csrf::verifyCsrf();
        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule(MysqlConnection::getConnection()));
        $validation = $validator->make(Request::post(), [
            "firstname" => "required|max:20",
            "lastname" => "required|max:20",
            "email" => "required|email|max:50|unique:users,email",
            "password" => "required|max:50",
            "matchPassword" => "required|same:password"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            Session::setKey("postData", Request::post());
            return redirect("users/auth/register");
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
            return redirect("users/$id");
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
        Csrf::verifyCsrf();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);

        $validator = new Validator();
        $validator->addValidator('unique', new UniqueRule(MysqlConnection::getConnection()));
        $validation = $validator->make(Request::put(), [
            "firstname" => "required|max:20",
            "lastname" => "required|max:20",
            "email" => "required|email|max:50|unique:users,email,$user->email"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            $errors = $validation->errors();
            Session::setKey("validationErrors", $errors->firstOfAll());
            return redirect("users/$id/edit");
        } else {
            $data = [
                "firstname" => Request::put("firstname"),
                "lastname" => Request::put("lastname"),
                "email" => Request::put("email")
            ];
            $this->userModel->update($id, $data);
            Session::setKey("success", "Data updated successfully!");
            return redirect("users/$id");
        }
    }

    public function toggleAccount($id, $state)
    {
        Session::checkLogin();
        if ($state != "enable" && $state != "disable") {
            http_response_code(400);
            Loader::view("errors/msg", ["msg" => "Resource Not Found"]);
            exit();
        }
        $disabled = 0;
        if ($state == "disable") {
            $disabled = 1;
        }
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        if ($user->disabled === $disabled) {
            Session::setKey("error", "account already {$state}d");
            return redirect("users");
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
        Session::checkLogin();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        Csrf::verifyCsrf();
        $validator = new Validator();
        $validation = $validator->make(Request::put(), [
            "disabled" => "required"
        ]);
        $validation->validate();
        if ($validation->fails()) {
            Loader::view("errors/msg", ["msg" => "Access Denied"]);
            exit();
        } else {
            $data = [
                "disabled" => Request::put("disabled")
            ];
            $this->userModel->update($id, $data);
            Session::setKey("success", "Account updated successfully!");
            return redirect("users");
        }
    }

    public function resetPassword($id)
    {
        Session::checkLogin();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
        if ($user->id != Session::getKey("id")) {
            Loader::view("errors/msg", ["msg" => "Unauthorized access"]);
        } else {
            Loader::view("users/resetPassword", compact("id"));
        }
    }

    public function updateResetPassword($id)
    {
        Csrf::verifyCsrf();
        Session::checkLogin();
        $user = $this->userModel->getOne($id);
        Request::validateResource($user);
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
            return redirect("users/auth/resetPassword/$id");
        } else {
            if (password_verify(Request::put("oldPassword"), $user->password)) {

                $data = [
                    "password" => password_hash(Request::put("newPassword"), PASSWORD_DEFAULT)
                ];
                $this->userModel->update($user->id, $data);
                Session::setKey("success", "Password updated successfully!");
                return redirect("users/$user->id");
            } else {
                Session::setKey("error", "Your old password didn't match");
                return redirect("users/auth/resetPassword/$id");
            }
        }
    }
}
