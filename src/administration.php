<?php
include_once '../db_scripts/Models/User.php';
include_once '../db_scripts/db_connection.php';
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');

session_start();
logger("-- In Administration");

// Check if User is logged in AND is an Admin
if (isset($_SESSION['login'])
    && $_SESSION['login'] === true
    && isset($_SESSION['user_role'])
    && $_SESSION['user_role'] === User::ADMIN)
{
    // User already logged in...
    logger("User: " . $_SESSION['user_username']);
    logger("Role: " . $_SESSION['user_role']);
}
else
{
    // Redirect to index
    $feedback = "true";
    $f_title = "You do not have access to that page.";
    $f_msg_count = 0;
    $f_color = "f-error";
    ?>
    <form id="admin-to-index-form" action="./index.php" method="post">
        <input type="hidden" name="feedback" value="<?php echo $feedback?>">
        <input type="hidden" name="f_color" value="<?php echo $f_color?>">
        <input type="hidden" name="f_title" value="<?php echo $f_title?>">
        <input type="hidden" name="f_msg_count" value="<?php echo $f_msg_count?>">
    </form>
    <script type="text/javascript">
        document.getElementById("admin-to-index-form").submit();
    </script>
    <?php
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CineMania</title>
    <link rel='stylesheet' type='text/css' href='CSS/main.css' />
    <link rel='stylesheet' type='text/css' href='CSS/administration.css' />
</head>

<body class="no-overflow">
    <?php // ---- Navigation Panel - START ----?>
    <div class="top-nav">
        <div class="nav-items">
            <h5 id="top-nav-title">CineMania</h5>
            <a href="welcome.php">Home</a>
            <?php
            if ($_SESSION['user_role'] === USER::CINEMAOWNER)
                echo '<a href="owner.php">Owner Panel</a> ';

            if ($_SESSION['user_role'] === USER::ADMIN)
                echo '<a href="administration.php">Admin Panel</a>';
            ?>
        </div>
        <form method="post" action="./index.php" class="fl-col">
            <button type="submit" class="btn-primary">Logout</button>
        </form>
    </div>
    <?php // ---- Navigation Panel - END ----?>

    <div class="main-content" id="admin_content">
        <div class="card">
            <h4>Manage Users</h4>
            <hr/>

            <div class="table-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Password</th>
                        <th>e-mail</th>
                        <th>Role</th>
                        <th>Confirm</th>
                        <th>Submit</th>
                    </tr>
                    <?php

                    $users = User::getAllUsers();
                    /* @var $user User (IDE type hint) */
                    foreach ($users as $user)
                    {
                        ?>
                            <tr id="user_<?php echo $user->id?>">
                                <td><div><input id="<?php echo $user->id?>_id"        type="text"  value="<?php echo $user->id?>"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_username"  type="text"  value="<?php echo $user->username?>"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_name"      type="text"  value="<?php echo $user->name?>"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_surname"   type="text"  value="<?php echo $user->surname?>"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_password"  type="text"  value="" placeholder="Enter new password..."/></div></td>
                                <td><div><input id="<?php echo $user->id?>_email"     type="text"  value="<?php echo $user->email?>"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_role"      type="text"  value="<?php echo $user->role?>"/></div></td>
                                <td></td>
                                <td><div><button id="<?php echo $user->id?>_submit" class="btn-primary" onclick="submitUser(this)" >Save</button></div></td>
                            </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

        </div>

    </div>
</body>
<script type="text/javascript">
    function submitUser(button)
    {

    }
</script>
</html>












