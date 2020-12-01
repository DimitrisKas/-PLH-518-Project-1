<?php
include_once '../db_scripts/Models/Users.php';
include_once '../db_scripts/Models/Cinemas.php';
include_once '../db_scripts/Models/Movies.php';
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

                $cinemas = Cinema::GetAllOwnerCinemas($_SESSION['user_id']);
                /* @var $cinema Cinema (IDE type hint) */
                foreach ($cinemas as $cinema)
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
        <?php if (count($cinemas) > 0)
        {
        ?>
            <div class="table-container">
                <table id="admin-table">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Cinema Name</th>
                        <th>Category</th>
                        <th></th>
                        <th></th>
                    </tr>

                    <?php

                    $movies = Movie::GetAllOwnerMovies($_SESSION['user_id']);
                    /* @var $movie Movie (IDE type hint) */
                    foreach ($movies as $movie)
                    {
                        ?>
                        <tr id="user_<?php echo $movie->id?>">
                            <td><div><input id="<?php echo $movie->id?>_id"          type="text" value="<?php echo $movie->id?>"          class="disabled-input" disabled/></div></td>
                            <td><div><input id="<?php echo $movie->id?>_title"       type="text" value="<?php echo $movie->title?>"       class="custom-input"/></div></td>
                            <td><div><input id="<?php echo $movie->id?>_start_date"  type="date"  min="1997-01-01" max="2030-12-31" value="<?php echo $movie->start_date?>"  class="custom-input"/></div></td>
                            <td><div><input id="<?php echo $movie->id?>_end_date"    type="date"  min="1997-01-01" max="2030-12-31" value="<?php echo $movie->end_date?>"    class="custom-input"/></div></td>
                            <td>
                                <div>
                                    <select id="<?php echo $movie->id?>_cinema_name">
                                        <?php
                                            foreach($cinemas as $cinema)
                                            {
                                                echo '<option value="'.$cinema->name.'"' . (($movie->cinema_name === $cinema->name) ? "selected" : "") . '>' .$cinema->name.'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td><div><input id="<?php echo $movie->id?>_category"    type="text" value="<?php echo $movie->category?>"    class="custom-input"/></div></td>
                            <td class="action-td">
                                <div><button id="<?php echo $movie->id?>_submit" class="btn-primary btn-success" onclick="submitMovie('<?php echo $movie->id?>')" >Save</button></div>
                            </td>
                            <td class="action-td">
                                <div><button id="<?php echo $movie->id?>_delete" class="btn-primary btn-danger" onclick="deleteMovie('<?php echo $movie->id?>')" >Delete</button></div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>


                        <tr class="no-hover-row title-row">
                            <td><h5>Add new Movie</h5></td>
                        </tr>
                        <tr id="movie_new" class="no-hover-row">
                            <td><div><input id="new_movie_id"           class="disabled-input" type="text"  value="Auto Generated" disabled/></div></td>
                            <td><div><input id="new_movie_title"        class="custom-input"   type="text"  value=""  placeholder="Enter Movie Title"/></div></td>
                            <td><div><input id="new_movie_start_date"   class="custom-input"   type="date"  min="1997-01-01" max="2030-12-31"  value=""  placeholder="Enter Start Date"/></div></td>
                            <td><div><input id="new_movie_end_date"     class="custom-input"   type="date"  min="1997-01-01" max="2030-12-31"  value=""  placeholder="Enter End Date"/></div></td>
                            <td>
                                <div>
                                    <select id="new_movie_cinema_name">
                                        <option value="" disabled selected>Select a Cinema</option>
                                        <?php
                                        foreach($cinemas as $cinema)
                                        {
                                            echo '<option value="'.$cinema->name.'">'.$cinema->name.'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </td>
                            <td><div><input id="new_movie_category"     class="custom-input"   type="text"  value=""  placeholder="Enter Category"/></div></td>
                            <td class="action-td">
                                <div><button id="new_movie_submit" class="btn-primary btn-success" onclick="addMovie()" >Add</button></div>
                            </td>
                        </tr>
                </table>
            </div>
        <?php
        } else {
        ?>
            <p style="font-size: 20px;">To Add a movie, please add a Cinema first</p>
        <?php
        }
        ?>
    </div>
</div>
</body>
<script type="text/javascript">
    function addCinema()
    {
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

    function addMovie()
    {
        // Check if no Cinema was selected:
        let cinemaName = document.getElementById('new_movie_cinema_name').value

        if (cinemaName === "")
            cinemaName = "<?php echo (count($cinemas) > 0) ? $cinemas[0]->name : ""; ?>";

        // Check if no date was given.
        let startDate = document.getElementById('new_movie_start_date').value;
        let endDate = document.getElementById('new_movie_end_date').value;

        // Get current date, plus the date in 7 days
        let today = new Date();
        let in7days = new Date(today.getTime() +  7 * 24 * 60 * 60 * 1000);

        if (startDate === "")
            startDate = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();

        if (endDate === "")
            endDate = in7days.getFullYear()+'-'+(in7days.getMonth()+1)+'-'+in7days.getDate();

        // Initiate the request
        fetch('async/movie_add.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'movie_title': document.getElementById('new_movie_title').value,
                'movie_start_date': startDate,
                'movie_end_date': endDate,
                'movie_cinema_name': cinemaName,
                'movie_category': document.getElementById('new_movie_category').value,
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

    function submitMovie(movie_id)
    {
        fetch('async/movie_edit.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'movie_id' :  document.getElementById(movie_id+'_id').value,
                'movie_title': document.getElementById(movie_id+'_title').value,
                'movie_start_date': document.getElementById(movie_id+'_start_date').value,
                'movie_end_date': document.getElementById(movie_id+'_end_date').value,
                'movie_cinema_name': document.getElementById(movie_id+'_cinema_name').value,
                'movie_category': document.getElementById(movie_id+'_category').value,
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

    function deleteMovie(movie_id)
    {
        fetch('async/movie_delete.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ 'movie_id': movie_id})
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












