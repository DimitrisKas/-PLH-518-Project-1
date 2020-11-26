<?php
include_once '../db_scripts/Models/User.php';
include_once '../db_scripts/db_connection.php';
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] === true)
{
    // User already logged in...
    logger("User already logged in");
}
else
{
    $error = false;
    if (empty($_POST['username'])){
        logger("Empty username...");
        header("Location: " . $_SERVER['BASE_URL']);
        $error = true;
    }

    if (empty($_POST['password'])) {
        logger("Empty password...");
        header("Location: " . $_SERVER['BASE_URL']);
        $error = true;
    }

    if ( !$error )
    {
        $currentUser = User::LoginUser($_POST['username'], $_POST['password']);

        if ($currentUser === false)
        {
            header("Location: " . $_SERVER['BASE_URL']);
            logger("Redirecting to index");
        }
        else {
            $_SESSION['user_id'] = $currentUser->id;
            $_SESSION['user_username'] = $currentUser->username;
            $_SESSION['user_role'] = $currentUser->role;
            $_SESSION['user_email'] = $currentUser->email;
            $_SESSION['user_name'] = $currentUser->name;
            $_SESSION['user_surname'] = $currentUser->surname;

            $_SESSION['login'] = true;
            logger("Logged in User: " . $currentUser->username);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineMania - Welcome</title>
    <link rel='stylesheet' type='text/css' href='CSS/main.css' />
    <link rel='stylesheet' type='text/css' href='CSS/welcome.css' />
</head>
<body class="no-overflow">
    <div class="top-nav">
        <div class="nav-items">
            <h5 id="top-nav-title">CineMania</h5>
            <span>Home</span>
        </div>
        <form method="post" action="./index.php" class="fl-col">
            <button type="submit" class="btn-primary">Logout</button>
        </form>
    </div>
    <div class="main-content">
        <div id="welcome-options">
            <div class="card welcome-option">
                <h5>Browse Movies</h5>
                <p>View a list of all available Movies</p>
            </div>
            <?php if ($_SESSION['user_role'] == USER::CINEMAOWNER)
                echo '
                    <div class="card welcome-option">
                        <h5>Manage your Movies</h5>
                        <p>View and Edit your registered Movies</p>
                    </div>
                '?>
            <?php if ($_SESSION['user_role'] == USER::ADMIN)
                echo '
                    <div class="card welcome-option">
                        <h5>Manage Users</h5>
                        <p>View and Edit all registered Users.</p>
                    </div>
                '?>
        </div>
    </div>

</body>

</html>
