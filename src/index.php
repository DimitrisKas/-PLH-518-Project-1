<?php
include_once('../db_scripts/Models/User.php');
include_once('../db_scripts/drop_db.php');
include_once('../db_scripts/create_db.php');
include_once('../db_scripts/create_tables.php');
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');

function initDB()
{
    dropDB();
    createDB();
    createTables();

    $user = new User(
            "Dimitris",
            "Kastrinakis",
            "dkastrinakis",
            "1234",
            "dk@email.com",
            User::ADMIN,
            true
    );
    if (!$user->addToDB())
    {
        logger("Error adding user.");
    }
    $user = new User(
        "Bob",
        "Bobby",
        "user2",
        "1234",
        "dk@email2.com",
        User::ADMIN,
        true
    );


    if (!$user->addToDB())
    {
        logger("Error adding user.");
    }

    User::getAllUsers();
}


//initDB();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project 1</title>
    <link rel='stylesheet' type='text/css' href='CSS/main.css' />
    <link rel='stylesheet' type='text/css' href='CSS/index.css' />
</head>
<body id="index-body">
    <div class="upper-half"></div>
    <div class="bottom-half"></div>
    <div id="top-nav">
        <h2 id="Logo-Text">CineMania</h2>
    </div>
    <div id="index-card" class="card">
        <h3 class="text-color-dark">Log in</h3>
        <form action="./welcome.php" method="post" id="login-form" class="fl-row">
            <label for="username_input">Username</label>
            <input type="text" id="username_input" name="username" placeholder="Username"/>
            <label for="password_input">Password:</label>
            <input type="password" id="password_input"  name="password" placeholder="Password"/>
            <input type="submit" class="btn-primary"/>
        </form>
    </div>
</body>
</html>

