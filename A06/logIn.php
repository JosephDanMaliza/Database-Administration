<?php
session_start();  // Start the session to track the user

include('connect.php');

$login_error = "";
$login_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST['usernameOrEmail'];
    $password = $_POST['password'];

    // Prepare SQL query to fetch user by username or email
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Check if the password matches
        if ($password === $row['password']) {
            // Store user data in session
            $_SESSION['userID'] = $row['userID'];
            $_SESSION['username'] = $row['username']; // You can store other details like username

            // Set a success message
            $login_success = "Login successful!";
            // Redirect to welcome page
            header("Location: welcome.php");
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "No account found with that username or email.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        body {
            background-color: #000;
            color: #FFD700;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            background-color: #222;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            width: 300px;
            text-align: center;
        }
        h2 {
            color: #FFD700;
        }
        label, input[type="submit"] {
            color: #FFD700;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #FFD700;
            border-radius: 5px;
            background-color: #333;
            color: #FFD700;
        }
        input[type="submit"] {
            background-color: #FFD700;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            color: #000;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #FFC700;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Log In</h2>

        <?php if ($login_error): ?>
            <p style="color: red;"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <?php if ($login_success): ?>
            <p style="color: green;"><?php echo $login_success; ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <label for="usernameOrEmail">Username or Email:</label>
            <input type="text" name="usernameOrEmail" id="usernameOrEmail" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Log In">
        </form>
    </div>
</body>
</html>
