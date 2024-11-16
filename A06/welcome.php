<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_email = $_SESSION['email']; // Fetch email from session
echo "Welcome, " . htmlspecialchars($user_email);

$servername = "localhost";
$username = "root";
$db_password = "";
$dbname = "my_database";

$conn = new mysqli($servername, $username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$account_deleted = false; // Flag to track if the account was deleted

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $user_password = $_POST['password'];

    $sql = "SELECT password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($user_password, $stored_password)) {
            // Delete the logged-in user's account
            $delete_sql = "DELETE FROM users WHERE email = ?";
            $delete_stmt = $conn->prepare($delete_sql);

            if ($delete_stmt) {
                $delete_stmt->bind_param("s", $user_email);
                if ($delete_stmt->execute()) {
                    $account_deleted = true; // Set the flag
                    session_unset(); // Unset session variables
                    session_destroy(); // Destroy the session
                } else {
                    echo "<div class='text-danger text-center mt-3'>Error deleting account. Please try again.</div>";
                }
                $delete_stmt->close();
            }
        } else {
            echo "<div class='text-danger text-center mt-3'>Invalid credentials. Account not deleted.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='text-danger text-center mt-3'>An error occurred. Please try again later.</div>";
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

<?php if ($account_deleted): ?>
    <div class="container mt-5 text-center">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-success">Account Successfully Deleted</h2>
                <p><a href="register.php" class="btn btn-primary">Register Again</a></p>
            </div>
        </div>
    </div>
<?php elseif (isset($_GET['section']) && $_GET['section'] == 'account'): ?>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">Delete Your Account</h2>
                <form action="welcome.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user_email) ?>" readonly
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
    </div>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
