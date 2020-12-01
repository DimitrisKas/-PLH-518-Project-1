<?php
include_once '../db_scripts/Models/Users.php';
include_once '../db_scripts/Models/Movies.php';
include_once '../db_scripts/db_connection.php';
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');

session_start();
logger("-- In Movies");

// Check if User is logged in AND is an Admin
if (isset($_SESSION['login'])
    && $_SESSION['login'] === true)
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
    <form id="redirect-form" action="./index.php" method="post">
        <input type="hidden" name="feedback" value="<?php echo $feedback?>">
        <input type="hidden" name="f_color" value="<?php echo $f_color?>">
        <input type="hidden" name="f_title" value="<?php echo $f_title?>">
        <input type="hidden" name="f_msg_count" value="<?php echo $f_msg_count?>">
    </form>
    <script type="text/javascript">
        document.getElementById("redirect-form").submit();
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
    <link rel='stylesheet' type='text/css' href='CSS/movies.css' />
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

<div class="main-content" id="movies_content">
    <div class="card">
        <h4>Manage Users</h4>
        <hr/>

        <div class="table-container">
            <table id="admin-table">
                <tr>
                    <th>Favorite</th>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Cinema Name</th>
                    <th>Category</th>
                </tr>

                <?php
                if (isset($_GET['search']))
                {
                    $movies = Movie::Search($_SESSION['user_id'], $_GET['title'], $_GET['date'], $_GET['cin_name'], $_GET['cat']);
                }
                else
                {
                    $movies = Movie::GetAllMovies($_SESSION['user_id']);
                }
                /* @var $movie Movie (IDE type hint) */
                foreach ($movies as $movie)
                {
                    ?>
                    <tr id="movie_<?php echo $movie->id?>">
                        <td><div><input id="<?php echo $movie->id?>_favorite"   type="checkbox" <?php echo $movie->favorite ? "checked" : ""?> onclick="toggleFavorite('<?php echo $movie->id?>', this)"/></div></td>
                        <td class="td-movie-title"><div><span id="<?php echo $movie->id?>_title"       ><?php echo $movie->title?></span></div></td>
                        <td><div><span id="<?php echo $movie->id?>_start_date"  ><?php echo $movie->start_date?></span></div></td>
                        <td><div><span id="<?php echo $movie->id?>_end_date"    ><?php echo $movie->end_date?></span></div></td>
                        <td><div><span id="<?php echo $movie->id?>_cinema_name" ><?php echo $movie->cinema_name?></span></div></td>
                        <td><div><span id="<?php echo $movie->id?>_category"    ><?php echo $movie->category?></span></div></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <div class="search-cont title-row fl-col">
                <h5>Search: </h5>
                <form method="GET" action="./movies.php">
                    <input name="search" class="custom-input" type="hidden"  value="1" placeholder="Title"/>
                    <input name="title" class="custom-input" type="text"  value="" placeholder="Title"/>
                    <input name="date" class="custom-input" type="date"  value="" placeholder="Date"/>
                    <input name="cin_name" class="custom-input" type="text"  value="" placeholder="Cinema Name"/>
                    <input name="cat" class="custom-input" type="text"  value="" placeholder="Category"/>
                    <input class="btn-primary" type="submit" value="Search"/>
                </form>
            </div>
        </div>

    </div>

</div>
</body>
<script type="text/javascript">
    function toggleFavorite(movie_id, checkbox)
    {
        fetch('async/favorite_toggle.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                'isFavorite': checkbox.checked ? "true" : "false",
                'movie_id' : movie_id
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
</script>
</html>












