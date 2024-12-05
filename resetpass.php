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
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/index.css">
    
    <title>Auxiliary | Reset Password</title>

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
