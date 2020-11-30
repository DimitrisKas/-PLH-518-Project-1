<?php
include_once '../db_scripts/Models/Users.php';
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
    <form id="toIndex" action="./index.php" method="post">
        <input type="hidden" name="feedback" value="<?php echo $feedback?>">
        <input type="hidden" name="f_color" value="<?php echo $f_color?>">
        <input type="hidden" name="f_title" value="<?php echo $f_title?>">
        <input type="hidden" name="f_msg_count" value="<?php echo $f_msg_count?>">
    </form>
    <script type="text/javascript">
        document.getElementById("toIndex").submit();
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
            <a href="movies.php">Movies</a>
            <?php
            if ($_SESSION['user_role'] === USER::CINEMAOWNER)
                echo '<a href="owner.php">Owner Panel</a> ';

            if ($_SESSION['user_role'] === USER::ADMIN)
                echo '<a href="administration.php">Admin Panel</a>';
            ?>
        </div>
        <form id="logout-form" method="post" action="./index.php?logout" class="fl-row">
            <span id="username-span"><?php echo $_SESSION['user_username'] ?></span>
            <button type="submit" class="btn-primary">Logout</button>
        </form>
    </div>
    <?php // ---- Navigation Panel - END ----?>

    <div class="main-content" id="admin_content">
        <div class="card">
            <h4>Manage Users</h4>
            <hr/>

            <div class="table-container">
                <table id="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Password</th>
                        <th>E-mail</th>
                        <th>Role</th>
                        <th>Confirmed</th>
                        <th></th>
                        <th></th>
                    </tr>

                    <?php

                    $users = User::GetAllUsers();
                    /* @var $user User (IDE type hint) */
                    foreach ($users as $user)
                    {
                        ?>
                            <tr id="user_<?php echo $user->id?>" onclick="toggleHighlight(this)">
                                <td><div><input id="<?php echo $user->id?>_id"        type="text"  value="<?php echo $user->id?>"       class="disabled-input" disabled/></div></td>
                                <td><div><input id="<?php echo $user->id?>_username"  type="text"  value="<?php echo $user->username?>" class="custom-input"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_name"      type="text"  value="<?php echo $user->name?>"     class="custom-input"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_surname"   type="text"  value="<?php echo $user->surname?>"  class="custom-input"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_password"  type="text"  value="" placeholder="Enter new password..." class="custom-input"/></div></td>
                                <td><div><input id="<?php echo $user->id?>_email"     type="text"  value="<?php echo $user->email?>"    class="custom-input"/></div></td>
                                <td>
                                    <div>
                                        <select id="<?php echo $user->id?>_role" name="role">
                                            <option value="ADMIN" <?php echo $user->role === User::ADMIN ? "selected" : "" ?>>Admin</option>
                                            <option value="CINEMAOWNER" <?php echo $user->role === User::CINEMAOWNER ? "selected" : "" ?>>Cinema Owner</option>
                                            <option value="USER" <?php echo $user->role === User::USER ? "selected" : "" ?>>User</option>
                                        </select>
                                    </div>
                                </td>
                                <td><div><input id="<?php echo $user->id?>_confirmed" type="checkbox" <?php echo $user->confirmed ? "checked" : ""?>/></div></td>
                                <td class="action-td">
                                    <div><button id="<?php echo $user->id?>_submit" class="btn-primary btn-success" onclick="submitUser('<?php echo $user->id?>')" >Save</button></div>
                                </td>
                                <td class="action-td">
                                    <div><button id="<?php echo $user->id?>_delete" class="btn-primary btn-danger" onclick="deleteUser('<?php echo $user->id?>')" >Delete</button></div>
                                </td>
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
    function submitUser(uid)
    {
        this.event.stopPropagation();
        fetch('async/user_edit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'user_id': uid,
                'user_username': document.getElementById(uid+'_username').value,
                'user_name': document.getElementById(uid+'_name').value,
                'user_surname': document.getElementById(uid+'_surname').value,
                'user_password': document.getElementById(uid+'_password').value,
                'user_email': document.getElementById(uid+'_email').value,
                'user_role': document.getElementById(uid+'_role').value,
                'user_confirmed': document.getElementById(uid+'_confirmed').checked ? 'true' : 'false'
            })
        })
            .then( response => {
                return response.json();
            })
            .then( success =>{
                if (success) {
                    location.reload();
                }
            });

    }

    function deleteUser(uid)
    {
        this.event.stopPropagation();
        fetch('async/user_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 'user_id': uid})
        })
            .then( response => {
                return response.json();
            })
            .then( success =>{
              if (success) {
                  location.reload();
              }
            });
    }

    function toggleHighlight(row)
    {
        let rows = document.getElementById("admin-table").children[0].children;
        Array.from(rows).forEach( row => row.classList.remove("highlighted-row"));
        row.classList.add("highlighted-row");
    }
</script>
</html>












