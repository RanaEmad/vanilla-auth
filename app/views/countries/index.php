<?php

use VanillaAuth\Core\Loader;

Loader::view("layout/header");
?>
<div class="content">
    <table>
        <thead>
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
                    <td><img style="width: 50px;" src="<?= $country->Flag ?>" alt="<?$country->Name?> Flag"></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
    <div class="pagination">
      <a href="<?=baseUrl("countries?page=$previous")?>">Previous</a>
      <a href="<?=baseUrl("countries?page=$next")?>">Next</a>
    </div>
</div>

<?php
Loader::view("layout/footer");
?>