<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">DanDaDan</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="#">Accounts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Posts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="deletionForm.php">Settings</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5 text-center">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1>Welcome!</h1>
      <p>You are logged in.</p>
    </div>
  </div>
</div>

</body>
</html>
