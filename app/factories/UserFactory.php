<?php

namespace VanillaAuth\Factories;

use Faker\Factory;
use VanillaAuth\Models\User;

class UserFactory
{
    public static function create()
    {
        $faker =  Factory::create();
        $userModel = new User();
        $password = $faker->word;
        $user = [
            "firstname" => $faker->name,
            "lastname" => $faker->name,
            "email" => $faker->email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $id = $userModel->insert($user);
        $user["id"] = $id;
        $user["plainPassword"] = $password;
        return $user;
    }
}
