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
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/borrow&return.css">

    <title>Auxiliary | Borrow/Request</title>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>

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