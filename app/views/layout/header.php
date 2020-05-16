<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Title</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
  <div class="container-fluid p-0">
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl("users") ?>">Users</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl("countries") ?>">Countries</a>
          </li>
        </ul>

        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl("users/auth/login") ?>">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">/</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= baseUrl("users/auth/register") ?>">Register</a>
          </li>
        </ul>

      </div>
    </nav>
  </div>

  <div class="container">
    <?= flashMessage("error") ?>
    <?= flashMessage("success") ?>
  </div>