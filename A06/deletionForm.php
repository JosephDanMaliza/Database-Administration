<?php
session_start();
include('connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$delete_error = "";
$delete_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; 
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE id = ? AND username = ? AND email = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $username, $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);

        if ($delete_stmt->execute()) {
            session_unset();
            session_destroy();
            $delete_success = "Goodbye, Thank you for spending time with us.";
        } else {
            $delete_error = "An error occurred while trying to delete your account.";
        }

        $delete_stmt->close();
    } else {
        $delete_error = "Incorrect account details. Please try again.";
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
    <title>Delete Account</title>
    <style>
        body {
            background-color: #000;
            color: #FFD700;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .form-container {
            background-color: #222;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            text-align: center;
            width: 300px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #FFD700;
            border-radius: 5px;
            background-color: #333;
            color: #FFD700;
        }
        button {
            background-color: #FFD700;
            color: #000;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #FFC700;
        }
        .message {
            color: #FFD700;
            margin-top: 20px;
        }
        a {
            color: #FFD700;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php if ($delete_success): ?>
        <div class="message">
            <h2><?php echo $delete_success; ?></h2>
            <p><a href="register.php">Register again</a></p>
        </div>
    <?php else: ?>
        <div class="form-container">
            <h2>Delete Account</h2>
            <?php if ($delete_error): ?>
                <p style="color: red;"><?php echo $delete_error; ?></p>
            <?php endif; ?>
            <form method="POST" action="form.php">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Delete Account</button>
            </form>
        </div>
    <?php endif; ?>
</body>
</html>
