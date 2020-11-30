<?php
include_once '../db_scripts/Models/Users.php';
include_once '../db_scripts/Models/Cinemas.php';
include_once '../db_scripts/db_connection.php';
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');

session_start();
logger("-- In Owner");

// Check if User is logged in AND is an Cinema Owner
if (isset($_SESSION['login'])
    && $_SESSION['login'] === true
    && isset($_SESSION['user_role'])
    && $_SESSION['user_role'] === User::CINEMAOWNER)
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
    <link rel='stylesheet' type='text/css' href='CSS/owner.css' />
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

<div class="main-content" id="owner_content">
    <div class="card">
        <h4>Manage Your Cinemas</h4>
        <hr/>

        <div class="table-container">
            <table id="admin-table">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Owner</th>
                    <th></th>
                    <th></th>
                </tr>

                <?php

                $users = Cinema::GetAllOwnerCinemas($_SESSION['user_id']);
                /* @var $cinema Cinema (IDE type hint) */
                foreach ($users as $cinema)
                {
                    ?>
                    <tr id="cinema_<?php echo $cinema->id?>">
                        <td><div><input id="<?php echo $cinema->id?>_id"      type="text"  value="<?php echo $cinema->id?>"     class="disabled-input" disabled/></div></td>
                        <td><div><input id="<?php echo $cinema->id?>_name"    type="text"  value="<?php echo $cinema->name?>"   class="custom-input"/></div></td>
                        <td><div><input id="<?php echo $cinema->id?>_owner"   type="text"  value="<?php echo $cinema->owner." (".$_SESSION['user_username'].")" ?>"  class="disabled-input" disabled/></div></td>
                        <td class="action-td">
                            <div><button id="<?php echo $cinema->id?>_submit" class="btn-primary btn-success" onclick="submitCinema('<?php echo $cinema->id?>')" >Save</button></div>
                        </td>
                        <td class="action-td">
                            <div><button id="<?php echo $cinema->id?>_delete" class="btn-primary btn-danger" onclick="deleteCinema('<?php echo $cinema->id?>')" >Delete</button></div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr class="no-hover-row title-row">
                    <td><h5>Add new Cinema</h5></td>
                </tr>
                <tr id="cinema_new" class="no-hover-row">
                    <td><div><input id="new_cinema_id"     class="disabled-input" type="text"  value="Auto Generated" disabled/></div></td>
                    <td><div><input id="new_cinema_name"   class="custom-input"   type="text"  value=""  placeholder="Enter Name"/></div></td>
                    <td><div><input id="new_cinema_owner"  class="disabled-input" type="text"  value="<?php echo $_SESSION['user_id']." (".$_SESSION['user_username'].")" ?>" disabled/></div></td>
                    <td class="action-td">
                        <div><button id="new_cinema_submit" class="btn-primary btn-success" onclick="addCinema()" >Add</button></div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="card">
        <h4>Manage Your Movies</h4>
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
    function addCinema()
    {
        this.event.stopPropagation();
        fetch('async/cinema_add.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'cinema_name': document.getElementById('new_cinema_name').value,
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

    function submitCinema(cinema_id)
    {
        this.event.stopPropagation();
        fetch('async/cinema_edit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'cinema_id': cinema_id,
                'cinema_name': document.getElementById(cinema_id+'_name').value,
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

    function deleteCinema(cinema_id)
    {
        this.event.stopPropagation();
        fetch('async/cinema_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 'cinema_id': cinema_id})
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
</script>
</html>












