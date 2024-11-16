updated register.php<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css"> 
</head>
<body>
<div class="container mt-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="text-center mb-4">Register</h2>
      <form action="register.php" method="POST">
        <div class="form-group">
          <label for="username">Username:</label>
          <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password:</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
          <label for="phonenumber">Phone Number:</label>
          <input type="text" class="form-control" id="phonenumber" name="phonenumber" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Register</button>
      </form>
    </div>
  </div>
</div>

<?php
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {


  $user_username = $_POST['username'];
  $user_email = $_POST['email'];
  $user_password = $_POST['password']; 
  $user_phone = $_POST['phonenumber']; 


  if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
      echo "<div class='text-danger text-center mt-3'>Invalid email format.</div>";
      exit();
  }

  if (!preg_match("/^[0-9]{10}$/", $user_phone)) {
      echo "<div class='text-danger text-center mt-3'>Invalid phone number. Please enter a 10-digit number.</div>";
      exit();
  }

  $check_email_sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($check_email_sql);
  $stmt->bind_param("s", $user_email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    echo "<div class='text-danger text-center mt-3'>Email already registered. Please use a different email.</div>";
    $stmt->close();
    exit();
  }


  $sql = "INSERT INTO users (username, email, password, phonenumber) VALUES (?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);

  if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
  }


  $stmt->bind_param("ssss", $user_username, $user_email, $user_password, $user_phone);

  if ($stmt->execute()) {
    header("Location: logIn.php"); // Redirect to login page after successful registration
    exit();
  } else {
    error_log("Error inserting user: " . $stmt->error); // Log any errors for debugging purposes
    echo "<div class='text-danger text-center mt-3'>An error occurred, please try again.</div>";
  }


  $stmt->close();
  $conn->close();
}
?>

</body>
</html>
