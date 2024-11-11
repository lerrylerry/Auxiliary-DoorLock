<?php
require('dbcred/db.php');

if (isset($_SESSION['loginid'])) {
  header("location: admin/homepage.php");
}

if (isset($_POST['changepass'])) {

    if($_POST['password'] == $_POST['changepass']){
        $sqlupdateup = "UPDATE `tbadmin` SET `password`='".$_POST['password']."' WHERE username='" . $_POST['username'] . "' AND otp ='".$_POST['otp']."'";
        mysqli_query($db, $sqlupdateup);
        header("location:  login.php");
    }else{
        $error ="Mismatch Password";
    }


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Auxiliary | New Password</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark ">
        <div class="container-fluid">
          <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="" style="width: 60px; height: 60px;">
            <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
          </div>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="dropdown">
                <button class="btn dropdown-toggle serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                  Services
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" value="borrowANDreturn.php">Borrow/Get Items</a></li>
                  <li><a class="dropdown-item" value="minor-repair.php">Request a minor repair</a></li>
                </ul>
            </div> 
            <div class="divLogin d-flex justify-content-center">
                <a href="login.php" class="loginBtn" style="text-decoration: none;">
                  <button class="btn btn-outline-success" type="button" id="login">Login</button>
                </a>
            </div>
          </div>
        </div>
      </nav>

       
      <div class="card login-container mx-auto mt-5 mb-5" style="width: 25rem; height: 35rem; border: 3px solid #0e0e0f;">
        
        <div class="card-body login-content" style="z-index: 1;">
            <h1 class="card-title login-label">NEW PASSWORD</h1>
            <form method="post" action="">
            <div class="login-form">
                <label>Username:</label><br>
                <input type="hidden" id="email" name="email" value="<?php echo $_GET['email']?>">
                <input type="hidden"  name="otp" value="<?php echo $_GET['otp']?>"><br>
                <input type="text" id="username" name="username" placeholder="Enter Username"><br>
                <div id="usernameError" class="error"></div>
                <label>New Password:</label><br>
                <input type="text" id="password" name="password" placeholder="Enter Password"><br>
                <div id="usernameError" class="error"></div>
                <label>Confirm Password:</label><br>
                <input type="text" id="changepass" name="changepass" placeholder="Confirm Password"><br>
                <div id="usernameError" class="error"><?php if (isset($error)) { echo $error;}?></div>
                <button class="submit mx-auto" id="loginBtn">Confirm</button><br>
            </div>
            </form>
        </div>
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;"> 
    </div>
</body>
<script src="js/login.js" type="module"></script>
</html>