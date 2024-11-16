<!DOCTYPE html>
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
<<<<<<< HEAD

if ($_SERVER["REQUEST_METHOD"] == "POST") {


  $servername = "localhost";
  $username = "root"; 
  $db_password = ""; 
  $dbname = "my_database"; 


  $conn = new mysqli($servername, $username, $db_password, $dbname);


  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }


=======
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

>>>>>>> d728c8dca4e7b76c666ff47001ee24e1be6a226f
  $user_username = $_POST['username'];
  $user_email = $_POST['email'];
  $user_password = $_POST['password']; 
  $user_phone = $_POST['phonenumber']; 

<<<<<<< HEAD

  $sql = "INSERT INTO users (username, email, password, phonenumber) VALUES (?, ?, ?, ?)";


  $stmt = $conn->prepare($sql);


=======
  if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
      echo "<div class='text-danger text-center mt-3'>Invalid email format.</div>";
      exit();
  }

  $sql = "INSERT INTO users (username, email, password, phonenumber) VALUES (?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);

>>>>>>> d728c8dca4e7b76c666ff47001ee24e1be6a226f
  if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
  }

<<<<<<< HEAD

  $stmt->bind_param("ssss", $user_username, $user_email, $user_password, $user_phone);


  if ($stmt->execute()) {

    header("Location: logIn.php");
    exit();
  } else {

    echo "<div class='text-danger text-center mt-3'>Error: " . $stmt->error . "</div>";
  }


=======
  $stmt->bind_param("ssss", $user_username, $user_email, $user_password, $user_phone);

  if ($stmt->execute()) {
    header("Location: logIn.php");
    exit();
  } else {
    error_log("Error inserting user: " . $stmt->error);
    echo "<div class='text-danger text-center mt-3'>An error occurred, please try again.</div>";
  }

>>>>>>> d728c8dca4e7b76c666ff47001ee24e1be6a226f
  $stmt->close();
  $conn->close();
}
?>

</body>
</html>
