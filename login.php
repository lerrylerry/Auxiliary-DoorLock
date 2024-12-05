<?php
require('dbcred/db.php');
session_start();

if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
    exit();
}

$error = ''; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $myusername = mysqli_real_escape_string($db, $_POST['username']);
    $mypassword = mysqli_real_escape_string($db, $_POST['password']);
    
    $login = "SELECT id, username, name, password, email, role FROM tbadmin WHERE username='$myusername'";
    $result = mysqli_query($db, $login);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $results = mysqli_fetch_assoc($result);
        if ($mypassword == $results['password']) {
            $_SESSION['user'] = $myusername;
            $_SESSION['name'] = $results['name'];
            $_SESSION['email'] = $results['email'];
            $_SESSION['loginid'] = $results['id'];
            $_SESSION['role'] = $results['role'];
            
            // Set success message and prepare for redirection
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Login successful! Redirecting...';
            
            // Determine destination based on role
            $redirect = ($results['role'] == "assistant") ? "assistant/homepage.php" : "admin/homepage.php";
            
            echo "<script>
                    setTimeout(function() {
                        window.location.href = '$redirect';
                    }, 2000); // Delay redirect by 2 seconds
                  </script>";
        } else {
            $error = 'Wrong password. Please try again.';
        }
    } else {
        $error = 'Username does not exist.';
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

    <title>Auxiliary | Login</title>
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
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Services</button>
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
        <div class="alert alert-<?php echo $_SESSION['status']; ?> alert-dismissible fade show text-center" role="alert">
            <?php
            echo $_SESSION['message'];
            unset($_SESSION['status']);
            unset($_SESSION['message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<!-- Login Form -->
<div class="card mx-auto">
    <div class="card-body">
        <h1 class="card-title">Log In</h1>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter Username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter password" required>
            </div>
            <div class="form-group submitBtn">
                <button type="submit">Log In</button>
            </div>
        </form>
        <p class="sulink">Forgot password? Click <a href="forgotpass.php">here</a> to reset it.</p>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
