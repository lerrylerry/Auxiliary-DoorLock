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
                    $mail->Body = 'Click <a href="https://tupcauxiliary.com/Auxiliary/resetpass.php?token='. $token . '">here</a> to reset your password. The link will be use once.';

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
    <style>
/* General Body and Layout */
    body {
        background-color: #dedede; /* Light grey background to match locker theme */
        background-image: url('images/LOCKER.jpg');
        background-size: cover;
        background-position: center;
        font-family: Arial, sans-serif;
        color: #fff;
        margin: 0;
        padding: 0;
        position: relative; /* Ensure the overlay is above the background image */
        min-height: 100vh; /* Make sure the body takes at least full height of the viewport */
        display: flex;
        flex-direction: column; /* Use flexbox layout */
    }

    /* Add a semi-transparent overlay to the body */
    body::before {
        content: ''; /* Empty content for the overlay */
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black overlay */
        z-index: -1; /* Place it behind the content */
    }

/* Navbar */
.navbar {
    background-color: #9e1b32; /* Cardinal Red */
}

.navbar .container-fluid {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar .navbar-toggler {
    border: 1px solid #fff;
    background-color: #9e1b32;
}

/* Navbar Dropdown Menu */
.navdiv .dropdown .btn {
    color: white;
    background-color: #9e1b32;
    border: 1px solid #fff;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.navdiv .dropdown .btn:hover {
    background-color: #7a1623;
    color: white;
}

.navdiv .dropdown .btn:focus {
    background-color: #7a1623;
    color: white;
    box-shadow: none;
}

.dropdown-menu {
    background-color: #9e1b32;
    border: 1px solid #7a1623;
}

.dropdown-item {
    color: white;
    background-color: #9e1b32;
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: #7a1623;
    color: white;
}

/* Navbar Logo */
.navLogo {
    display: flex;
    align-items: center;
}

.navLogo img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid #555;
}

.navLogo p {
    color: white;
    padding-left: 10px;
    font-size: 1.2rem;
}

.navbar .navbar-nav .nav-item {
    margin: 0 10px;
}

.navbar .navbar-nav .nav-link {
    color: white;
    font-size: 1.1rem;
    text-transform: uppercase;
}

.navbar .navbar-nav .nav-link:hover {
    color: #d8d8d8;
}

    /* Card and Form Styles */
    .card {
        background-color: rgba(255, 255, 255, 0.9); /* Light form card to stand out */
        border-radius: 8px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3); /* Darker shadow for contrast */
        padding: 30px;
        margin-top: 50px;
        width: 100%;
        max-width: 400px;
        margin-bottom: 30px;
        box-sizing: border-box;
    }

    .card-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #9e1b32;
        margin-bottom: 20px;
        text-align: center;
    }

    /* Input Styles */
    input, textarea {
        width: 100%;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 15px;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }

    input:focus, textarea:focus {
        border-color: #9e1b32;
        box-shadow: 0 0 10px rgba(158, 27, 50, 0.5);
        outline: none;
    }

    /* Label Styles */
    label {
        color: #333;
        font-size: 1rem;
        font-weight: 500;
    }

    /* Submit Button */
    .submitBtn button {
        width: 100%;
        padding: 12px;
        background-color: #9e1b32;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .submitBtn button:hover {
        background-color: #7a1623;
    }

    /* Forgot Password Link */
    .sulink {
        text-align: center;
        margin-top: 10px;
        color: #333;
    }

    .sulink a {
        color: #9e1b32;
        text-decoration: none;
    }

    .sulink a:hover {
        text-decoration: underline;
    }

    /* Remember Me Checkbox */
    .rmbr {
        display: inline-block;
        font-size: 0.9rem;
        margin-top: 10px;
        margin-right: 5px;
    }

    #rmbr {
        vertical-align: middle;
    }

    /* Error Message Styles */
    .error {
        color: red;
        font-size: 0.9rem;
        margin-top: 5px;
        text-align: center;
    }

    /* Background Logo */
    #bg-logo {
        position: absolute;
        bottom: 0;
        right: 0;
        opacity: 0.1;
        z-index: -1;
        width: 120px;
    }

    /* Footer Styles */
    footer {
        background-color: #2d2d2d;
        color: white;
        text-align: center;
        padding: 10px 0;
        margin-top: auto; /* Push footer to the bottom of the page */
    }

    footer p {
        font-size: 0.9rem;
        margin: 0;
    }

    footer a {
        color: #ed8383;
        text-decoration: none;
    }

    footer a:hover {
        text-decoration: underline;
    }
    </style>
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
                        <li><a class="dropdown-item" href="minor-repair.php">Request a minor repair</a></li>
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
