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
                $stored_password = $user['password'];

                if (password_verify($password, $stored_password)) { 
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
