<?php
session_start();  

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $servername = "localhost";
  $username = "root";
  $db_password = "";
  $dbname = "my_database";

  // Establish the database connection
  $conn = new mysqli($servername, $username, $db_password, $dbname);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  // SQL query to fetch the user by email
  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
      die('MySQL prepare error: ' . $conn->error);
  }

  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  $login_error = '';

  // Check if a user was found
  if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();

      // Compare the entered password with the stored password
      if ($password === $user['password']) {
          $_SESSION['user_id'] = $user['id'];  // Store user ID in session
          header("Location: welcome.php");  // Redirect to welcome page
          exit();  // Ensure no further code is executed after redirect
      } else {
          $login_error = "Incorrect password.";
      }
  } else {
      $login_error = "No account found with that email.";
  }

  $stmt->close();
  $conn->close();
}

?>

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

      <?php
     
      if (!empty($login_error)) {
          echo "<div class='text-danger text-center mt-3'>{$login_error}</div>";
      }
      ?>
    </div>
  </div>
</div>

</body>
</html>
