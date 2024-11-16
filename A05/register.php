<?php
include('connect.php');

$register_error = "";
$register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation for empty fields or invalid email format
    if (empty($username) || empty($email) || empty($password)) {
        $register_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $register_error = "Invalid email format.";
    } else {
        // SQL query to insert user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $register_success = "Registration successful! Redirecting to login page...";
                header("refresh:3;url=login.php"); // Redirect after 3 seconds
                exit();
            } else {
                $register_error = "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $register_error = "Error: " . $conn->error;
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
    <title>Register</title>
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
        .register-container {
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
        input[type="text"], input[type="email"], input[type="password"] {
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
    <div class="register-container">
        <h2>Register</h2>

        <!-- Display errors or success messages -->
        <?php if ($register_error): ?>
            <p style="color: red;"><?php echo $register_error; ?></p>
        <?php endif; ?>
        <?php if ($register_success): ?>
            <p style="color: green;"><?php echo $register_success; ?></p>
        <?php endif; ?>

        <form action="register.php" method="post">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
