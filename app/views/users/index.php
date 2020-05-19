<?php

use VanillaAuth\Core\Loader;
use VanillaAuth\Core\Session;

Loader::view("layout/header");
?>
<div class="container">
    <h1 class="mb-4 text-secondary">Users</h1>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($users as $user) {
                ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $user->firstname ?></td>
                        <td><?= $user->lastname ?></td>
                        <td><?= $user->email ?></td>
                        <td>
                            <a class="btn btn-outline-success" href="<?= baseUrl("users/$user->id/edit") ?>">Edit</a>
                            <?php
                            if ($user->id != Session::getKey("id")) {
                                if ($user->disabled) {
                            ?>
                                    <a class="btn btn-outline-primary" href="<?= baseUrl("users/toggleAccount/$user->id/enable") ?>">Enable</a>
                                <?php
                                } else {
                                ?>
                                    <a class="btn btn-outline-primary" href="<?= baseUrl("users/toggleAccount/$user->id/disable") ?>">Disable</a>
                            <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <nav aria-label="users pagination">
        <ul class="pagination">
            <?php if (!$links["previous"]) { ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
            <?php } else {
            ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $links["previous"] ?>">Previous</a>
                </li>
            <?php } ?>

            <?php
            foreach ($links as $page => $link) {
                if ($page == "previous" || $page == "next" || $page == "current") {
                    continue;
                }
                if ($links["current"] == $page) {
            ?>
                    <li class="page-item active" aria-current="page">
                        <a class="page-link" href="<?= $link ?>"><?= $page ?> <span class="sr-only">(current)</span></a>
                    </li>
                <?php

                } else {
                ?>
                    <li class="page-item"><a class="page-link" href="<?= $link ?>"><?= $page ?></a></li>
            <?php
                }
            }
            ?>
            <?php if (!$links["next"]) { ?>
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                </li>
            <?php } else {
            ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $links["next"] ?>">Next</a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>

<?php
Loader::view("layout/footer");
?>