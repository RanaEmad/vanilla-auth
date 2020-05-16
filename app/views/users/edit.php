<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="container">
    <?= flashValidationErrors() ?>
    <h1 class="mb-4 text-secondary">Edit User</h1>
    <form action="<?= baseUrl("users/$user->id") ?>" method="POST">
        <input type="hidden" name="_method" value="PUT" />

        <div class="form-group">
            <label for="firstname">First Name</label>
            <input name="firstname" type="firstname" class="form-control" id="firstname" value="<?= $user->firstname ?>" />
        </div>
        <div class="form-group">
            <label for="lastname">Last Name</label>
            <input name="lastname" type="lastname" class="form-control" id="lastname" value="<?= $user->lastname ?>" />
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input name="email" type="email" class="form-control" id="email" value="<?= $user->email ?>" />
        </div>

        <div class="form-group">
            <input class="btn btn-info" name="submit" type="submit" value="Submit" />
        </div>

    </form>
</div>

<?php
Loader::view("layout/footer");
?>