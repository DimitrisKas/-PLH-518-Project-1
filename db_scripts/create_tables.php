<?php 
include 'db_connection.php';
echo '';

$conn = OpenCon(true);

$sql_str = "CREATE TABLE Users(
    ID VARCHAR(10) NOT NULL PRIMARY KEY,
    NAME VARCHAR(20),
    SURNAME VARCHAR(20),
    USERNAME VARCHAR(20) NOT NULL,
    PASSWORD VARCHAR(20) NOT NULL,
    EMAIL VARCHAR(50) NOT NULL,
    ROLE ENUM('ADMIN', 'CINEMAOWNER', 'USER'),
    CONFIRMED BOOLEAN
)";

if ($conn->query($sql_str) === TRUE) {
    echo "Table \"Users\" created!\n";
} else {
    echo "Error creating \"Users\" Table: " . $conn->error ."\n";
}


$sql_str = "CREATE TABLE Movies(
    ID VARCHAR(10) NOT NULL PRIMARY KEY,
    TITLE VARCHAR(20),
    STARTDATE DATE,
    ENDDATE DATE, 
    CINEMANAME VARCHAR(20),
    CATEGORY VARCHAR(20)
)";
if ($conn->query($sql_str) === TRUE) {
    echo "Table \"Movies\" created!\n";
} else {
    echo "Error creating \"Movies\" Table: " . $conn->error ."\n";
}

$sql_str = "CREATE TABLE Favorites(
    ID VARCHAR(10) NOT NULL PRIMARY KEY,
    USERID  VARCHAR(10) NOT NULL,
    MOVIEID  VARCHAR(10) NOT NULL,
    FOREIGN KEY (USERID) REFERENCES Users(ID),
    FOREIGN KEY (MOVIEID) REFERENCES Movies(ID)
)";
if ($conn->query($sql_str) === TRUE) {
    echo "Table \"Favorites\" created!\n";
} else {
    echo "Error creating \"Favorites\" Table: " . $conn->error ."\n";
}

$sql_str = "CREATE TABLE Cinemas(
    ID VARCHAR(10) NOT NULL PRIMARY KEY,
    OWNER VARCHAR(20) NOT NULL,
    NAME VARCHAR(20) NOT NULL
)";

if ($conn->query($sql_str) === TRUE) {
    echo "Table \"Cinemas\" created!\n";
} else {
    echo "Error creating \"Cinemas\" Table: " . $conn->error ."\n";
}

CloseCon($conn);


?>