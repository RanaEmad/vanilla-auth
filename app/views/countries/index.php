<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Country</th>
                    <th>Region</th>
                    <th>Currency</th>
                    <th>Currency Code</th>
                    <th>Flag</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($countries as $country) {
                ?>
                    <tr>
                        <td><?= $country->Name ?></td>
                        <td><?= $country->Region ?></td>
                        <td><?= $country->CurrencyName ?></td>
                        <td><?= $country->CurrencyCode ?></td>
                        <td><img class="img-thumbnail" style="width: 50px;" src="<?= $country->Flag ?>" alt="<? $country->Name ?> Flag"></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <nav aria-label="countries pagination">
        <ul class="pagination">
            <?php if ($previous) { ?>
                <li class="page-item">
                    <a class="page-link" href="<?= baseUrl("countries?page=$previous") ?>">Previous</a>
                </li>
            <?php } else {
            ?>
                <li class="page-item disabled">
                    <a class="page-link" href="<?= baseUrl("countries?page=$previous") ?>" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
            <?php } ?>
            <li class="page-item">
                <a class="page-link" href="<?= baseUrl("countries?page=$next") ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<?php
Loader::view("layout/footer");
?>