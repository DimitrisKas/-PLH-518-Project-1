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
</head>
<body>
    <h1>Hello world!</h1>
    <p>This is some text</p>
    <?php echo "<h1>Hello world!</h1>";?>
</body>
</html>

