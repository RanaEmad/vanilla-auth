<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">


    <form action="<?= baseUrl("users/toggleAccount/$user->id") ?>" method="POST">
    <input type="hidden" name="_method" value="PUT"/>
        <input type="hidden" name="disabled" value="<?=$disabled?>"/>
        Are you sure you want to <?=$state." ".$user->firstname." ".$user->lastname."'s account?"?> 
        <div class="form-group">
            <input name="submit" type="submit" value="<?=$state?>" />
        </div>

    </form>
</div>

<?php
Loader::view("layout/footer");
?>