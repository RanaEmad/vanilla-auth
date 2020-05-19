<?php

use VanillaAuth\Core\Session;

function baseUrl($uri = NULL)
{
    if ($uri) {
        return BASE_URL . "/" . trim($uri, "/");
    }
    return BASE_URL;
}

function redirect($uri = NULL)
{
    header('Location: ' . baseUrl($uri));
    exit();
}

function flashMessage($type = "error")
{
    $alert = "success";
    if ($type == "error") {
        $alert = "danger";
    }
    $msg = Session::getKey($type);
    Session::unsetKey($type);
    if ($msg) {
        return
            "<div class='alert alert-$alert'>
        $msg
        </div>";
    }
    return "";
}

function flashValidationErrors()
{
    $validationErrors = Session::getKey("validationErrors");
    Session::unsetKey("validationErrors");

    $data = Session::getKey("postData");
    if ($data) {
        $_POST = $data;
        Session::unsetKey("postData");
    }

    $errors = "<div class='alert alert-danger'> ";
    if ($validationErrors) {
        foreach ($validationErrors as $key => $error) {
            $errors .= "<p>$error</p>";
        }
        $errors .= "</div>";
        return $errors;
    }
    return "";
}

function pd($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    die;
}
