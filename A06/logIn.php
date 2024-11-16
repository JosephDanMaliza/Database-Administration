<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="text-center mb-4">Login</h2>
      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
      </form>
    </div>
  </div>
</div>

<?php
include('connect.php');  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
      die('MySQL prepare error: ' . $conn->error);
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();

      
      if ($password === $user['password']) {
          session_start();
          $_SESSION['user_id'] = $user['id'];  
          header("Location: welcome.php"); 
          exit();
      } else {
          echo "<div class='text-danger text-center mt-3'>Incorrect password.</div>";
      }
  } else {
      echo "<div class='text-danger text-center mt-3'>No account found with that email.</div>";
  }

  $stmt->close();
}

$conn->close();
?>

</body>
</html>
