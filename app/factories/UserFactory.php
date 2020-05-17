<?php

namespace VanillaAuth\Factories;

use Faker\Factory;
use VanillaAuth\Models\User;

class UserFactory
{
    public static function create($override = null)
    {
        $faker =  Factory::create();
        $userModel = new User();
        $password = "randompassword";
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
        if ($override) {
            foreach ($override as $key => $value) {
                $user[$key] = $value;
            }
        }
        return $user;
    }

    public static function createBulk($n = 1)
    {
        for ($i = 1; $i <= $n; $i++) {
            self::create();
        }
    }
}
