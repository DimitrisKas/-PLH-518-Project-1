<?php 
include 'db_connection.php';

$conn = OpenCon(false);

$sql_str = "CREATE DATABASE IF NOT EXISTS Project1";
if ($conn->query($sql_str) === TRUE) {
    logger("Database created successfully (or was already)!");
} else {
    logger("Error creating database: " . $conn->error);
}

CloseCon($conn);

?>