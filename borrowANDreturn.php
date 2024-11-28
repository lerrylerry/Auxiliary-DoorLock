<?php
require('dbcred/db.php');
session_start(); // Start the session for error handling

$error = "";

// Handle the form submission for borrow
if (isset($_POST['loginupid'])) {
    $loginupid = $_POST['loginupid'];
    $pincodeborrow = $_POST['pincodeborrow'];

    // Validate the fields in PHP as well
    if (empty($loginupid) || empty($pincodeborrow)) {
        $_SESSION['status'] = 'error';
        $error = "Please fill in both fields.";
    } else {
        $sqlverifyuser = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='".$loginupid."';";
        $verify = mysqli_fetch_assoc(mysqli_query($db, $sqlverifyuser));
        if ($verify['pincode'] == $pincodeborrow) {
            header("location: borrow.php?userid=".$verify['id']);
            exit();
        } else {
            $error = "Incorrect pincode.";
        }
    }
}

// Handle the form submission for return
if (isset($_POST['returnid'])) {
    $returnid = $_POST['returnid'];
    $pincodereturn = $_POST['pincodereturn'];

    // Validate the fields in PHP as well
    if (empty($returnid) || empty($pincodereturn)) {
        $error = "Please fill in both fields.";
    } else {
        $sqlverifyuser = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='".$returnid."';";
        $verify = mysqli_fetch_assoc(mysqli_query($db, $sqlverifyuser));
        if ($verify['pincode'] == $pincodereturn) {
            header("location: return.php?userid=".$verify['id']);
            exit();
        } else {
            $error = "Incorrect pincode.";
        }
    }
}

// Fetch active users for dropdown lists
$sqlgetup = "SELECT id,name,pincode,status FROM `tbup` WHERE status ='active';";
$listup = mysqli_query($db, $sqlgetup);

$sqlgetupr = "SELECT id,name,pincode,status FROM `tbup` WHERE status ='active';";
$listupr = mysqli_query($db, $sqlgetupr);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!--CSS-->
    <!-- <link rel="stylesheet" href="css/borrow-items.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!--JS-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">

    <title>Auxiliary | Borrow/Request</title>

    <style>
/* Global Styles */
/* Ensure the html and body are at full height */
html, body {
    height: 100%; /* Ensure full viewport height */
    margin: 0; /* Remove default margin */
}

/* Body styles */
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* Ensures body takes full height of the screen */
    background-color: #dedede; 
    background-image: url('images/LOCKER.jpg');
    background-size: cover;
    background-position: center;
    font-family: Arial, sans-serif;
    color: #fff;
    padding: 0;
    position: relative; /* Ensure the body is a reference point for the pseudo-element */
}

/* Add a semi-transparent overlay to the body */
body::before {
    content: ''; /* Empty content for the overlay */
    position: fixed; /* Fixed position to cover the entire viewport */
    top: 0;
    left: 0;
    width: 100%;
    height: 100%; /* Cover the entire viewport height */
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

/* Modal Styles */
.optionModal {
    position: fixed; /* Use fixed positioning so it stays on top of the page */  
    width: 32rem;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.3);
    z-index: 9999;  /* Ensure it's on top of other content */
    background-color: rgba(0, 0, 0, 0.7); /* Add semi-transparent background */
    text-align: center;
    display: block;  /* Ensure the modal is visible on load */
}

.optionModal h2 {
    color: #333; /* Darker color for better visibility */
    font-size: 1.8rem;
    margin-bottom: 20px;
    text-align: center;
}

.optionModal .modal-content {
    background-color: #444;  /* Slightly lighter background for content */
    border-radius: 12px;
    padding: 20px;
}

.optionModal .row {
    margin-top: 30px;
}

.optionModal .btnForBorrow, .optionModal .btnForReturn {
    margin-top: 15px;
    text-align: center;
}

.optionModal .borrow-btn, .optionModal .return-btn {
    background-color: #9e1b32;
    color: #fff;
    font-size: 1.3rem;
    padding: 15px 25px;
    border-radius: 50px;
    width: 100%;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    border: none;
}

.optionModal .borrow-btn:hover, .optionModal .return-btn:hover {
    background-color: #7a1623;
    transform: scale(1.05);
}

/* Form Styles */
.card {
    background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white */
    color: #000;
    border-radius: 15px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
    padding: 30px;
    width: 30rem;
    margin: 10% auto;
    position: relative;
    z-index: 1; /* Ensure form is below modal */
}

.card h3 {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.7rem;
    color: #9e1b32;
    text-align: center;
}

.card .card-body {
    padding: 20px;
}

.card label {
    font-size: 1.1rem;
    font-weight: bold;
    color: #000;
    display: block;
    margin-bottom: 8px;
}

/* Form Styles */
.card select, .card input {
    width: 100%;
    padding: 12px;
    margin-bottom: 20px;
    font-size: 1rem;
    border-radius: 10px;
    border: 2px solid #ccc; /* Add a light grey border */
    background-color: #f9f9f9;
    color: #333;
    box-sizing: border-box; /* Ensure padding is included in the width */
}

.card select:focus, .card input:focus {
    border-color: #9e1b32; /* Cardinal red border color on focus */
    outline: none; /* Remove the default outline */
    box-shadow: 0 0 5px rgba(158, 27, 50, 0.5); /* Add a subtle shadow on focus */
}

/* Password field specifically */
.card input[type="password"] {
    font-size: 1rem;
    border: 2px solid #ccc; /* Add border for password field */
}

.card .submitBtn {
    text-align: center;
}

.card .submitBtn button {
    background-color: #9e1b32; /* Cardinal Red */
    color: white;
    padding: 12px 25px;
    border-radius: 50px;
    border: none;
    width: 100%;
    font-size: 1.3rem;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.card .submitBtn button:hover {
    background-color: #7a1623;
    transform: scale(1.05);
}

footer {
    margin-top: auto;  /* Push footer to the bottom */
    background-color: #2d2d2d;
    color: #fff;
    text-align: center;
    padding: 10px 0;
}

footer p {
    font-size: 1rem;
    margin: 0;
}

/* Error Styling */
.error {
    color: red;
    font-size: 1rem;
    margin-bottom: 15px;
}

/* Mobile Styling */
@media (max-width: 767px) {
    .navbar .navbar-nav {
        text-align: center;
    }

    .card {
        width: 90%;
        padding: 20px;
    }

    .card .submitBtn button {
        font-size: 1rem;
    }

    .optionModal {
        width: 90%;
        padding: 20px;
    }
}

/* Hover Effects */
.card:hover {
    box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.9);
}

.navbar .navbar-nav .nav-link:hover {
    color: #d8d8d8;
}

/* Error Message */
.alert-danger {
    background-color: #ffcccc;
    border-color: #ff0000;
    color: #ff0000;
    font-size: 1rem;
    padding: 10px;
    margin-top: 20px;
    text-align: center;
    border-radius: 5px;
}

/* Ensure the footer is hidden when the modal is visible */

    </style>
</head>
<body>
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

    <!-- Modal: This should be placed above the card -->
    <div class="card optionModal" id="optionModal">
        <h2 class="mx-auto mt-5">Purpose (Borrow or Return)</h2>
        <?php if ($error): ?>
            <div class="error text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="row mx-auto mt-5">
            <div class="col-6 btnForBorrow">
                <button class="borrow-btn" id="borrow-btn">Borrow</button>
            </div>
            <div class="col-6 btnForReturn">
                <button class="return-btn" id="return-btn">Return</button>
            </div>
        </div>
    </div>

    <!-- Borrow Card -->
    <div class="card borrow-container mx-auto mt-5 mb-5" style="height: 36rem; border: 3px solid #0e0e0f; display: none;">
        <h3 class="card-title borrow-label">REQUEST ITEMS (BORROW)</h3>
        <hr>
        <div class="card-body borrow-content">
            <div class="row">
                <div class="col-6">
                    <form method="post" action="">
                        <label>Name:</label><br>
                        <select id="unitDropdown" name="loginupid" class="form-select">
                            <option value="">- Name -</option>
                            <?php while ($data = mysqli_fetch_assoc($listup)) { ?>
                                <option value="<?php echo $data['id']?>"><?php echo $data['name']?></option>
                            <?php } ?>
                        </select>
                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input class="form-control" type="password" class="pinpin" id="pincode"  name="pincodeborrow" inputmode="numeric" min="0" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>

            <div class="submitBtn mx-auto">
                <button type="submit" class="borrow-submit mx-auto">Submit</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Return Card -->
    <div class="card return-container mx-auto mt-5 mb-5" style="height: 36rem; border: 3px solid #0e0e0f; display: none;">
        <h3 class="card-title return-label">REQUEST ITEMS (RETURN)</h3>
        <hr>
        <div class="card-body borrow-content">
            <form method="post" action="">
                <div class="row">
                    <div class="col-6">
                        <label>Name:</label><br>
                        <select id="unitDropdown" name="returnid" class="form-select" required>
                            <option value="">- Name -</option>
                            <?php while ($data = mysqli_fetch_assoc($listupr)) { ?>
                                <option value="<?php echo $data['id']?>"><?php echo $data['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Pincode:</label><br>
                        <input required class="form-control" type="password" id="pincode" inputmode="numeric" name="pincodereturn" maxlength="4" placeholder="Enter pincode">
                    </div>
                </div>

                <div class="submitBtn mx-auto">
                    <button type="submit" class="return-submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
    </footer>

    <script>
$(document).ready(function() {
    // Automatically show the modal when the page is loaded
    $('#optionModal').fadeIn(500); // This will make the modal appear with a fade-in effect
    $('body').addClass('modal-open'); // Add a class to body when modal is open

    // Handle Borrow button click
    $('#borrow-btn').on('click', function() {
        $('#optionModal').fadeOut(500); // Hide the modal with a fade-out effect
        $('.borrow-container').fadeIn(500); // Show the borrow card
        $('.return-container').fadeOut(500); // Hide the return card (if it was visible)
    });

    // Handle Return button click
    $('#return-btn').on('click', function() {
        $('#optionModal').fadeOut(500); // Hide the modal with a fade-out effect
        $('.return-container').fadeIn(500); // Show the return card
        $('.borrow-container').fadeOut(500); // Hide the borrow card (if it was visible)
    });

    // Handle Borrow form submission
    $('.borrow-submit').on('click', function(event) {
        var name = $('select[name="loginupid"]');
        var pincode = $('input[name="pincodeborrow"]');

        // Clear previous error message if any
        $('.alert-danger').remove();

        // Validate fields
        if (name.val() === '' || pincode.val() === '') {
            // Create and display error message
            var errorMessage = $('<div class="alert alert-danger" role="alert">Please fill in both fields.</div>');
            $('.borrow-label').after(errorMessage); // Append it below the label
            event.preventDefault(); // Prevent form submission
        }
    });

    // Handle Return form submission
    $('.return-submit').on('click', function(event) {
        var name = $('select[name="returnid"]');
        var pincode = $('input[name="pincodereturn"]');

        // Clear previous error message if any
        $('.alert-danger').remove();

        // Validate fields
        if (name.val() === '' || pincode.val() === '') {
            // Create and display error message
            var errorMessage = $('<div class="alert alert-danger" role="alert">Please fill in both fields.</div>');
            $('.return-label').after(errorMessage); // Append it below the label
            event.preventDefault(); // Prevent form submission
        }
    });

    // Handle modal close (if necessary)
    $('#close-modal').on('click', function() {
        $('#optionModal').fadeOut(500); // Hide the modal
        $('body').removeClass('modal-open'); // Remove modal-open class from body
        $('.borrow-container').fadeOut(500); // Hide the borrow form
        $('.return-container').fadeOut(500); // Hide the return form
    });
});

    </script>
</body>
</html>