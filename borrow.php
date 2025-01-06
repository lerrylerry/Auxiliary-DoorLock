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
    // Get the user ID from the POST data
    $userId = $_POST['finalizerequest'];

    // Check if there are any pending borrow items for the user in tbpendingborrow
    $sqlCheckPending = "SELECT COUNT(*) AS count FROM `tbpendingborrow` WHERE `userid` = '$userId' AND `transid` = '0'";
    $resultCheckPending = mysqli_query($db, $sqlCheckPending);
    $row = mysqli_fetch_assoc($resultCheckPending);

    // If there are no pending borrow items, do nothing and exit
    if ($row['count'] == 0) {
        $message = "No pending borrow items found for this user.";
        $modalType = "error"; // Set modal type to error
        header("Location: borrowANDreturn.php");
        exit();
    }

    // Insert the borrow request into tbborrow table
    $sqlsubmitrequest = "INSERT INTO `tbborrow`(`userid`, `status`) 
                         VALUES ('$userId', 'Pending')";
    $insertResult = mysqli_query($db, $sqlsubmitrequest);

    // Check if the borrow request was successfully inserted
    if ($insertResult) {
        // Get the transaction ID of the borrow request
        $transid = mysqli_insert_id($db);

        // Update pending borrow items with the transaction ID
        $sqlupdatetransid = "UPDATE `tbpendingborrow` 
                             SET `transid` = '$transid' 
                             WHERE `userid` = '$userId' 
                             AND `transid` = '0'";
        $updateResult = mysqli_query($db, $sqlupdatetransid);

        // Check if the transaction ID was successfully updated in tbpendingborrow
        if ($updateResult && mysqli_affected_rows($db) > 0) {
            // Fetch user details (name and email) for sending the email
            $sqlgetcu = "SELECT id, name, email FROM `tbup` WHERE id = '$userId' AND pincode != '7777';";
            $listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

            // Fetch the list of items the user has borrowed
            $sqlgetitems = "SELECT tbproductlist.name, tbpendingborrow.borrowqty 
                            FROM tbpendingborrow 
                            LEFT JOIN tbproductlist ON tbpendingborrow.itemid = tbproductlist.id 
                            WHERE tbpendingborrow.userid = '$userId' 
                            AND tbpendingborrow.transid = '$transid'";
            $itemsresult = mysqli_query($db, $sqlgetitems);

            // Check if the user actually borrowed items
            if (mysqli_num_rows($itemsresult) > 0) {
                // Prepare user email content
                $emailContent = "Dear " . $listcu['name'] . ",\n\n";
                $emailContent .= "Your borrow request has been processed successfully.";

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
                $adminEmailContent .= "You have received a new borrow request from " . $listcu['name'];

                // Reset itemsresult query to get borrow items again
                $itemsresult = mysqli_query($db, $sqlgetitems);

                $adminEmailContent .= "\nTo review the borrow request, please visit the following link:\n";
                $adminEmailContent .= "<p><a href='https://tupcauxiliary.com/Auxiliary/admin/borrowedItems.php'>Click here to view the request.</a></p>";
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
            } else {
                // Handle the case where no items were borrowed
                $message = "No items were borrowed. The transaction was not processed.";
                $modalType = "error"; // Set modal type to error
            }
        } else {
            // Handle failed transaction update (if needed)
            $message = "There was an error processing your borrow request.";
            $modalType = "error"; // Set modal type to error
        }
    } else {
        // Handle failed insert (if needed)
        $message = "There was an error inserting your borrow request.";
        $modalType = "error"; // Set modal type to error
    }

    // Redirect after showing the modal
    header("Location: borrow-success.php");
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

// Check if there are any results
$hasPendingItems = mysqli_num_rows($listresult) > 0 ? 'true' : 'false'; // 'true' if there are results, 'false' otherwise

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

<!-- Borrow Items Form -->
<div class="card borrow-container mx-auto mt-5 mb-5">
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
                                <form action="" method="POST" id="deleteit">
                                    <input type="hidden" value="<?php echo $data['id']; ?>" name="delete">
                                    <button type="submit" class="btn btn-danger mb-1" id="deleted">Delete</button>
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
                                <?php echo $data['name'] ?> | <?php echo $data['unit'] ?>
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
            <form method="post" action="" id="borrowForm">
                <input type="hidden" class="form-control" value="<?php echo $_GET['userid']; ?>" name="finalizerequest" required>
                <button type="submit" class="borrow-submit w-100" id="submitBtn">Submit</button>
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

<script>
$(document).ready(function() {
    $("#borrowForm").on("submit", function() {
        // Disable the submit button functionality
        $("#submitBtn").prop("disabled", true);

        // Add a class to change the appearance
        $("#submitBtn").addClass("disabled-button");

        // Optionally, change the button text
        $("#submitBtn").text("Processing..."); 

        // Disable the submit button functionality
        $("#deleted").prop("disabled", true);

        // Add a class to change the appearance
        $("#deleted").addClass("disabled-button");

        // Disable the submit button functionality
        $("#add").prop("disabled", true);

        // Add a class to change the appearance
        $("#add").addClass("disabled-button");
    });
});
</script>

<script>
    $(document).ready(function() {
        // Get the PHP result and convert it to boolean
        var hasPendingItems = <?php echo $hasPendingItems == 'true' ? 'true' : 'false'; ?>;
        
        // Enable or disable the button based on hasPendingItems value
        $('#submitBtn').prop('disabled', !hasPendingItems);
    });
</script>


<style>
/* This is the class that will visually disable the button */
.disabled-button {
    background-color: #cccccc; /* Change to a lighter color */
    color: #666666; /* Light text color */
    cursor: not-allowed; /* Change the cursor to indicate disabled */
}
</style>

</body>
</html>
