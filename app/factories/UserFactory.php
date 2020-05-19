<?php

namespace VanillaAuth\Factories;

use Faker\Factory;
use VanillaAuth\Models\User;

class UserFactory
{
    public static $id = 1;
    public static function create($override = null)
    {
        $faker =  Factory::create();
        $userModel = new User();
        $password = $faker->lexify(str_repeat("?", 8));
        $user = [
            "id" => self::$id,
            "firstname" => $faker->lexify(str_repeat("?", 10)),
            "lastname" => $faker->lexify(str_repeat("?", 10)),
            "email" => $faker->email(20),
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        if ($override) {
            foreach ($override as $key => $value) {
                $user[$key] = $value;
            }
        }
        $userModel->insert($user);
        self::$id++;
        $user["plainPassword"] = $password;
        return $user;
    }

    public static function createBulk($n = 1)
    {
        $users = [];
        for ($i = 1; $i <= $n; $i++) {
            $users[] = self::create();
        }
        return $users;
    }

    public static function make($override = null)
    {
        $faker =  Factory::create();
        $password = "randompassword";
        $user = [
            "firstname" => $faker->name,
            "lastname" => $faker->name,
            "email" => $faker->email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "disabled" => 0
        ];
        $user["plainPassword"] = $password;
        if ($override) {
            foreach ($override as $key => $value) {
                $user[$key] = $value;
            }
        }
        return $user;
    }
}
