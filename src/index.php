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
    <div id="index-main-form" class="fl-col">
        <div id="index-card-login" class="card">
            <h3 class="text-color-dark">Log in</h3>
            <form action="./welcome.php" method="post" id="login-form" class="fl-col">
                <label for="username_input">Username</label>
                <input type="text" id="username_input" name="username" placeholder="Username"/>
                <label for="password_input">Password:</label>
                <input type="password" id="password_input"  name="password" placeholder="Password"/>
                <input type="submit" class="btn-primary"/>
            </form>
        </div>

        <div id="index-card-signup" class="card">
            <h3 class="text-color-dark">Sign up</h3>
            <form action="./welcome.php" method="post" id="login-form" class="fl-col">
                <label for="username_input">Username</label>
                <input type="text" id="username_input" name="username" placeholder="Username"/>
                <label for="password_input">Password:</label>
                <input type="password" id="password_input"  name="password" placeholder="Password"/>

                <input type="text" id="name_input"  name="name" placeholder="Name"/>
                <input type="text" id="surname_input"  name="surname" placeholder="Surname"/>
                <input type="text" id="email_input"  name="email" placeholder="E-mail"/>
                <select id="roles_input" name="role">
                    <option value="" disabled selected>Choose option</option>
                    <option value="ADMIN">Admin</option>
                    <option value="CINEMAOWNER">Cinema Owner</option>
                    <option value="USER">User</option>
                </select>
                <input type="submit" value="Sign Up" class="btn-primary"/>
            </form>
        </div>
        <div class="fl-col">
            <span id="signup-text" onclick="signup_toggle()">Do you want to Sign Up instead?</span>
        </div>
    </div>
</body>

<script type="text/javascript">
    // On document load:
    let isSigningUp = false;
    let signupCard = document.getElementById("index-card-signup");
    let loginCard = document.getElementById("index-card-login");

    signupCard.hidden = true;

    function signup_toggle()
    {
        // Reverse the boolean flag
        isSigningUp = !isSigningUp;

        if (isSigningUp)
        {
            signupCard.hidden = false;
            loginCard.hidden = true;
        }
        else
        {
            signupCard.hidden = true;
            loginCard.hidden = false;

        }
    }
</script>
</html>

