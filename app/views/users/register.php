<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <form action="<?=baseUrl("users/register")?>" method="POST">
        <div class="form-group">
            <label>fristname</label>
            <input name="firstname" />
        </div>
        <div class="form-group">
            <label>lastname</label>
            <input name="lastname" />
        </div>
        <div class="form-group">
            <label>email</label>
            <input name="email" />
        </div>
        <div class="form-group">
            <label>password</label>
            <input name="password" type="password" />
        </div>
        <div class="form-group">
            <label>match password</label>
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