<?php

use VanillaAuth\Core\Loader;
use VanillaAuth\Services\Csrf;

Loader::view("layout/header");
?>
<div class="container">

    <div class="card bg-light mb-3">
        <h5 class="card-header text-uppercase text-secondary"><?= $state ?> Account</h5>
        <div class="card-body">
            <form action="<?= baseUrl("users/toggleAccount/$user->id") ?>" method="POST">
                <input type="hidden" name="_method" value="PUT" />
                <?= Csrf::getCsrfField(); ?>
                <input type="hidden" name="disabled" value="<?= $disabled ?>" />
                <p class="card-title"> Are you sure you want to <?= $state . " " . $user->firstname . " " . $user->lastname . "'s account?" ?> </p>
                <div class="form-group">
                    <input class="btn btn-danger" name="submit" type="submit" value="<?= strtoupper($state) ?>" />
                </div>

            </form>
        </div>
    </div>

</div>

<?php
Loader::view("layout/footer");
?>