<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vanilla Auth</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
  <div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="


<?= baseUrl("users") ?>">Users</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl("countries") ?>">Countries</a>
          </li>
        </ul>

        <ul class="navbar-nav">
          <?php

          use VanillaAuth\Core\Session;

          if (Session::loggedIn()) {
          ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl("users/" . Session::loggedIn()) ?>">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">/</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl("users/auth/logout") ?>">Log Out</a>
            </li>
          <?php
          } else {
          ?>
            <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl("users/auth/login") ?>">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">/</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= baseUrl("users/auth/register") ?>">Register</a>
            </li>
          <?php } ?>
        </ul>

      </div>
    </nav>
  </div>

  <div class="container">
    <?= flashMessage("error") ?>
    <?= flashMessage("success") ?>
  </div>