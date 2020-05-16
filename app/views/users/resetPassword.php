<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <form action="<?= baseUrl("users/resetPassword/$id") ?>" method="POST">
        <input type="hidden" name="_method" value="PUT" />
        <div class="form-group">
            <label>Old Password</label>
            <input name="oldPassword" type="password" />
        </div>
        <div class="form-group">
            <label>New Password</label>
            <input name="newPassword" type="password" />
        </div>
        <div class="form-group">
            <label>Match Password</label>
            <input name="matchPassword" type="password" />
        </div>
        <div class="form-group">
            <input name="submit" type="submit" value="Submit" />
        </div>
    </form>
</div>

<?php
Loader::view("layout/footer");
?>