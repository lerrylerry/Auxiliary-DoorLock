<?php
require('dbcred/db.php');

// Check if already logged in
if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
}

// Insert data into database if form is submitted
if (isset($_POST['name'])) {
    $sqlinsert = "INSERT INTO `tbminorrepair`(`name`, `position`, `department`, `email`, `type`, `serial`, `brandmodel`, `propertyno`, `acqdate`, `acqcost`, `scope`, `endUser`) 
    VALUES ('" . $_POST['name'] . "','" . $_POST['position'] . "','" . $_POST['department'] . "','" . $_POST['email'] . "','" . $_POST['type'] . "','" . $_POST['serial'] . "','" . $_POST['model'] . "','" . $_POST['propertyno'] . "','" . $_POST['acqusitionDate'] . "','" . $_POST['acqusitionCost'] . "','" . $_POST['message'] . "','" . $_POST['name'] . "')";
    mysqli_query($db, $sqlinsert);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Repair</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

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


                .navbar {
            background-color: #9e1b32;
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
            border-radius: 50%;
            border: 3px solid #555;
        }

        .navLogo p {
            color: white;
            padding-left: 10px;
            font-size: 1.2rem;
        }

        .navbar-toggler {
            color: white;
            border: 1px solid #fff;
            background-color: #9e1b32;
        }

        .navbar-toggler-icon {
            background-color: transparent;
            border: none;
        }

        .navbar-toggler-icon::before, 
        .navbar-toggler-icon::after {
            background-color: white;
        }

        .navbar-toggler-icon span {
            background-color: white;
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


        .navbar .container-fluid {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Card Styles */
        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            width: 100%; /* Make sure the card takes up full width */
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #9e1b32;
        }

        .card hr {
            border-color: #9e1b32;
            margin: 10px 0;
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        input:focus, textarea:focus {
            border-color: #ed8383;
            box-shadow: 0 0 10px rgba(237, 131, 131, 0.5);
            outline: none;
        }

        .submitBtn button {
            width: 100%;
            padding: 12px;
            background-color: #9e1b32;
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
        }

        .submitBtn button:hover {
            background-color: #d77373;
        }

        .form-section {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        #bg-logo {
            position: absolute;
            bottom: 0;
            right: 0;
            opacity: 0.1;
            z-index: -1;
        }

        /* Adjust form fields for mobile */
        @media (max-width: 767px) {
            .card {
                padding: 15px;
                margin-top: 30px;
            }

            input, textarea {
                font-size: 0.9rem;
                padding: 10px;
            }

            .submitBtn button {
                font-size: 1rem;
            }

            label {
                font-size: 0.9rem;
            }
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
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Services</button>
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

<!-- Form for Requesting Minor Repair -->
<form action="" method="post">
    <div class="card container">
        <h4 class="card-title">REQUEST BY</h4>
        <hr>
        <div class="form-section">
            <label>Name:<span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" placeholder="Enter name" required>
        </div>
        <div class="form-section">
            <label>Position:<span class="text-danger">*</span></label>
            <input type="text" id="position" name="position" placeholder="Enter position" required>
        </div>
        <div class="form-section">
            <label>Department:<span class="text-danger">*</span></label>
            <input type="text" id="department" name="department" placeholder="Enter department" required>
        </div>
        <div class="form-section">
            <label>Email:<span class="text-danger">*</span></label>
            <input type="text" id="email" name="email" placeholder="Enter email" required>
        </div>

        <h4 class="card-title">DESCRIPTION OF PROPERTY</h4>
        <hr>
        <div class="form-section">
            <label>Type:<span class="text-danger">*</span></label>
            <input type="text" id="type" name="type" placeholder="Enter type" required>
        </div>
        <div class="form-section">
            <label>Brand Model:<span class="text-danger">*</span></label>
            <input type="text" id="model" name="model" placeholder="Enter brand model" required>
        </div>
        <div class="form-section">
            <label>Serial No. (Optional).:</label>
            <input type="text" id="serial" name="serial" placeholder="Enter serial">
        </div>
        <div class="form-section">
            <label>Property No. (Optional):</label>
            <input type="text" id="property" name="propertyno" placeholder="Enter property number">
        </div>
        <div class="form-section">
            <label>Acquisition Date (Optional):</label>
            <input type="date" id="acqusitionDate" name="acqusitionDate">
        </div>
        <div class="form-section">
            <label>Acquisition Cost (Optional):</label>
            <input type="text" id="acqusitionCost" name="acqusitionCost" placeholder="Enter acquisition cost">
        </div>

        <h4 class="card-title">DEFECTS AND COMPLAINTS</h4>
        <hr>
        <div class="form-section">
            <label>Nature and Scope of Works to be done:<span class="text-danger">*</span></label>
            <textarea id="message" name="message" placeholder="Explanation message" rows="" required></textarea>
        </div>

        <div class="submitBtn">
            <button type="submit">Submit</button>
        </div>
    </div>
</form>

<!-- Background Logo -->
<!-- <img src="images/TUP-LOGO-modified.png" id="bg-logo"> -->
 <!-- Footer -->
<footer class="footer">
    <div class="footer-bottom text-center">
        <p>&copy; 2024 Auxiliary System | All Rights Reserved</p>
    </div>
</footer>

<!-- Additional Styles for the Footer -->
<style>
    .footer {
        background-color: #2d2d2d;
        color: white;
    }

    .footer h5 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        color: #ed8383;
    }

    .footer ul {
        list-style-type: none;
        padding: 0;
    }

    .footer ul li {
        margin-bottom: 10px;
    }

    .footer ul li a {
        color: white;
        text-decoration: none;
    }

    .footer ul li a:hover {
        color: #ed8383;
    }

    .social-links a {
        color: white;
        font-size: 1.5rem;
        margin: 0 10px;
        text-decoration: none;
    }

    .social-links a:hover {
        color: #ed8383;
    }

    .footer-bottom {
        background-color: #222;
        padding: 10px 0;
        margin-top: 20px;
    }

    .footer-bottom p {
        margin: 0;
        font-size: 1rem;
    }

    /* Mobile Adjustments */
    @media (max-width: 767px) {
        .footer .container {
            text-align: center;
        }

        .footer .social-links {
            margin-top: 20px;
        }
    }
</style>


<!-- JavaScript Files -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
