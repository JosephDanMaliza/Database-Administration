<?php
include('connect.php');  

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  echo "<p>Email: $email</p>";
  echo "<p>Password: $password</p>";  

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

      echo "<p>Stored password (from DB): " . $user['password'] . "</p>"; 

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
