<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <form action="<?=baseUrl("users/$user->id")?>" method="POST">
    <input type="hidden" name="_method" value="PUT" />
        <div class="form-group">
            <label>fristname</label>
            <input name="firstname" value="<?=$user->firstname?>" />
        </div>
        <div class="form-group">
            <label>lastname</label>
            <input name="lastname" value="<?=$user->lastname?>" />
        </div>
        <div class="form-group">
            <label>email</label>
            <input name="email" value="<?=$user->email?>" />
        </div>
        <div class="form-group">
            <input name="submit" type="submit" value="Submit" />
        </div>

    </form>
</div>

<?php
Loader::view("layout/footer");
?>