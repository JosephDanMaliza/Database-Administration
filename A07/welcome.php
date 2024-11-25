<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_email = $_SESSION['email']; 
echo "Welcome, " . htmlspecialchars($user_email);

include('connect.php');

$account_deleted = false;  
$account_updated = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle account deletion
    if (isset($_POST['delete_account'])) {
        $user_password = $_POST['password'];

        $sql = "SELECT password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $stmt->bind_result($stored_password);
            $stmt->fetch();

            // Plain text password comparison
            if ($user_password === $stored_password) {
                $stmt->close();

                $delete_sql = "DELETE FROM users WHERE email = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                if ($delete_stmt) {
                    $delete_stmt->bind_param("s", $user_email);
                    if ($delete_stmt->execute()) {
                        session_destroy();
                        header("Location: welcome.php?account_deleted=true");
                        exit();
                    } else {
                        echo "<div class='text-danger text-center mt-3'>Error deleting account. Please try again.</div>";
                    }
                    $delete_stmt->close();
                }
            } else {
                echo "<div class='text-danger text-center mt-3'>Invalid credentials. Account not deleted.</div>";
            }
        } else {
            echo "<div class='text-danger text-center mt-3'>An error occurred. Please try again later.</div>";
        }
    }

    // Handle account update
    if (isset($_POST['update_account'])) {
        $new_email = $_POST['new_email'];
        $new_name = $_POST['new_name'];
        $new_password = $_POST['new_password'];

        $update_sql = "UPDATE users SET email = ?, name = ?, password = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);

        if ($update_stmt) {
            $update_stmt->bind_param("ssss", $new_email, $new_name, $new_password, $user_email);
            if ($update_stmt->execute()) {
                $_SESSION['email'] = $new_email;
                echo "<div class='text-success text-center mt-3'>Account successfully updated.</div>";
            } else {
                echo "<div class='text-danger text-center mt-3'>Error updating account. Please try again.</div>";
            }
            $update_stmt->close();
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
  <title>Welcome</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <a class="navbar-brand" href="#">Welcome</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="posts.php">Posts</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="welcome.php?section=account">Delete Account</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5 text-center">
  <div class="card shadow-sm">
    <div class="card-body">
      <h1>You have logged in</h1>
    </div>
  </div>
</div>

<?php
// Handle different sections for account deletion or edit
if (isset($_GET['account_deleted']) && $_GET['account_deleted'] == 'true') {
  echo '
  <div class="container mt-5">
      <div class="card shadow-sm">
          <div class="card-body">
              <h2 class="text-center mb-4">Account Successfully Deleted</h2>
              <p class="text-center">Your account has been deleted. You can <a href="register.php">register again</a> if you wish.</p>
          </div>
      </div>
  </div>';
} else if (isset($_GET['section']) && $_GET['section'] == 'account') {
  echo '
  <div class="container mt-5">
      <div class="card shadow-sm">
          <div class="card-body">
              <h2 class="text-center mb-4">Delete Your Account</h2>
              <form action="welcome.php" method="POST">
                  <div class="form-group">
                      <label for="email">Email:</label>
                      <input type="email" class="form-control" id="email" name="email" value="' . htmlspecialchars($user_email) . '" readonly
                             style="background-color: #2c2c2c; color: white; border: 1px solid #555; padding: 10px; border-radius: 4px;">
                  </div>
                  <div class="form-group">
                      <label for="password">Password:</label>
                      <input type="password" class="form-control" id="password" name="password" required
                             style="background-color: #2c2c2c; color: white; border: 1px solid #555; padding: 10px; border-radius: 4px;">
                  </div>
                  <button type="submit" class="btn btn-danger btn-block" name="delete_account">Delete Account</button>
              </form>
          </div>
      </div>
  </div>';
} elseif (isset($_GET['section']) && $_GET['section'] == 'edit') {
  echo '
  <div class="container mt-5">
     <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center mb-4">Edit your Account</h2>
            <form action="welcome.php" method="POST">
                <div class="form-group">
                    <label for="new_name">Name:</label>
                    <input type="text" class="form-control" id="new_name" name="new_name" required>
                </div>
                <div class="form-group">
                    <label for="new_email">Email:</label>
                    <input type="email" class="form-control" id="new_email" name="new_email" value="' . htmlspecialchars($user_email) . '" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password:</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" name="update_account">Update Account</button>
            </form>
        </div>
    </div>
  </div>';
}
?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
