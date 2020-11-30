<?php
include_once('../db_scripts/Models/Users.php');
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

    User::GetAllUsers();
}

//initDB();
logger("-- In index: ");
$f_title = "";
$f_text  = "";
$isSigningUp = false;

session_start();

// Check if logging out
if (isset($_GET['logout']))
    $_SESSION['login'] = false;
else if (isset($_SESSION['login']) && $_SESSION['login'] === true)
{
    // Redirecting to Welcome page
    ?>
    <form id="toIndex" action="./welcome.php" method="post"></form>
    <script type="text/javascript">
        document.getElementById("toIndex").submit();
    </script>
    <?php
}



// Check Feedback Box
if (!empty($_POST['feedback']) && $_POST['feedback'] === "true")
{
    logger("Got feedback!");
    $f_title  = isset($_POST['f_title'])  ? $_POST['f_title'] : "";
    $f_text  = isset($_POST['f_msg'])  ? $_POST['f_msg'] : "";
    $hasFeedback = true;
}
else
{
    logger("No feedback found!");
    $hasFeedback = false;
}

// Only show sign up
if (!empty($_POST['prevLocation']) && $_POST['prevLocation'] === "signup" && $_POST['userAdded'] === "false")
    $isSigningUp = true;

// Fallback value for feedback box
if (!isset($_POST['f_color']))
    $_POST['f_color'] = "f-warning";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project 1 - Cinemania</title>
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
        <div id="index-feedback" class="feedback-box <?php echo $_POST['f_color']?>" <?php if (!$hasFeedback) echo 'hidden'?>>
            <span class="feedback-title"><?php echo $f_title?></span>
            <?php
            if($_POST['f_msg_count'] > 0)
            {
                echo '<ul>';
                for($i =0; $i < $_POST['f_msg_count']; $i++) {
                    echo '<li class="feedback-text">'.$_POST['f_msg'][$i].'</li>';
                }
                echo '</ul>';
            }

            ?>
        </div>
        <div id="index-card-login" class="card" <?php echo ($isSigningUp ? "hidden":"")?>>
            <h3 class="text-color-dark">Log in</h3>
            <form action="./welcome.php" method="post" id="login-form" class="fl-col">
                <label for="username_input">Username</label>
                <input class="custom-input" type="text" id="username_input" name="username" placeholder="Username"/>
                <label for="password_input">Password:</label>
                <input class="custom-input" type="password" id="password_input"  name="password" placeholder="Password"/>
                <input type="submit" value="Submit" class="btn-primary"/>
            </form>
        </div>

        <div id="index-card-signup" class="card" <?php echo ($isSigningUp ? "":"hidden")?>>
            <h3 class="text-color-dark">Sign up</h3>
            <form action="./signup.php" method="post" id="login-form_signup" class="fl-col">

                <label for="username_input_signup">Username</label>
                <input class="custom-input" type="text" id="username_input_signup" name="username" placeholder="Username"
                       value="<?php echo isset($_POST['username']) ? $_POST['username']: '';?>"/>

                <label for="password_input_signup">Password:</label>
                <input class="custom-input" type="password" id="password_input_signup"  name="password" placeholder="Password"
                       value="<?php echo isset($_POST['password']) ? $_POST['password']: '';?>"/>

                <label for="name_input">Name</label>
                <input class="custom-input" type="text" id="name_input"  name="name" placeholder="Name"
                       value="<?php echo isset($_POST['name']) ? $_POST['name']: '';?>"/>

                <label for="surname_input">Surname</label>
                <input class="custom-input" type="text" id="surname_input"  name="surname" placeholder="Surname"
                       value="<?php echo isset($_POST['surname']) ? $_POST['surname']: '';?>"/>

                <label for="email_input">Email</label>
                <input class="custom-input" type="text" id="email_input"  name="email" placeholder="E-mail"
                       value="<?php echo isset($_POST['email']) ? $_POST['email']: '';?>"/>

                <label for="roles_input">Roles</label>
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
    let isSigningUp = <?php echo ($isSigningUp ? "true":"false")?>;
    let signupCard = document.getElementById("index-card-signup");
    let loginCard = document.getElementById("index-card-login");

    let feedbackBox = document.getElementById("index-feedback");

    function signup_toggle()
    {
        // Reverse the boolean flag
        isSigningUp = !isSigningUp;

        // Hide last feedback box
        feedbackBox.hidden = true;

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

