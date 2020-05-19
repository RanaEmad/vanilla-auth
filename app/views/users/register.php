<?php

use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Request;
use VanillaAuth\Services\Csrf;

Loader::view("layout/header");
?>
<div class="container">
    <?= flashValidationErrors() ?>
    <h1 class="mb-4 text-secondary">Register</h1>
    <form action="<?= baseUrl("users/auth/register") ?>" method="POST">
        <?= Csrf::getCsrfField(); ?>
        <div class="form-group">
            <label for="firstname">First Name</label>
            <input name="firstname" type="firstname" class="form-control" id="firstname" value="<?= Request::post("firstname") ?>">
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input name="lastname" type="lastname" class="form-control" id="lastname" value="<?= Request::post("lastname") ?>">
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input name="email" type="email" class="form-control" id="email" value="<?= Request::post("email") ?>">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" type="password" class="form-control" id="password">
        </div>
        <div class="form-group">
            <label for="password">Match Password</label>
            <input name="matchPassword" type="password" class="form-control" id="matchPassword">
        </div>

        <div class="form-group">
            <input class="btn btn-info" name="submit" type="submit" value="Submit" />
        </div>

    </form>
</div>

<?php
Loader::view("layout/footer");
?>