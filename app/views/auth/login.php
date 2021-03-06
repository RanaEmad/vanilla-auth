<?php

use VanillaAuth\Core\Loader;
use VanillaAuth\Services\Csrf;

Loader::view("layout/header");
?>
<div class="container">
    <?= flashValidationErrors() ?>
    <h1 class="mb-4 text-secondary">Login</h1>
    <form action="<?= baseUrl("users/auth/login") ?>" method="POST">
        <?= Csrf::getCsrfField() ?>
        <div class="form-group">
            <label for="email">Email address</label>
            <input name="email" type="email" class="form-control" id="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" type="password" class="form-control" id="password">
        </div>
        <p class="text-secondary">Don't have an account? <a class="text-warning" href="<?= baseUrl("users/auth/register") ?>">Register</a></p>

        <div class="form-group">
            <input class="btn btn-info" name="submit" type="submit" value="Submit" />
        </div>


    </form>
</div>

<?php
Loader::view("layout/footer");
?>