<?php

use VanillaAuth\Core\Loader;
use VanillaAuth\Services\Csrf;

Loader::view("layout/header");
?>
<div class="container">
    <?= flashValidationErrors() ?>
    <h1 class="mb-4 text-secondary">Reset Password</h1>
    <form action="<?= baseUrl("users/auth/resetPassword/$id") ?>" method="POST">
        <input type="hidden" name="_method" value="PUT" />
        <?= Csrf::getCsrfField(); ?>
        <div class="form-group">
            <label for="oldPassword">Old Password</label>
            <input name="oldPassword" type="password" class="form-control" id="oldPassword">
        </div>
        <div class="form-group">
            <label for="newPassword">New Password</label>
            <input name="newPassword" type="password" class="form-control" id="newPassword">
        </div>
        <div class="form-group">
            <label for="matchPassword">Match Password</label>
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