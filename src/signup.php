<?php
include_once('../db_scripts/Models/User.php');
include_once('../db_scripts/db_connection.php');
include_once('../Utils/Random.php');
include_once('../Utils/Logs.php');
// File only with logic for signing up.

logger("-- In signup: ");

$f_title = "";
$feedback = "true";
$f_msg = [];
$f_msg_count = 0;
$userAdded = "false";


if (!empty($_POST))
{
    $error_Count = 0;
    if (empty($_POST['username']))
    {
        logger("No username provided");
        $f_msg[$error_Count++] = "You need to provide a Username";
    }
    if (empty($_POST['password']))
    {
        logger("No password provided");
        $f_msg[$error_Count++] = "You need to provide a Password";
    }
    if (empty($_POST['email']))
    {
        logger("No email provided");
        $f_msg[$error_Count++] = "You need to provide a Email";
    }
    if (empty($_POST['role']))
    {
        logger("No role selected");
        $f_msg[$error_Count++] = "No role was specified";
        $_POST['role'] = USER::USER;
    }

    // Setup variables for use in POST data
    logger("Error count: " .  $error_Count);
    $f_msg_count = $error_Count;

    if ($error_Count == 0)
    {
        $user = new User($_POST['name'], $_POST['surname'],$_POST['username'],$_POST['password'], $_POST['email'],$_POST['role'], false);
        if ($user->addToDB() === false)
        {
            logger("Error adding user.");
            $f_title = "Internal error while adding new user";
        }
        else // Success
        {
            logger("User added successfully!");
            $userAdded =true;
            $f_title = "Sign up successful!";
            $f_msg[$f_msg_count++] = "You need to wait for an Admin to confirm you!";
        }
    }
    else
    {
        $f_title = "Error during signup";
    }

}
?>

<form id="signup-form" action="./index.php" method="post">

    <?php //Previous User Data
        if (!$userAdded)
        {
            echo '<input type="hidden" name="username" value="'. isset($_POST['username']) ? $_POST['username']: '' . '">';
            echo '<input type="hidden" name="password" value="'. isset($_POST['password']) ? $_POST['password']: '' . '">';
            echo '<input type="hidden" name="name" value="'. isset($_POST['name']) ? $_POST['name']: '' . '">';
            echo '<input type="hidden" name="surname" value="'. isset($_POST['surname']) ? $_POST['surname']: '' . '">';
            echo '<input type="hidden" name="email" value="'. isset($_POST['email']) ? $_POST['email']: '' . '">';
        }
    ?>
    <input type="hidden" name="username" value="<?php echo isset($_POST['username']) ? $_POST['username']: ''?>">
    <input type="hidden" name="password" value="<?php echo isset($_POST['password']) ? $_POST['password']: ''?>">
    <input type="hidden" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name']: ''?>">
    <input type="hidden" name="surname" value="<?php echo isset($_POST['surname']) ? $_POST['surname']: ''?>">
    <input type="hidden" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email']: ''?>">

    <?php // Feedback Data ?>
    <input type="hidden" name="f_title" value="<?php echo $f_title?>">
    <input type="hidden" name="f_msg_count" value="<?php echo $f_msg_count?>">
    <input type="hidden" name="feedback" value="<?php echo $feedback?>">

    <input type="hidden" name="prevLocation" value="signup">
    <input type="hidden" name="userAdded" value="<?php echo $userAdded?>">
    <input type="hidden" name="feedback" value="<?php echo $feedback?>">
    <input type="hidden" name="f_title" value="<?php echo $f_title?>">
    <input type="hidden" name="f_msg_count" value="<?php echo $f_msg_count?>">
    <input type="hidden" name="feedback" value="<?php echo $feedback?>">
    <?php
        for($i=0; $i < $f_msg_count; $i++)
        {
            echo '<input type="hidden" name="f_msg['.$i.']" value="'.$f_msg[$i] .'">';
        }
    ?>
</form>
<script type="text/javascript">
    document.getElementById('signup-form').submit();
</script>
