<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_email = $_SESSION['email']; // Fetch email from session
echo "Welcome, " . $user_email;

$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "my_database";

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $user_password = $_POST['password'];

    // Check if the logged-in user's credentials are correct
    $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user_email, $user_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Delete the logged-in user's account
        $delete_sql = "DELETE FROM users WHERE email = ? AND password = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ss", $user_email, $user_password);

        if ($delete_stmt->execute()) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            echo "<div class='text-danger text-center mt-3'>Error deleting account. Please try again.</div>";
        }
    } else {
        echo "<div class='text-danger text-center mt-3'>Incorrect password. Account not deleted.</div>";
    }

    $stmt->close();
}
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
if (isset($_GET['section']) && $_GET['section'] == 'account') {
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
}

?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
