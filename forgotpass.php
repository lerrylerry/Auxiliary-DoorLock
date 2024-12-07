<?php
require('dbcred/db.php');
session_start();

// Redirect to homepage if user is already logged in
if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$mail = new PHPMailer(true);

$error = ''; // Initialize error message variable

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $myemail = mysqli_real_escape_string($db, $_POST['email']);
    
    // Query to check if the username exists in the database
    $login = "SELECT id, username, password, email, role FROM tbadmin WHERE username='$myusername'";
    $result = mysqli_query($db, $login);
    $count = mysqli_num_rows($result);
    
    // If the username exists, proceed to check email
    if ($count == 1) {
        // Fetch the user details
        while ($results = mysqli_fetch_array($result)) {
            if ($myemail == $results['email']) {
                // Generate a secure 32-character token
                $token = bin2hex(random_bytes(16)); // 32 characters long

                // Update the database with the token
                $sqlupdater = "UPDATE tbadmin SET token='$token', token_used=FALSE WHERE username='$myusername' AND email='$myemail'";
                mysqli_query($db, $sqlupdater);


                try {
                    // PHPMailer Configuration
                    $mail->SMTPDebug = false;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'projxacts12@gmail.com'; // Replace with your email
                    $mail->Password = 'vdbwgupzfybcixsk'; // Replace with your email password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
                    $mail->addAddress($myemail, $results['name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = 'Click <a href="https://tupcauxiliary.com/resetpass.php?token='. $token . '">here</a> to reset your password. The link will be use once.';

                    // Send email
                    $mail->send();

                    // Redirect with success message
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = 'A password reset link has been sent to your email address.';
                    header("Location: forgotpass.php");
                    exit();

                } catch (Exception $e) {
                    // Handle email sending error
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                    header("Location: forgotpass.php");
                    exit();
                }
            } else {
                // If email does not match, show error
                $error = "The email address does not match our records.";
            }
        }
    } else {
        // If username is not found, show error
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/index.css">
    
    <title>Auxiliary | Forgot Password</title>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
        <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="TUP Logo">
            <p class="me-auto mb-2 mb-lg-0">Auxiliary System</p>
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navdiv d-flex justify-content-center w-100">
                <div class="dropdown mx-auto">
                    <button class="btn dropdown-toggle text-white btn-outline-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">Services</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="borrowANDreturn.php">Borrow/Get Items</a></li>
                        <li><a class="dropdown-item" href="minorRepair.php">Request a minor repair</a></li>
                    </ul>
                </div>
                <div class="divLogin d-flex justify-content-center">
                    <a href="login.php" class="loginBtn" style="text-decoration: none;">
                        <button class="btn btn-outline-light" type="button" id="login">Login</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>


<!-- Display status messages -->
<?php if (isset($_SESSION['status'])): ?>
    <div class="status-message">
        <div class="alert alert-<?php echo $_SESSION['status']; ?> text-center alert-dismissible fade show" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['status']);
            unset($_SESSION['message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Forgot Password Form -->
<div class="card mx-auto">
    <div class="card-body">
        <h1 class="card-title">Forgot Password</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="form-group submitBtn">
                <button type="submit">Send Reset Link</button>
            </div>
        </form>

        <p class="sulink">Back to login? <a href="login.php">Click here</a></p>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
