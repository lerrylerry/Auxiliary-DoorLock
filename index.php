<?php
// index.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/all.css"> <!-- Link to your custom CSS -->

    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <title>Auxiliary | Home</title>

    <style>
/* General Body and Layout */
body {
    background-color: #ed8383; /* Light Red Fallback */
    background-image: url('images/LOCKER.jpg'); /* Replace with your image path */
    background-size: cover;
    background-position: center;
    font-family: Arial, sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    color: #fff;
    overflow-x: hidden;
}

/* Navbar Styles */
.navbar {
    background-color: #9e1b32; /* Cardinal Red */
    padding: 10px 20px;
    z-index: 1000;
}

.navbar .container-fluid {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navLogo {
    display: flex;
    align-items: center;
}

.navLogo img {
    width: 50px;
    height: 50px;
    border-radius: 50%; /* Circular logo */
    border: 3px solid #555; /* Gray border inside the circle */
}

.navLogo p {
    color: white;
    padding-left: 10px;
    font-size: 1.2rem;
}

.navbar-toggler {
    color: white;
    border: 1px solid #fff;
    background-color: #9e1b32; /* Same as navbar background */
}

.navbar-toggler-icon {
    background-color: transparent; /* Transparent background */
    border: none;
}

.navbar-toggler-icon::before, 
.navbar-toggler-icon::after {
    background-color: white; /* Make the bars white */
}

.navbar-toggler-icon span {
    background-color: white; /* Make the bars white */
}

/* Dropdown Button Style */
.navdiv .dropdown .btn {
    color: white;
    background-color: #9e1b32; /* Cardinal Red */
    border: 1px solid #fff;
    font-size: 1rem;
    transition: background-color 0.3s ease;
}

.navdiv .dropdown .btn:hover {
    background-color: #7a1623; /* Darker red for hover */
    color: white;
}

.navdiv .dropdown .btn:focus {
    background-color: #7a1623; /* Darker red on focus/active */
    color: white;
    box-shadow: none; /* Remove the default blue focus shadow */
}

/* Dropdown Menu */
.dropdown-menu {
    background-color: #9e1b32; /* Same red as button */
    border: 1px solid #7a1623; /* Darker border to match hover effect */
}

.dropdown-item {
    color: white; /* Text color inside dropdown */
    background-color: #9e1b32; /* Same red as button */
    transition: background-color 0.3s ease;
}

.dropdown-item:hover {
    background-color: #7a1623; /* Darker red on hover */
    color: white;
}

/* Mobile Navbar Adjustment */
@media (max-width: 767px) {
    .navbar .container-fluid {
        flex-direction: row;
        align-items: center;
    }

    .navLogo {
        flex-grow: 1;
        display: flex;
        align-items: center;
    }

    .navbar-toggler {
        margin-left: 10px;
    }

    .navdiv {
        display: none;
        flex-direction: column;
    }

    .collapse.show .navdiv {
        display: flex;
    }

    .divLogin {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .divLogin .loginBtn button {
        width: 100%;
    }
}

/* Footer Styles */
footer {
    background-color: #2d2d2d;
    color: white;
    text-align: center;
    padding: 10px 0; /* Reduced padding */
    margin-top: auto;
}

footer p {
    font-size: 0.9rem; /* Smaller text size */
    margin: 0; /* Removes any default margin */
}

footer a {
    color: #ed8383;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}

/* Container Styles */
.content {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 80px 20px; /* Increase padding for better spacing */
    text-align: center;
    flex-grow: 1;
    margin: 0 20px;
    background: rgba(0, 0, 0, 0.7); /* Darker semi-transparent background for better contrast */
    border-radius: 15px; /* Rounded corners for a more modern look */
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.5); /* Soft shadow around the content box */
}

.container {
    max-width: 1200px;
    width: 100%;
    padding: 20px;
}

/* Heading Styles */
h2 {
    font-size: 3rem; /* Increase font size for more emphasis */
    color: #fff;
    text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.7), 0 0 25px #ff0000, 0 0 5px #ff0000; /* More dramatic shadow */
    margin-bottom: 20px;
    letter-spacing: 2px; /* Add letter-spacing for a more elegant look */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern sans-serif font */
    font-weight: bold;
}

/* Subheading Styles (Tech Uni Text) */
p {
    font-size: 1.4rem; /* Slightly larger font for readability */
    color: #fff;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.6); /* Soft shadow to make it pop */
    margin-top: 0;
    font-family: 'Arial', sans-serif;
}

/* Responsive text for small screens */
@media (max-width: 576px) {
    h2 {
        font-size: 2.2rem; /* Smaller h2 size on small screens */
    }

    p {
        font-size: 1.1rem; /* Slightly smaller p size on small screens */
    }

    .content {
        margin: 0 10px;
        padding: 60px 10px; /* Less padding on small screens */
    }
}
    </style>
</head>
<body>

<!-- navbar.php -->
<nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
        <!-- Logo Section -->
        <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="Logo">
            <p class="me-auto mb-2 mb-lg-0">Auxiliary System</p>
        </div>

        <!-- Navbar Toggler (Hamburger Icon for Small Screens) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navdiv d-flex justify-content-center w-100">
                <!-- Dropdown for Services -->
                <div class="dropdown mx-auto">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Services</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="borrowANDreturn.php">Borrow/Get Items</a></li>
                        <li><a class="dropdown-item" href="minor-repair.php">Request a minor repair</a></li>
                    </ul>
                </div>

                <!-- Login Button -->
                <div class="divLogin d-flex justify-content-center">
                    <a href="login.php" class="loginBtn" style="text-decoration: none;">
                        <button class="btn btn-outline-light" type="button" id="login">Login</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Centered Content Section -->
<div class="content">
    <div class="container">
        <!-- Page Content -->
        <h2>Welcome to the Auxiliary System</h2>
        <p>TECHNOLOGICAL UNIVERSITY OF THE PHILIPPINES (CAVITE-CAMPUS)</p>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

</body>
</html>
