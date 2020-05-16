<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="container">
<h1 class="mb-4 text-secondary">Login</h1>
    <form action="<?= baseUrl("users/auth/login") ?>" method="POST">
        <div class="form-group">
            <label for="email">Email address</label>
            <input name="email" type="email" class="form-control" id="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input name="password" type="password" class="form-control" id="password" >
        </div>

        <div class="form-group">
            <input class="btn btn-info" name="submit" type="submit" value="Submit" />
        </div>


    </form>
</div>

<?php
Loader::view("layout/footer");
?>