<?php
require('dbcred/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
}

// if (isset($_POST['additem'])) {
//     $sqlinsertp = "INSERT INTO `tbpendingreturn`(`itemid`, `userid`,`borrowqty`) VALUES ('" . $_POST['additem'] . "','" . $_GET['userid'] . "','" . $_POST['qty'] . "')";
//     mysqli_query($db, $sqlinsertp);
// }

if (isset($_POST['additem'])) {
    // Assuming 'returningqty' is the column to update
    $itemid = $_POST['additem'];
    $userid = $_GET['userid'];
    $qty = $_POST['qty'];

    // Check if the item has already been borrowed
    $sqlcheck = "SELECT * FROM `tbpendingreturn` 
                 WHERE `itemid` = '$itemid' 
                 AND `userid` = '$userid' 
                 AND `returningqty` != '0'";  // Check only unprocessed (returningqty = 0) requests

    $result = mysqli_query($db, $sqlcheck);

    if (mysqli_num_rows($result) > 0) {
        // Item already borrowed, don't insert
        $message = "This item has already been added to your return request. Please delete the first entry and try again. Thank You!";
        $modalType = "error"; // Set modal type to error
    } else {
    // Update the returningqty field based on the itemid and userid
    $sqlupdatep = "UPDATE tbpendingreturn SET returningqty = returningqty + $qty WHERE itemid = '$itemid' AND userid = '$userid'";
        if (mysqli_query($db, $sqlupdatep)) {
            // $message = "Item added to borrow request.";
            // $modalType = "success"; // Set modal type to success
        } else {
            $message = "Error adding item to return request.";
            $modalType = "error"; // Set modal type to error
        }
    }
}

if (isset($_POST['finalizerequest'])) {
    // Insert a new request into tbreturn with status 'Pending'
    $sqlsubmitrequest = "INSERT INTO `tbreturn`(`userid`, `status`) VALUES ('" . $_POST['finalizerequest'] . "','Pending')";
    mysqli_query($db, $sqlsubmitrequest);

    // Get the transaction ID from the previous insert (this is used to link with tbpendingreturn)
    $transaction_id = mysqli_insert_id($db);

    // Update tbpendingreturn to link the transaction ID for items where returningqty != 0
    // Instead of setting transid = 0, we set transid to the new transaction ID
    $sqlupdatetransid = "UPDATE `tbpendingreturn` 
                         SET `transid` = '$transaction_id', `returningqty` = 0 
                         WHERE `userid` = '" . $_POST['finalizerequest'] . "' 
                         AND `returningqty` != 0";
    mysqli_query($db, $sqlupdatetransid);

    // Reset the door access to 'Rejected' for the user in tbup
    $resetaccess = "UPDATE `tbup` SET `dooraccess` = 'Rejected' WHERE id = '" . $_POST['finalizerequest'] . "'";
    mysqli_query($db, $resetaccess);

    // Step 1: Get the user details (name, email) for sending the email
    $sqlgetuser = "SELECT id, name, email FROM `tbup` WHERE id = '" . $_POST['finalizerequest'] . "';";
    $user = mysqli_fetch_assoc(mysqli_query($db, $sqlgetuser));

    // Step 2: Get the list of items the user is returning with their borrow quantity
    $sqlgetitems = "SELECT tbproductlist.name, tbpendingreturn.borrowqty 
                    FROM tbpendingreturn 
                    LEFT JOIN tbproductlist ON tbpendingreturn.itemid = tbproductlist.id 
                    WHERE tbpendingreturn.userid = '" . $_POST['finalizerequest'] . "' 
                    AND tbpendingreturn.transid = '$transaction_id';";
    $itemsresult = mysqli_query($db, $sqlgetitems);

    // Step 3: Prepare the email content
    $emailContent = "Dear " . $user['name'] . ",\n\n"; // Using $user here for name
    $emailContent .= "Your return request has been now processed";

    $emailContent .= "\nThank you for using our service.\n\nBest regards,\nTUP Auxiliary System";

    // Debugging: Output the email content to ensure it's correctly populated
    echo "<pre>" . htmlspecialchars($emailContent) . "</pre>";

    // Step 4: Send email notification using PHPMailer
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0;  // Disable debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your app password (use App Password if 2FA is enabled)
        $mail->SMTPSecure = 'tls';  // TLS encryption
        $mail->Port = 587;  // SMTP port for TLS

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($user['email'], $user['name']);  // Add recipient email dynamically

        // Email content
        $mail->isHTML(false);  // Plain text email
        $mail->Subject = 'Return Request Confirmation';
        $mail->Body    = $emailContent;

        // Send email
        $mail->send();

        // Set success message for the user
        $message = "Your return request has been successfully submitted!";
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
    $adminEmailContent .= "You have received a new return request to " . $user['name'];

    $adminEmailContent .= "\nTo review the borrow the request, please visit the following link:\n";
    $adminEmailContent .= "<p><a href='https://tupcauxiliary.com/Auxiliary/admin/return-items.php'>Click here to view the request.</a></p>";
    $adminEmailContent .= "Thank you,\nTUP Auxiliary System";

    // Reset itemsresult query to get return items again
    $itemsresult = mysqli_query($db, $sqlgetitems);

    while ($item = mysqli_fetch_assoc($itemsresult)) {
        $adminEmailContent .= "Item: " . $item['name'] . " | Quantity: " . $item['borrowqty'] . "\n";
    }

    // Send email to admin
    try {
        $mail->clearAddresses();
        $mail->addAddress($adminEmail, 'Admin');
        $mail->Subject = 'New Return Request Submitted';
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


if (isset($_POST['delete'])) {  
    $sqlupdateqty = "UPDATE `tbpendingreturn` SET returningqty = 0 WHERE id = '" . $_POST['delete'] . "'";
    mysqli_query($db, $sqlupdateqty);
}


$sqlgetitems = "SELECT tbproductlist.*,tbpendingreturn.* FROM tbpendingreturn LEFT JOIN tbproductlist ON tbpendingreturn.itemid = tbproductlist.id WHERE userid = '" . $_GET['userid'] . "' AND returningqty != 0;";
$listresult = mysqli_query($db, $sqlgetitems);

$sqlgetcu = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='" . $_GET['userid'] . "' AND pincode != '7777';";
$listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

// return------------------------------------------------------------------------------------------------------
$userId = filter_input(INPUT_GET, 'userid', FILTER_SANITIZE_NUMBER_INT);

$sqlgetp = "
    SELECT p.*, pl.unit, pl.*  -- Include pl.unit explicitly
    FROM tbpendingreturn p
    JOIN tbproductlist pl ON p.itemid = pl.id
    WHERE p.userid = '$userId' AND p.transid != 0
    ORDER BY pl.name ASC
";

$listp = mysqli_query($db, $sqlgetp);

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

<!-- Return Items Form -->
<div class="card borrow-container mx-auto mt-5 mb-5" style="border: 3px solid #0e0e0f;">
    <h3 class="card-title borrow-label text-center mb-4">REQUEST ITEMS (RETURN)</h3>
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
        <!-- Return Table Section -->
        <div class="borrow-table mx-auto mb-4">
            <table class="table table-bordered text-center" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Return</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($data = mysqli_fetch_assoc($listresult)) { ?>
                        <tr>
                            <td><?php echo $data['name'] ?></td>
                            <td><span class="text-success"><?php echo $data['returningqty'] ?></span></td>
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
            <div class="row mb-3">
                <div class="col-4">
                    <label for="unitDropdown" class="borrow-label small-label">Items:</label>
                    <select id="unitDropdown" name="additem" class="form-select" required>
                        <option value="">- Select -</option>
                        <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
                            <option value="<?php echo $data['id'] ?>" data-quantity="<?php echo $data['quantity'] ?>"><?php echo $data['name'] ?> | <?php echo $data['unit'] ?>: <?php echo $data['borrowqty'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-4">
                    <label for="quantity" class="borrow-label small-label">Quantity:</label>
                    <input class="form-control" type="number" id="quantity" name="qty" placeholder="Enter quantity" required>
                </div>
                <div class="col-4">
                    <br>
                    <button type="submit" id="add" class="btn btn-danger">Add</button>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
<!-- <script src="js/borrow-items.js" type="module"></script> -->

<script>
    $(document).ready(function() {
    $('#unitDropdown').change(function() {
        var selectedOption = $(this).find('option:selected');
        var quantity = selectedOption.data('quantity');
        $('#quantity').attr('placeholder', 'borrowed: ' + quantity).val(''); // Update placeholder
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
