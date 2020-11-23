<?php
include '../db_scripts/Models/User.php';
include '../db_scripts/db_connection.php';
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');

//$user = new User(
//        "Dimitris",
//        "Kastrinakis",
//        "dkastrinakis",
//        "1234",
//        "dk@email.com",
//        User::ADMIN,
//        true
//);
//
//if (!$user->addToDB())
//{
//    echo "Error adding user.\n";
//}
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

