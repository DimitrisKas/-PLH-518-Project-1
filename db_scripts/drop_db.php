<?php 
include_once 'db_connection.php';

function dropDB()
{
    $conn = OpenCon(false);

    $sql_str = "DROP DATABASE IF EXISTS Project1";
    if ($conn->query($sql_str) === TRUE) {
        logger("Database dropped successfully (or was already deleted)!");
    } else {
        logger("Error dropping database: " . $conn->error);
    }

    CloseCon($conn);
}
