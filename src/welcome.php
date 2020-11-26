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


<?php
if ($error)
    echo '<p>Could not login user!</p>';
else
    echo '<p>Login Successful!</p>';
    ?>

<form method="post" action="./index.php">
    <button type="submit">Logout</button>
</form>
