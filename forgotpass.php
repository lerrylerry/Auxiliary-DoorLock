<?php
require('dbcred/db.php');

if (isset($_SESSION['loginid'])) {
  header("location: admin/homepage.php");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $myemail = mysqli_real_escape_string($db, $_POST['email']);
    $login = "SELECT id,username,password,email,role FROM tbadmin WHERE username='" . $myusername . "'";
    $result = mysqli_query($db, $login);
    $count = @mysqli_num_rows($result);
    if ($count == 1) {
        while ($results = mysqli_fetch_array($result)) {
            if ($myemail == $results['email']) {
                $otp = rand(10000, 99999);
                $sqlupdater = "UPDATE `tbadmin` SET `otp`='" . $otp . "' WHERE username='" . $myusername . "' AND email = '" . $results['email'] . "'";
                mysqli_query($db, $sqlupdater);

                try {
                    $mail->SMTPDebug = false;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com;';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'projxacts12@gmail.com';
                    $mail->Password = 'vdbwgupzfybcixsk';
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
                    $mail->addAddress($myemail, $results['name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset';
                    $mail->Body = 'Password Reset Click here <br> ' . $_SERVER['SERVER_ADDR'] . '/newpass.php?email=' . $myemail . '&otp=' . $otp;
                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }


            } else {
                $error = "Invalid Login";
            }
        }
    } else {
        $error = "Invalid Login";
    }
}
?>

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


    <title>Auxiliary | Forgot Password</title>
</head>
<body>
    <nav class="navbar navbar-expand-md bg-dark" style="width: 100%;">
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
                  <!-- <li><a class="dropdown-item" value="vehicle-reservation.php">Request for vehicle</a></li> -->
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

    <div class="card login-container mx-auto mt-5 mb-5" style="width: 25rem; height: 33rem; border: 3px solid #0e0e0f;">

        <div class="card-body login-content" style="z-index: 1;">
            <h1 class="card-title login-label">FORGOT PASSWORD</h1>
            <form method="post" action="">
                <div class="login-form">
                    <label>Username:</label><br>
                    <input type="text" id="username" name="username" placeholder="Enter Username"><br>
                    <div id="usernameError" class="error"></div>
                    <label>Email:</label><br>
                    <input type="email" id="email" name="email" placeholder="Enter your email"><br>
                    <div id="passwordError" class="error"></div>
                    <label class="rmbr" style="position: relative; top: 15px;">-This will sent the link for creating new
                        password</label>
                    <button class="submit" id="loginBtn" style="position: relative; top:15px;">Continue</button>
                    <br>
            </form>
            <p class="sulink" style="position: relative; top:25px;">Didn't receive the email <a href="#" id="sulink">"resend"</a>
                here!!</p>
        </div>
    </div>
    <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;">
</body>
<script src="js/login.js" type="module"></script>
</html>