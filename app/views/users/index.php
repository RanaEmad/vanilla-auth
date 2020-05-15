<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($users as $user) {
            ?>
                <tr>
                    <td><?= $user->firstname ?></td>
                    <td><?= $user->lastname ?></td>
                    <td><?= $user->email ?></td>
                    <td>

                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div class="pagination">
    <a href="<?=$links["previous"]?>">previous</a>
    <?php
    foreach($links as $page=>$link){
        if($page=="previous"){
            continue;
        }
?>
<a href="<?=$link?>"><?=$page?></a>
<?php
    }
    ?>
    </div>
</div>

<?php
Loader::view("layout/footer");
?>