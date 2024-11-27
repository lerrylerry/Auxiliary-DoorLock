<?php
require('dbcred/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Initialize message and modal type variables
$message = ""; 
$modalType = ""; 

// Handle add item to pending borrow list
if (isset($_POST['additem'])) {
    $itemid = $_POST['additem'];
    $userid = $_GET['userid'];
    $borrowqty = $_POST['qty'];

    // Check if the item has already been borrowed
    $sqlcheck = "SELECT * FROM `tbpendingborrow` 
                 WHERE `itemid` = '$itemid' 
                 AND `userid` = '$userid' 
                 AND `transid` = '0'";  // Check only unprocessed (transid = 0) requests

    $result = mysqli_query($db, $sqlcheck);

    if (mysqli_num_rows($result) > 0) {
        // Item already borrowed, don't insert
        $message = "This item has already been added to your borrow request. Please delete the first entry and try again. Thank You!";
        $modalType = "error"; // Set modal type to error
    } else {
        // Item not borrowed, proceed with insert
        $sqlinsertp = "INSERT INTO `tbpendingborrow`(`itemid`, `userid`, `borrowqty`) 
                       VALUES ('$itemid', '$userid', '$borrowqty')";
        if (mysqli_query($db, $sqlinsertp)) {
            // $message = "Item added to borrow request.";
            // $modalType = "success"; // Set modal type to success
        } else {
            $message = "Error adding item to borrow request.";
            $modalType = "error"; // Set modal type to error
        }
    }
}

// Handle finalizing the borrow request
if (isset($_POST['finalizerequest'])) {
    // Insert the borrow request into tbborrow table
    $sqlsubmitrequest = "INSERT INTO `tbborrow`(`userid`, `status`) 
                         VALUES ('" . $_POST['finalizerequest'] . "','Pending')";
    mysqli_query($db, $sqlsubmitrequest);

    // Get the transaction ID of the borrow request
    $transid = mysqli_insert_id($db);

    // Update pending borrow items with the transaction ID
    $sqlupdatetransid = "UPDATE `tbpendingborrow` 
                         SET `transid` = '$transid' 
                         WHERE userid = '" . $_POST['finalizerequest'] . "' 
                         AND transid = '0'";
    mysqli_query($db, $sqlupdatetransid);

    // Fetch user details (name and email) for sending the email
    $sqlgetcu = "SELECT id, name, email FROM `tbup` WHERE id = '" . $_POST['finalizerequest'] . "';";
    $listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

    // Fetch the list of items the user has borrowed
    $sqlgetitems = "SELECT tbproductlist.name, tbpendingborrow.borrowqty 
                    FROM tbpendingborrow 
                    LEFT JOIN tbproductlist ON tbpendingborrow.itemid = tbproductlist.id 
                    WHERE tbpendingborrow.userid = '" . $_POST['finalizerequest'] . "' 
                    AND tbpendingborrow.transid = '$transid'";
    $itemsresult = mysqli_query($db, $sqlgetitems);

    // Prepare user email content
    $emailContent = "Dear " . $listcu['name'] . ",\n\n";
    $emailContent .= "Your borrow request has been processed. Below are the details of the items you have requested:\n\n";

    while ($item = mysqli_fetch_assoc($itemsresult)) {
        $emailContent .= "Item: " . $item['name'] . " | Quantity: " . $item['borrowqty'] . "\n";
    }

    $emailContent .= "\nThank you for using our service.\n\nBest regards,\nTUP Auxiliary System";

    // Send email to the user
    try {
        $mail = new PHPMailer(true);
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';
        $mail->Password = 'vdbwgupzfybcixsk'; // Use an App password for security
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($listcu['email'], $listcu['name']);

        // Email content
        $mail->isHTML(false);
        $mail->Subject = 'Borrow Request Confirmation';
        $mail->Body = $emailContent;

        // Send email
        $mail->send();

        // Set success message for the user
        $message = "Your borrow request has been successfully submitted!";
        $modalType = "success"; // Set modal type to success
    } catch (Exception $e) {
        $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $modalType = "error"; // Set modal type to error
    }

    // Fetch admin's email (assuming the admin's id is 1)
    $sqlgetAdminEmail = "SELECT email FROM tbadmin WHERE id = 1";
    $adminResult = mysqli_query($db, $sqlgetAdminEmail);
    $adminEmailData = mysqli_fetch_assoc($adminResult);
    $adminEmail = $adminEmailData['email'];

    // Prepare the admin's email content
    $adminEmailContent = "Dear Admin,\n\n";
    $adminEmailContent .= "You have received a new borrow request. Please review the request below:\n\n";
    $adminEmailContent .= "User: " . $listcu['name'] . "\n";
    $adminEmailContent .= "User Email: " . $listcu['email'] . "\n";
    $adminEmailContent .= "Borrow Items:\n";

    // Reset itemsresult query to get borrow items again
    $itemsresult = mysqli_query($db, $sqlgetitems);

    while ($item = mysqli_fetch_assoc($itemsresult)) {
        $adminEmailContent .= "Item: " . $item['name'] . " | Quantity: " . $item['borrowqty'] . "\n";
    }

    $adminEmailContent .= "\nTo review the borrow request, please visit the following link:\n";
    $adminEmailContent .= "http://127.0.0.1/Auxiliary-DoorLock/index.php\n\n";
    $adminEmailContent .= "Thank you,\nTUP Auxiliary System";

    // Send email to admin
    try {
        $mail->clearAddresses();
        $mail->addAddress($adminEmail, 'Admin');
        $mail->Subject = 'New Borrow Request Submitted';
        $mail->Body = $adminEmailContent;

        // Send email
        $mail->send();
    } catch (Exception $e) {
        $message = "Message could not be sent to admin. Mailer Error: {$mail->ErrorInfo}";
        $modalType = "error"; // Set modal type to error
    }

    // Redirect after showing the modal
    header("Location: borrowANDreturn.php");
    exit();
}

// Handle item deletion from pending borrow list
if (isset($_POST['delete'])) {
    $sqldeleteitem = "DELETE FROM `tbpendingborrow` WHERE id='" . $_POST['delete'] . "'";
    mysqli_query($db, $sqldeleteitem);
}

// Fetch list of pending borrow items for the user
$sqlgetitems = "SELECT tbproductlist.*, tbpendingborrow.* 
                FROM tbpendingborrow 
                LEFT JOIN tbproductlist ON tbpendingborrow.itemid = tbproductlist.id 
                WHERE userid = '" . $_GET['userid'] . "' 
                AND transid = '0';";
$listresult = mysqli_query($db, $sqlgetitems);

// Fetch user details
$sqlgetcu = "SELECT id, name, pincode, status, email FROM `tbup` WHERE id ='" . $_GET['userid'] . "';";
$listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

// Fetch available products to borrow
$sqlgetp = "SELECT id, unit, name, quantity, category 
            FROM `tbproductlist` 
            WHERE quantity > 0 
            ORDER BY name ASC;";
$listp = mysqli_query($db, $sqlgetp);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <!-- <link rel="stylesheet" href="css/borrow-items.css"> -->
    
    <title>Auxiliary | Borrow/Request</title>

    <style>
/* Global Styles */
body {
    background-color: #ed8383;
    background-image: url('images/LOCKER.jpg');
    background-size: cover;
    background-position: center;
    font-family: Arial, sans-serif;
    color: #fff;
}

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

/* Form Styles */
.card {
    background-color: #fff; /* White background for the form */
    color: #000; /* Black text for the form */
    border-radius: 15px;
    box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2); /* Lighter shadow */
    padding: 25px;
    max-width: 32rem;
    margin: 20px auto;
    position: relative; /* Add position relative for form element adjustments */
    overflow: hidden; /* Prevents overflow of content */
}

.card-body {
    padding: 20px;
}

/* Form Labels */
.borrow-label, .return-label {
    font-family: 'Montserrat', sans-serif;
    font-size: 1.4rem;
    color: #000; /* Black text */
    text-align: left;
    padding-bottom: 0.8rem;
}

label {
    font-size: 1rem;
    font-weight: bold;
    color: #000; /* Black text */
}


/* Focus effect on inputs */
select:focus, input:focus {
    outline: none;
    border-color: #9e1b32; /* Cardinal Red on focus */
}

/* Hover effect for inputs */
select:hover, input:hover {
    background-color: #e8e8e8; /* Slightly darker background on hover */
}

/* Pincode Input Specific Styling (ensure same size for password/text inputs) */
input[type="password"], input[type="text"], input[type="number"] {
    font-size: 1rem; /* Set font size for consistency */
}

/* Ensure no conflict with form control styling */
input.form-control {
    width: 100%;
    font-size: 1rem;
    margin-bottom: 15px;
}

/* Submit Buttons */
.borrow-submit, .return-submit {
    background-color: #9e1b32; /* Cardinal Red */
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 1.2rem;
    transition: background-color 0.3s ease, transform 0.3s ease;
    width: 100%;
    text-align: center;
}

.borrow-submit:hover, .return-submit:hover {
    background-color: #7a1623; /* Darker red on hover */
    transform: scale(1.05);
}

/* Footer */
footer {
    display: flex; /* Flexbox layout */
    justify-content: center; /* Center the content horizontally */
    align-items: center; /* Center the content vertically */
    padding: 10px 20px; /* Add padding for spacing */
    background-color: #343a40; /* Dark background for contrast */
    color: #ffffff; /* White text for readability */
    font-size: 16px; /* Adjust font size for readability */
    flex-wrap: wrap; /* Allow wrapping on smaller screens */
}

footer p {
    margin: 0; 
    text-align: center;
}

@media (max-width: 768px) {
    footer {
        font-size: 12px; 
        padding: 8px 15px; 
    }
}

@media (max-width: 480px) {
    footer {
        font-size: 10px; /* Further reduce font size for very small screens */
    }
}

/* Modal Styling */
.modal-content {
    border-radius: 12px;
    color: white;
}

.modal-header {
    border-bottom: 1px solid #9e1b32;
}

.modal-title {
    color: #fff;
    font-size: 1.5rem;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    border-top: 1px solid #9e1b32;
}

button.btn-secondary {
    background-color: #555;
    border-color: #555;
}

/* Responsive Styling */
@media (max-width: 767px) {
    .card {
        width: 90%;
        padding: 20px;
    }
    .borrow-submit, .return-submit {
        padding: 10px;
        font-size: 1rem;
    }
}

/* Hover Effects */
.card:hover {
    box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.9);
}

/* Navbar Hover Effect */
.navbar .navbar-nav .nav-link:hover {
    color: #d8d8d8;
}

/* Mobile View Adjustments */
@media (max-width: 767px) {
    .navbar .navbar-nav {
        text-align: center;
    }

    .card {
        width: 90%;
        padding: 15px;
    }

    .borrow-submit, .return-submit {
        padding: 10px;
        font-size: 1rem;
    }
}

/* Additional Specific Styling for Borrow Request Form */
.card-body .row {
    margin-bottom: 15px;
}

/* Table Styles */
.table th, .table td {
    text-align: center;
    padding: 12px 8px;
}

/* Make Sure the Form Add Button and Table don't Overflow the Form */
.borrow-table {
    max-height: 200px; /* Limit height */
    overflow-y: auto; /* Allow scroll if content exceeds */
}

.borrow-container {
    max-width: 30rem;
    margin: auto;
}

/* Modify the form button and input spacing to ensure proper alignment inside the form box */
.submitBtn, .borrow-submit {
    margin-top: 20px;
}

/* Add some space between the form elements */
.card .row input, .card .row select {
    margin-bottom: 15px;
}

/* Adjust the Add Item Form Layout */
form .row .col-4 {
    margin-bottom: 15px;
}

/* Error message styling */
.error {
    color: red;
    font-size: 1rem;
}

/* Modal for Error Messages */
.modal-content {
    border-radius: 12px;
    color: white;
}

.modal-header {
    border-bottom: 1px solid #9e1b32;
}

.modal-title {
    color: #fff;
    font-size: 1.5rem;
}

.modal-body {
    padding: 20px;
}

button.btn-secondary {
    background-color: #555;
    border-color: #555;
}
.small-label {
    font-size: 0.85rem; /* Smaller text size for labels */
    color: #555; /* Slightly darker color for better readability */
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

<!-- Borrow Items Form -->
<div class="card borrow-container mx-auto mt-5 mb-5" style="max-width: 90%; width: 30rem; border: 3px solid #0e0e0f;">
    <h3 class="card-title borrow-label text-center mb-4">REQUEST ITEMS (BORROW)</h3>
    <hr>
    <div class="card-body borrow-content">
        <!-- User Info Section -->
        <div class="row mb-3">
            <div class="col-12">
                <label for="name" class="borrow-label small-label">Name:</label>
                <input type="text" id="name" name="upname" class="form-control" disabled value="<?php echo $listcu['name'] ?>">
            </div>
        </div>
        <hr>
        <!-- Borrow Table Section -->
        <div class="borrow-table table-responsive mx-auto mb-4">
            <table class="table table-bordered text-center" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Receive</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data = mysqli_fetch_assoc($listresult)) { ?>
                        <tr>
                            <td><?php echo $data['name'] ?></td>
                            <td><?php echo $data['quantity'] ?><span class="text-danger"> (-<?php echo $data['borrowqty'] ?>) </span></td>
                            <td><?php echo $data['unit'] ?></td>
                            <td>
                                <form action="" method="POST">
                                    <input type="hidden" value="<?php echo $data['id']; ?>" name="delete">
                                    <button type="submit" class="btn btn-danger mb-1">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Add Item Section -->
        <form method="post" action="" class="mt-4">
            <!-- Row for Items and Quantity -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="unitDropdown" class="borrow-label small-label">Items:</label>
                    <select id="unitDropdown" name="additem" class="form-select" required>
                        <option value="">- Select -</option>
                        <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
                            <option value="<?php echo $data['id'] ?>" data-quantity="<?php echo $data['quantity'] ?>">
                                <?php echo $data['name'] ?> | <?php echo $data['unit'] ?>: <?php echo $data['quantity'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="quantity" class="borrow-label small-label">Quantity:</label>
                    <input class="form-control" type="number" id="quantity" name="qty" placeholder="Enter quantity" required>
                </div>
            </div>

            <!-- Add Button below Quantity -->
            <div class="row">
                <div class="col-md-6 offset-md-6 d-flex justify-content-end">
                    <button type="submit" id="add" class="btn btn-primary w-50">Add</button>
                </div>
            </div>
        </form>



        <!-- Submit Button -->
        <div class="submitBtn mx-auto mt-4">
            <form method="post" action="">
                <input type="hidden" class="form-control" value="<?php echo $_GET['userid']; ?>" name="finalizerequest" required>
                <button type="submit" class="borrow-submit w-100">Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 Auxiliary System. All rights reserved.</p>
</footer>

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="messageModalLabel">Error Message:</h5>
            </div>
            <div class="modal-body text-dark" id="modalMessage">
                <!-- The message will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- <script src="js/borrow-items.js" type="module"></script> -->
<script>
    $(document).ready(function() {
    $('#unitDropdown').change(function() {
        var selectedOption = $(this).find('option:selected');
        var quantity = selectedOption.data('quantity');
        $('#quantity').attr('placeholder', 'stock: ' + quantity).val(''); // Update placeholder
    });

    $('#quantity').on('input', function() {
        var placeholderText = $(this).attr('placeholder');
        var placeholderQuantity = parseInt(placeholderText.split(': ')[1], 10); // Extract quantity
        var inputVal = parseInt($(this).val(), 10); // Current input value

        if (inputVal > placeholderQuantity) {
            $(this).val(placeholderQuantity);
            // alert("You cannot enter a quantity higher than " + placeholderQuantity);
        }
    });
});

</script>

<script>
$(document).ready(function() {
    // Check if there's a message to show
    <?php if (!empty($message)) { ?>
        // Set the modal message content
        $('#modalMessage').text("<?php echo $message; ?>");
        
        // Show the modal
        $('#messageModal').modal('show');
    <?php } ?>
});
</script>

</body>
</html>
