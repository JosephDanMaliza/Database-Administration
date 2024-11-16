register.php

<?php
include('connect.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = $_POST['username'];
    $user_email = $_POST['email'];
    $user_password = $_POST['password']; 
    $user_phone = $_POST['phonenumber']; 

    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='text-danger text-center mt-3'>Invalid email format.</div>";
        exit();
    }

    if (!preg_match("/^[0-9]{10}$/", $user_phone)) {
        echo "<div class='text-danger text-center mt-3'>Invalid phone number. Please enter a 10-digit number.</div>";
        exit();
    }

    
    $check_email_sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_email_sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='text-danger text-center mt-3'>Email already registered. Please use a different email.</div>";
        $stmt->close();
        exit();
    }

    
    $sql = "INSERT INTO users (username, email, password, phonenumber) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("ssss", $user_username, $user_email, $user_password, $user_phone);

    if ($stmt->execute()) {
        header("Location: logIn.php"); 
        exit();
    } else {
        error_log("Error inserting user: " . $stmt->error); 
        echo "<div class='text-danger text-center mt-3'>An error occurred, please try again.</div>";
    }

    $stmt->close();
}


$conn->close();
?>
