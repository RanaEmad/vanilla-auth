<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="container d-flex justify-content-center align-items-center">
    <h1 class="text-danger"><?= $msg ?? "Undefined Error" ?></h1>
</div>

<?php
Loader::view("layout/footer");
?>