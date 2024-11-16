<?php
session_start();
include('connect.php');


ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);


    echo "Entered Email: " . htmlspecialchars($email) . "<br>";
    echo "Entered Password (Trimmed): " . htmlspecialchars($password) . "<br>";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log('MySQL prepare error: ' . $conn->error);
            $error_message = "An unexpected error occurred. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();


                echo "Stored Password (from DB): " . htmlspecialchars($user['password']) . "<br>";
                echo "Stored Password Length: " . strlen($user['password']) . "<br>";


                if ($password === $user['password']) { 
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: welcome.php");
                    exit();
                } else {
                    $error_message = "Invalid email or password.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }

            $stmt->close();
        }
    }
}

$conn->close();
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
      <?php if (!empty($error_message)): ?>
        <div class="text-danger text-center mt-3">
          <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
