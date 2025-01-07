<?php
// index.php
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

    <title>Auxiliary | Home</title>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
        <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="Logo">
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

<div class="content">
    <div class="container">
        <h2>Welcome to the Auxiliary System</h2>
        <p class="school">TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES - CAVITE</p>
    </div>
</div>

<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
