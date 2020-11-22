<?php 
include 'db_connection.php';

$conn = OpenCon(false);

$sql_str = "CREATE DATABASE IF NOT EXISTS Project1";
if ($conn->query($sql_str) === TRUE) {
    echo "Database created successfully (or was already)!";
} else {
    echo "Error creating database: " . $conn->error;
}

CloseCon($conn);

?>