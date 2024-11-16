<?php

$host = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "my_database"; 


$conn = new mysqli($host, $db_username, $db_password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$tables = [];
$result = $conn->query("SHOW TABLES");

if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}


foreach ($tables as $table) {
    echo "<h2>Data from table: $table</h2>";
    $sql = "SELECT * FROM `$table`";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            echo "<table border='1'><tr>";


            while ($field = $result->fetch_field()) {
                echo "<th>{$field->name}</th>";
            }
            echo "</tr>";


            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $column) {
                    echo "<td>" . htmlspecialchars($column) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No records found in $table.<br>";
        }
    } else {
        echo "Error fetching data from $table: " . $conn->error . "<br>";
    }
}


$conn->close();
?>
