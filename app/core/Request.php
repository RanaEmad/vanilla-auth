<?php

namespace VanillaAuth\Core;

class Request
{
    public static function post($attribute = NULL)
    {
        if ($attribute) {
            return htmlspecialchars(trim($_POST[$attribute]));
        }
        $post=[];
        foreach($_POST as $key=>$value){
            $post[$key]=htmlspecialchars(trim($value));
        }
        return $post;
    }
}
