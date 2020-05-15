<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <form action="<?= baseUrl("users/login") ?>" method="POST">
        <div class="form-group">
            <label>email</label>
            <input name="email" />
        </div>
        <div class="form-group">
            <label>password</label>
            <input name="password" type="password" />
        </div>
        <div class="form-group">
            <input name="submit" type="submit" value="Submit" />
        </div>

    </form>
</div>

<?php
Loader::view("layout/footer");
?>