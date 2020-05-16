<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="container">

    <div class="card bg-light mb-3">
        <h5 class="card-header text-uppercase text-secondary">Profile</h5>
        <div class="card-body">
            <p class="card-text">
                <span class="font-weight-bold mr-4">First Name</span><?= $user->firstname ?>
            </p>
            <p class="card-text">
                <span class="font-weight-bold mr-4">Last Name</span><?= $user->lastname ?>
            </p>
            <p class="card-text">
                <span class="font-weight-bold mr-4">Email Address</span><?= $user->email ?>
            </p>
        </div>
        <div class="card-footer">
            <a class="btn btn-outline-info" href="<?= baseUrl("users/$user->id/edit") ?>">Edit Profile</a>
            <a class="btn btn-outline-warning" href="<?= baseUrl("users/auth/resetPassword/$user->id") ?>">Reset Password</a>
        </div>

    </div>

</div>

<?php
Loader::view("layout/footer");
?>