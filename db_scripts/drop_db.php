<?php 
include 'db_connection.php';

$conn = OpenCon(false);

$sql_str = "DROP DATABASE IF EXISTS Project1";
if ($conn->query($sql_str) === TRUE) {
    echo "Database dropped successfully (or was already deleted)!";
} else {
    echo "Error dropping database: " . $conn->error;
}

CloseCon($conn);


?>