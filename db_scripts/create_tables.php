<?php 
include_once 'db_connection.php';

function createTables()
{
    $conn = OpenCon(true);

    $sql_str = "CREATE TABLE Users(
        ID VARCHAR(10) NOT NULL PRIMARY KEY,
        NAME VARCHAR(20),
        SURNAME VARCHAR(20),
        USERNAME VARCHAR(20) NOT NULL UNIQUE,
        PASSWORD VARCHAR(20) NOT NULL,
        EMAIL VARCHAR(50) NOT NULL UNIQUE,
        ROLE ENUM('ADMIN', 'CINEMAOWNER', 'USER'),
        CONFIRMED BOOLEAN
    )";

    if ($conn->query($sql_str) === TRUE) {
        logger("Table \"Users\" created!");
    } else {
        logger("Error creating \"Users\" Table: " . $conn->error);
    }


    $sql_str = "CREATE TABLE Movies(
        ID VARCHAR(10) NOT NULL PRIMARY KEY,
        TITLE VARCHAR(50) NOT NULL,
        STARTDATE DATE,
        ENDDATE DATE, 
        CINEMANAME VARCHAR(20),
        CATEGORY VARCHAR(20)
    )";
    if ($conn->query($sql_str) === TRUE) {
        logger("Table \"Movies\" created!");
    } else {
        logger("Error creating \"Movies\" Table: " . $conn->error);
    }

    $sql_str = "CREATE TABLE Favorites(
        ID VARCHAR(10) NOT NULL PRIMARY KEY,
        USERID  VARCHAR(10) NOT NULL,
        MOVIEID  VARCHAR(10) NOT NULL,
        FOREIGN KEY (USERID) REFERENCES Users(ID) ON DELETE CASCADE,
        FOREIGN KEY (MOVIEID) REFERENCES Movies(ID) ON DELETE CASCADE,
    )";
    if ($conn->query($sql_str) === TRUE) {
        logger("Table \"Favorites\" created!");
    } else {
        logger("Error creating \"Favorites\" Table: " . $conn->error);
    }

    $sql_str = "CREATE TABLE Cinemas(
        ID VARCHAR(10) NOT NULL PRIMARY KEY,
        OWNER VARCHAR(20) NOT NULL,
        NAME VARCHAR(20) NOT NULL
    )";

    if ($conn->query($sql_str) === TRUE) {
        logger("Table \"Cinemas\" created!");
    } else {
        logger("Error creating \"Cinemas\" Table: " . $conn->error);
    }

    CloseCon($conn);
}

