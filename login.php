<?php 

require_once("utils/_init.php");

if (verify_post("username", "password")) {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
  
    $user = $auth->authenticate($username, $password);
    if ($user === NULL) {
      $errors = "Invalid username or password";
    }

    if (!isset($errors)) {
      $auth->login($user);
      redirect("index.php");
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
    <title>Log in</title>
</head>
<body>

<video autoplay loop class="back-video" muted plays-inline>
            <source src="res/video/Stadium.mp4" type="video/mp4">
        </video>
        <div id="black_sheet"></div>


<div id="log-in" class="login-modal">
<h1 style="text-align: center; color:#f8f9fb;  text-shadow: 1px 1px 2px black, 0 0 25px blue, 0 0 5px darkblue;">ELTE Stadium</h1>
  <form class="modal-content animate" action="" novalidate method="post">
    <div class="imgcontainer">
      <!-- <span onclick="document.getElementById('log-in').style.display='none'" class="close" title="Close Modal">&times;</span> -->
      <img src="res/img/avatar.png" alt="Avatar" class="avatar">
    </div>

    <div class="container">
    <label for="username"><b>Username</b></label>
    <?php if(isset($username)):?>
        <input type="text" placeholder="Enter Username" name="username" value="<?=$username?>" required>
  
    <?php else:?>
        <input type="text" placeholder="Enter Username" name="username" required/>
    <?php endif?>

      <label for="password"><b>Password</b></label>
      <input type="password" placeholder="Enter Password" name="password" required>
      <?php if(isset($errors)):?> <span class="error"><?=$errors?></span> <?php endif?>
        
      <button type="submit">Login</button>
    </div>

    <div class="container" style="background-color:#f1f1f1">
    <a href="index.php"><button type="button" onclick="document.getElementById('log-in').style.display='none'" class="cancelbtn">Cancel</button></a>
      <a href="register.php"><button type="button" id="unregistered">I don't have an account</button></a>
    </div>
  </form>
</div>

</body>
</html>