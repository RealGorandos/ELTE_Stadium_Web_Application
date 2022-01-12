<?php 

require_once("utils/_init.php");

if (verify_post("username","email", "password", "confirm-password")) {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm-password"];

    if (empty($username)) {
        $usernameError = "Username must not be empty";
    }
    else if($auth->username_exists($username)){
        $usernameError = "Username already exists";
    }
    if (empty($email)) {
        $emailError = "E-mail address name must not be empty";
    }
    else if(!strpos($email, "@") || !strpos($email, ".")){
        $emailError = "E-mail address is in an incorrect format";
    }
    else if($auth->user_exists($email)){
        $emailError = "E-mail already registered";
    }

    if (strlen($password) < 5) {
        $passwordError = "Password must be at least 5 characters long";
    }

    if ($password !== $confirm_password) {
        $confPasswordError = "Passwords do not match";
    }

    if(!isset($usernameError) && !isset($emailError) && !isset($passwordError) && !isset($confPasswordError)){
        $auth->register([
            "username" => $username,
            "email"    => $email,
            "password" => $password,
            "favorite" => [],
          ]);
        redirect("login.php");
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>Register</title>
</head>
<body>
<video autoplay loop class="back-video" muted plays-inline>
            <source src="res/video/Stadium.mp4" type="video/mp4">
        </video>
        <div id="black_sheet"></div>
<div id="sign-up" class="modal">
    <!-- <span onclick="document.getElementById('sign-up').style.display='none'" class="close" title="Close Modal">&times;</span> -->
    <h1 style="text-align: center; color:#f8f9fb;  text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue;">ELTE Stadium</h1>
    <form class="modal-content animate" action="" novalidate method="post">
        <div class="container">
        <h1>Sign Up</h1>
        <p>Please fill in this form to create an account.</p>
        <hr>

        <label for="username"><b>Username</b></label>
        <?php if(isset($username)):?>
            <input type="text" placeholder="Enter Username" name="username" required value=<?=$username?>>   
            <?php if(isset($usernameError)):?> <span class="error"><?=$usernameError?></span> <?php endif?>
        <?php else:?>
            <input type="text" placeholder="Enter Username" name="username" required>
        <?php endif?>
        <!--  -->
        <label for="email"><b>Email</b></label>
        <?php if(isset($email)):?>
            <input type="text" placeholder="Enter Email" name="email" required value=<?=$email?>>   
            <?php if(isset($emailError)):?> <span class="error"><?=$emailError?></span> <?php endif?>
        <?php else:?>
            <input type="text" placeholder="Enter Email" name="email" required>
        <?php endif?>
        <!--  -->
        <label for="password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="password" required>
        <?php if(isset($passwordError)):?> <span class="error"><?=$passwordError?></span> <?php endif?>

        <label for="confirm-password"><b>Confirm Password</b></label>
        <input type="password" placeholder="Repeat Password" name="confirm-password" required>
        <?php if(isset($confPasswordError)):?> <span class="error"><?=$confPasswordError?></span> <?php endif?>

        
        <div class="clearfix">
            <a href="index.php"><button type="button" onclick="document.getElementById('sign-up').style.display='none'" class="cancelbtn">Back</button></a>
            <button type="submit" class="signupbtn">Sign Up</button>
        </div>
        <a href="login.php"><button type="button" id="registered">I already have an account</button></a>
        </div>
    </form>
    </div>
</body>
</html>