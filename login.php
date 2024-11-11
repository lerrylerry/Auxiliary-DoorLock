<?php
require('dbcred/db.php');
session_start();

if (isset($_SESSION['loginid'])) {
  header("location: admin/homepage.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $mypassword = mysqli_real_escape_string($db, $_POST['password']);
    $login = "SELECT id,username,name,password,email,role FROM tbadmin WHERE username='" . $myusername . "'";
    $result = mysqli_query($db, $login);
    $count = @mysqli_num_rows($result);
    if ($count == 1) {
        while ($results = mysqli_fetch_array($result)) {
            if ($mypassword == $results['password']) {
                $_SESSION['user'] = $myusername;
                $_SESSION['name'] = $results['name'];
                $_SESSION['email'] = $results['email'];
                $_SESSION['loginid'] = $results['id'];
                if ($results['role'] =="assistant") {
                    header("location: assistant/homepage.php");
                } elseif ($results['role'] =="admin") {
                    header("location: admin/homepage.php");
                }else {
                    $_SESSION['errMsg'] = "You are not allowed to access this page";
                    header("location:  index.php");
                }
            }else{
                $error = "Invalid Login";
            }
        }
    } else {
        $error = "Invalid Login";
    }
}
?>


<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Auxiliary | Login</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark">
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
                  <li><a class="dropdown-item" value="repair - userTOadmin.php">Request a minor repair</a></li>
                  <li><a class="dropdown-item" value="vehicle-reservation.php">Request for vehicle</a></li>
                </ul>
            </div> 
            <div class="d-flex loginBtn">
              <button class="btn btn-outline-success" type="button">Login</button>
            </div>
          </div>
        </div>
      </nav>

      <nav class="navbar navbar-expand-md bg-dark">
        <div class="container-fluid">
          <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="" style="width: 55px; height: 55px;">
            <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
          </div>
          <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navdiv d-flex justify-content-center w-100">
              <div class="dropdown mx-auto">
                <button class="btn dropdown-toggle text-white serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Services
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" value="borrowANDreturn.php">Borrow/Get Items</a></li>
                  <li><a class="dropdown-item" value="repair - userTOadmin.php">Request a minor repair</a></li>
                  <li><a class="dropdown-item" value="vehicle-reservation.php">Request for vehicle</a></li>
                </ul>
              </div>
              <div class="divLogin d-flex justify-content-center">
                <a href="login.php" class="loginBtn" style="text-decoration: none;">
                  <button class="btn btn-outline-success" type="button" id="login">Login</button>
                </a>
              </div>
            </div>
          </div>
        </div>
    </nav>
       
    <div class="card login-container" style="width: 25rem; height: 33rem; border: 3px solid #0e0e0f;">
        
        <div class="card-body login-content" style="z-index: 1;">
            <h1 class="card-title login-label">LOG IN</h1>
            <form method="post" action="">
            <div class="login-form">
                <label>Username:</label><br>
                <input type="text" id="username" name="username" placeholder="Enter Username"><br>
                <div id="usernameError" class="error"></div>
                <label>Password:</label><br>
                <input type="password" id="password" name="password" placeholder="Enter password"><br>
                <div id="passwordError" class="error"></div>
                <label class="rmbr" style="position: relative; top: 15px;">Remember Me</label>
                <input type="checkbox" id="rmbr" style="position: relative; top: 15px;"><br>
                <button class="submit" id="loginBtn" style="position: relative; top:15px;">Log In</button><br>
            </form>
                <p class="sulink" style="position: relative; top:25px;">Forgot password? Click <a href="forgotpass.php" id="sulink">"here"</a> to change!!</p>
            </div>
        </div>
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;"> 
    </div>
</body>
<script src="js/login.js" type="module"></script>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS-->
    
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">

    <!--JS-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="css/login.css">

    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css"> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>>
    <link rel="stylesheet" href="css/login.css"> -->
    <title>Auxiliary | Login</title>
</head>
<body>

      <nav class="navbar navbar-expand-md bg-dark">
        <div class="container-fluid">
          <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="" style="width: 55px; height: 55px;">
            <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
          </div>
          <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navdiv d-flex justify-content-center w-100">
              <div class="dropdown mx-auto">
                <button class="btn dropdown-toggle text-white serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
        </div>
    </nav>
       
    <div class="card login-container mx-auto mt-5 mb-5 " style="width: 25rem; height: 33rem; border: 3px solid #0e0e0f;">
        
        <div class="card-body login-content" style="z-index: 1;">
            <h1 class="card-title login-label">LOG IN</h1>
            <form method="post" action="">
            <div class="login-form">
                <label>Username:</label><br>
                <input type="text" id="username" name="username" placeholder="Enter Username"><br>
                <div id="usernameError" class="error"></div>
                <label>Password:</label><br>
                <input type="password" id="password" name="password" placeholder="Enter password"><br>
                <div id="passwordError" class="error"></div>
                <label class="rmbr" style="position: relative; top: 15px;">Remember Me</label>
                <input type="checkbox" id="rmbr" style="position: relative; top: 15px;"><br>
                <button class="submit" id="loginBtn" style="position: relative; top:15px;">Log In</button><br>
            </form>
                <p class="sulink" style="position: relative; top:25px;">Forgot password? Click <a href="forgotpass.php" id="sulink">"here"</a> to change!!</p>
            </div>
        </div>
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;"> 
    </div>
</body>
<script src="js/login.js" type="module"></script>
</html>