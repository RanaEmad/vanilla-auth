<?php

namespace VanillaAuth\Core;

class Request
{
    public static function post($attribute = NULL)
    {
        if ($attribute) {
            if (array_key_exists($attribute, $_POST)) {
                return htmlspecialchars(trim($_POST[$attribute]));
            }
            return false;
        }
        $post = [];
        foreach ($_POST as $key => $value) {
            $post[$key] = htmlspecialchars(trim($value));
        }
        return $post;
    }
    public static function get($attribute = NULL)
    {
        if ($attribute) {
            if (array_key_exists($attribute, $_GET)) {
                return htmlspecialchars(trim($_GET[$attribute]));
            }
            return false;
        }
        $get = [];
        foreach ($_GET as $key => $value) {
            $get[$key] = htmlspecialchars(trim($value));
        }
        return $get;
    }
}
