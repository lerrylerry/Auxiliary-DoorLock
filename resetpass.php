<?php
require('dbcred/db.php');
session_start();


$error = "";
// Get the token from the URL (this should be the token passed in the password reset link)
$token = isset($_GET['token']) ? mysqli_real_escape_string($db, $_GET['token']) : '';

// Check if the token exists in the database and is not expired or already used
if ($token) {
    // Query to check if the token exists, is not used, and hasn't expired
    $query = "SELECT * FROM tbadmin WHERE token='$token' AND token_used=FALSE";
    $result = mysqli_query($db, $query);
    
    if (mysqli_num_rows($result) == 1) {
        // Token is valid, allow the user to reset their password
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = mysqli_real_escape_string($db, $_POST['password']);
            $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

            // Check if passwords match
            if ($new_password == $confirm_password) {
                // Don't hash the password, store it as plain text (not recommended)
                
                // Update the password and mark the token as used
                $update_query = "UPDATE tbadmin SET password='$new_password', token_used=TRUE WHERE token='$token'";
                if (mysqli_query($db, $update_query)) {
                    // Password updated successfully, redirect to success page
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = 'Your password has been successfully reset!';
                    header("Location: 200.php"); // Redirect to 200.php on success
                    exit();
                } else {
                    // Error updating password
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Error resetting password.';
                    header("Location: 404.php"); // Redirect to 404.php on error
                    exit();
                }
            } else {
                // Passwords don't match
                $error = 'Passwords do not match.';
            }
        }
    } else {
        // Invalid or expired token
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Invalid or expired token. Please request a new password reset link.';
        header("Location: 404.php"); // Redirect to 404.php on error
        exit();
    }
} else {
    // No token provided in URL
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'No token provided.';
    header("Location: 404.php"); // Redirect to 404.php on error
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Reset Password</title>
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

        /* Styling for alert messages */
        .alert {
            padding: 10px;
            font-size: 1.1rem;
            margin: 10px 0;
        }
        .alert-success {
            background-color: #28a745;
            color: white;
        }
        .alert-error {
            background-color: #dc3545;
            color: white;
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
        <div class="alert alert-<?php echo $_SESSION['status'] == 'success' ? 'success' : 'error'; ?> alert-dismissible fade show text-center" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['status']);
            unset($_SESSION['message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Reset Password Form -->
<div class="card mx-auto">
    <div class="card-body">
        <h1 class="card-title">Reset Your Password</h1>

        <?php if ($error): ?>
            <div class="error text-left"><?php echo $error; ?></div>
        <?php endif; ?>


        <form method="post" action="">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <div class="form-group submitBtn">
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
