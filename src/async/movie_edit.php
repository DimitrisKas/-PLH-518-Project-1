<?php
include_once '../../db_scripts/Models/Users.php';
include_once '../../db_scripts/Models/Movies.php';
include_once '../../db_scripts/db_connection.php';
include_once('../../Utils/Random.php');
include_once('../../Utils/Logs.php');

session_start();
logger("-- In Edit Movie");

// Check if User is logged in AND is an Admin
if (isset($_SESSION['login'])
    && $_SESSION['login'] === true
    && isset($_SESSION['user_role'])
    && $_SESSION['user_role'] === User::CINEMAOWNER)
{
    // User already logged in...
    logger("User: " . $_SESSION['user_username']);
    logger("Role: " . $_SESSION['user_role']);

    $data = json_decode(file_get_contents('php://input'), true);

    If (isset($data['movie_id']))
    {
        $success_flag = Movie::EditMovie(
            $data['movie_id'], $data['movie_title'], $data['movie_start_date'],
            $data['movie_end_date'], $data['movie_cinema_name'], $data['movie_category']
        );

        header('Content-type: application/json');
        echo json_encode($success_flag);
        exit();
    }
}

// If failed for any reason...
echo json_encode(false);


