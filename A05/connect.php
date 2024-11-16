<?php
$host = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "my_database"; 

$conn = new mysqli($host, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to the database successfully.";

$conn->close();
?>
