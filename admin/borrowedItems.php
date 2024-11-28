<?php
require('../dbcred/db.php');
session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Function to send email notification
function sendEmailNotification($email, $name, $status, $itemDetails, $optionalMessage = '') {
    $subject = "Your Borrow Request Status";
    $body = "Dear $name,\n\nYour borrow request has been $status.\n\nDetails:\n$itemDetails\n\n";

    if (!empty($optionalMessage)) {
        $body .= "Message from Admin: $optionalMessage\n\n"; // Add the optional message if present
    }

    $body .= "Best regards,\nTUP Auxiliary System";

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your app password (use App Password if 2FA is enabled)
        $mail->SMTPSecure = 'tls';  // TLS encryption
        $mail->Port = 587;  // SMTP port for TLS

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($email, $name);  // Add recipient email dynamically

        // Email content
        $mail->isHTML(false);  // Plain text email
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['approveborrow'])) {
    // Update the borrow request status to Approved
    $sqlupdateb = "UPDATE `tbborrow` SET `status`='Approved' WHERE id='" . $_POST['approveborrow'] . "'";
    if (!mysqli_query($db, $sqlupdateb)) {
        echo "Error updating borrow status: " . mysqli_error($db);
        exit;
    }

    $sqlupdatedooraccess = "UPDATE `tbup` SET `dooraccess`='Approved' WHERE id='" . $_POST['userid'] . "'";
    if (!mysqli_query($db, $sqlupdatedooraccess)) {
        echo "Error updating door access: " . mysqli_error($db);
        exit;
    }

    // Fetch items for the borrow request
    $sqlgetitems = "
        SELECT 
            tbborrow.id AS borrow_id, 
            tbpendingborrow.itemid, 
            tbpendingborrow.borrowqty, 
            tbpendingborrow.transid 
        FROM 
            tbborrow 
        INNER JOIN 
            tbpendingborrow ON tbpendingborrow.transid = tbborrow.id 
        WHERE 
            tbpendingborrow.userid = '" . $_POST['userid'] . "' 
            AND tbborrow.id = '" . $_POST['approveborrow'] . "'
    ";

    $listitems = mysqli_query($db, $sqlgetitems);

    $itemDetails = ''; // To hold the item details for the email

    while ($itelis = mysqli_fetch_assoc($listitems)) {
        // Fetch the item name, category, and quantity from tbproductlist
        $sqlCategory = "SELECT name, category, quantity FROM tbproductlist WHERE id='" . $itelis['itemid'] . "'";
        $resultCategory = mysqli_query($db, $sqlCategory);

        if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
            $rowCategory = mysqli_fetch_assoc($resultCategory);

            // Check if there is enough stock
            if ($rowCategory['quantity'] >= $itelis['borrowqty']) {
                // Deduct stock from tbproductlist
                $sqlupdatestock = "UPDATE `tbproductlist` SET `quantity`=`quantity` - " . $itelis['borrowqty'] . " WHERE id='" . $itelis['itemid'] . "'";
                mysqli_query($db, $sqlupdatestock);

                // If the category is 'Returnables', insert into tbpendingreturn
                if ($rowCategory['category'] === 'Returnables') {
                    $sqlinsertreturn = "
                        INSERT INTO tbpendingreturn (userid, itemid, borrowqty, transid) 
                        VALUES (
                            '" . $_POST['userid'] . "', 
                            '" . $itelis['itemid'] . "', 
                            '" . $itelis['borrowqty'] . "', 
                            '" . $itelis['transid'] . "'
                        )
                    ";
                    mysqli_query($db, $sqlinsertreturn);
                }

                // Append item details to the email content
                $itemDetails .= "Item: " . $rowCategory['name'] . "\n";
                $itemDetails .= "Quantity: " . $itelis['borrowqty'] . "\n";
                $itemDetails .= "Category: " . $rowCategory['category'] . "\n\n";
            } else {
                // Not enough stock, show error message
                echo "<script>alert('Not enough stock for " . $rowCategory['name'] . ". Please check the stock availability.'); window.location = '" . $_SERVER['PHP_SELF'] . "';</script>";
                exit;
            }
        }
    }

    // Get user's email and name for email notification
    $sqlgetuser = "SELECT email, name FROM tbup WHERE id = '" . $_POST['userid'] . "';";
    $user = mysqli_fetch_assoc(mysqli_query($db, $sqlgetuser));

    // Get the optional approval message from the form
    $optionalMessage = isset($_POST['approvalMessage']) ? $_POST['approvalMessage'] : '';

    // Send email notification with item details
    $emailStatus = sendEmailNotification($user['email'], $user['name'], 'Approved', $itemDetails, $optionalMessage);
    if ($emailStatus) {
        $statusMessage = 'The borrow request has been approved and the email sent successfully.';
        $redirectUrl = $_SERVER['PHP_SELF'];
    } else {
        $statusMessage = 'Failed to send email! Please try again.';
        $redirectUrl = $_SERVER['PHP_SELF'];
    }
}

if (isset($_POST['rejectborrow'])) {
    // Update the borrow request status to Rejected
    $sqlupdater = "UPDATE `tbborrow` SET `status`='Rejected' WHERE id='" . $_POST['rejectborrow'] . "'";
    if (!mysqli_query($db, $sqlupdater)) {
        echo "Error updating borrow status: " . mysqli_error($db);
        exit;
    }

    // Get user's email and name for email notification
    $sqlgetuser = "SELECT email, name FROM tbup WHERE id = '" . $_POST['userid'] . "';";
    $result = mysqli_query($db, $sqlgetuser);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo "Error fetching user details.";
        exit;
    }

    $user = mysqli_fetch_assoc($result);

    // Since there's no item detail for rejection, just send the status in the email body
    $itemDetails = ''; // No items in rejection, leave this empty

    // Get the optional rejection message from the form
    $optionalMessage = isset($_POST['rejectionMessage']) ? $_POST['rejectionMessage'] : '';

    // Send email notification for rejection
    $emailStatus = sendEmailNotification($user['email'], $user['name'], 'Rejected', $itemDetails, $optionalMessage);
    if ($emailStatus) {
        $statusMessage = 'The borrow request has been rejected and the email sent successfully.';
        $redirectUrl = $_SERVER['PHP_SELF'];  // You may need to set this after rejection
    } else {
        $statusMessage = 'Failed to send email! Please try again.';
        $redirectUrl = $_SERVER['PHP_SELF'];
    }
}

$sqlgetsimplifiedlist = "SELECT tbborrow.id as mainid,tbup.name,tbborrow.userid,tbup.id,tbborrow.status,tbborrow.datetime FROM `tbborrow` LEFT JOIN tbup ON tbborrow.userid = tbup.id Where tbborrow.status = 'Pending' ORDER BY mainid DESC;";
$listsimply = mysqli_query($db, $sqlgetsimplifiedlist);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Borrow Request</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
            .table {
            margin-top: 15px;
        }

        .table th, .table td {
            text-align: center;
            padding: 12px;
        }

        .table th {
            background-color: #9e1b32;
            color: white;
        }

        .table td {
            background-color: #f9f9f9;
        }
        .active>.page-link, .page-link.active{
            background-color: #9e1b32;
            border-color: #9e1b32; 
        }
        .page-link {
            color: #9e1b32;
        }
        
    </style>
</head>
<body>
<?php include('static/sidebar.php') ?>
<section class="home-section" style="overflow-y: auto;">
    <div class="home-content">
        <i class='bx bx-menu'></i>
    </div>
    <div class="container mt-5">
        <h2>Borrow Request</h2>
        <table id="itemRequestTable" class="table table-striped table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Person</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($data = mysqli_fetch_assoc($listsimply)) { ?>
                <tr>
                    <td><a href="#" class="personLink" data-bs-toggle="modal"
                        data-bs-target="#personDetailsModal<?php echo $data['mainid'] ?>"><?php echo $data['name'] ?></a>
                        <?php
                        $sqlgetitems = "SELECT * FROM tbborrow INNER JOIN  tbpendingborrow ON tbpendingborrow.transid = tbborrow.id INNER JOIN tbproductlist ON tbproductlist.id = tbpendingborrow.itemid WHERE tbpendingborrow.userid = '" . $data['userid'] . "' AND tbborrow.id = '" . $data['mainid'] . "'";
                        $listitems = mysqli_query($db, $sqlgetitems);
                        // echo $sqlgetitems;
                        ?>
                        <!-- Person Details Modal -->
                        <div class="modal fade" id="personDetailsModal<?php echo $data['mainid'] ?>" tabindex="-1"
                            aria-labelledby="personDetailsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="personDetailsModalLabel">Items Requested</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table table-striped" id="personModal">
                                            <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                                <th>Available</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($itelis = mysqli_fetch_assoc($listitems)) { ?>
                                                <tr>
                                                    <td><?php echo $itelis['name'] ?></td>
                                                    <td><?php echo $itelis['unit'] ?></td>
                                                    <td><?php echo $itelis['borrowqty'] ?></td>
                                                    <td><?php echo $itelis['quantity'] ?></td>
                                                </tr>
                                            <?php } ?>
                                            <!-- Additional rows can be added dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </td>
                    <td><?php echo date("F j, Y g:i A", strtotime($data['datetime'])); ?></td>
                    <td>
                        <?php if ($data['status'] == "Pending") { ?>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#approveModal<?php echo $data['mainid'] ?>">
                                <i class="bi bi-check-circle-fill"></i> Approve
                            </button>
                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal<?php echo $data['mainid'] ?>" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p id="approveMessage<?php echo $data['mainid'] ?>">Are you sure you want to approve this request for <span class="text-danger"><?php echo $data['name']; ?>?</span></p>
                                            <!-- Optional message input field -->
                                            <form method="post" action="">
                                                <textarea name="approvalMessage" class="form-control" placeholder="Optional message" rows="3"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <input type="hidden" name="approveborrow" value="<?php echo $data['mainid'] ?>">
                                                <input type="hidden" name="userid" value="<?php echo $data['userid'] ?>">
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($data['status'] == "Pending") { ?>
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal<?php echo $data['mainid'] ?>">
                                <i class="bi bi-x-circle-fill"></i> Reject
                            </button>
                            <?php } ?>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal<?php echo $data['mainid'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="rejectModalLabel">Reject Request: </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p id="rejectMessage<?php echo $data['mainid'] ?>">Are you sure you want to reject this request for <span class="text-danger"><?php echo $data['name']; ?>?</span></p>
                                            <!-- Optional message input field -->
                                            <form method="post" action="">
                                            <textarea name="rejectionMessage" class="form-control" placeholder="Optional message" rows="3"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            
                                                <input type="hidden" name="rejectborrow" value="<?php echo $data['mainid'] ?>">
                                                <input type="hidden" name="userid" value="<?php echo $data['userid'] ?>">
                                                <button type="submit" class="btn btn-danger">Reject</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <!-- Success Modal -->
<div id="successModal" class="modal fade" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="successMessage">Success message will go here</p>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="modal fade" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="errorMessage">Error message will go here</p>
            </div>
        </div>
    </div>
</div>

    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // Initialize the main DataTable for itemRequestTable
    var itemRequestTable = $('#itemRequestTable').DataTable();

    // Handle modal visibility and email status (Success or Failure) based on approval or rejection
    <?php if (isset($statusMessage)) { ?>
        var statusMessage = "<?php echo $statusMessage; ?>";
        if (statusMessage.includes('rejected')) {
            // If the status message is about rejection
            $('#successMessage').text(statusMessage); // Set the success message for rejection
        } else {
            // Handle other cases (approvals or others)
            $('#successMessage').text(statusMessage);
        }

        $('#successModal').modal('show');
        setTimeout(function () {
            window.location = '<?php echo $redirectUrl; ?>';  // Ensure proper redirect after message
        }, 2000);
    <?php } ?>

    // Handle actions when approving a borrow request
    $('.approveborrow').on('click', function () {
        var submitButton = $(this); // Get the clicked button
        submitButton.prop('disabled', true);  // Disable the button
        submitButton.html('<i class="bi bi-arrow-clockwise"></i> Processing...'); // Change button text to indicate it's processing
        
        // Open the approve modal
        $('#approveModal').modal('show');

        // Prevent any further clicks until the modal is processed
        return false;
    });

    // Handle actions when rejecting a borrow request
    $('.rejectborrow').on('click', function () {
        var submitButton = $(this); // Get the clicked button
        submitButton.prop('disabled', true);  // Disable the button
        submitButton.html('<i class="bi bi-arrow-clockwise"></i> Processing...'); // Change button text to indicate it's processing
        
        // Open the reject modal
        $('#rejectModal').modal('show');

        // Prevent any further clicks until the modal is processed
        return false;
    });

    // Handle click event on person link to populate modal with product details
    $('.personLink').on('click', function () {
        // Populating product details in the modal
        $('#productName').val('Product A').prop('disabled', true);
        $('#unit').val('PCS').prop('disabled', true);
        $('#quantity').val('10').prop('disabled', true);
    });

    // Handle modal visibility and initialize DataTable for the person details modal only when it's shown
    $('#personDetailsModal').on('shown.bs.modal', function () {
        // Initialize DataTable for the modal if it's not already initialized
        if (!$.fn.dataTable.isDataTable('#personModal')) {
            $('#personModal').DataTable({
                "paging": false, // Disable pagination
                "searching": false, // Disable search
                "info": false // Disable show entries info
            });
        }
    });

    // Optional: Handle DataTable re-initialization for other modals (if required)
    // If you need to dynamically populate modals and then initialize DataTable
    $('#personModal').DataTable({
        "paging": false,
        "searching": false,
        "info": false
    });
});
</script>


<script src="static/script.js"></script>
<script>
  $(document).ready(function() {
    // Check window size and disable toggle functionality for smaller screens
    if ($(window).width() <= 768) {
        // If the window is mobile-sized, disable the open/close functionality
        $(".sidebar").removeClass("close");
    }

    // Add your sidebar toggle functionality here for larger screens if needed
    $(window).resize(function() {
        if ($(window).width() <= 768) {
            $(".sidebar").removeClass("close");
        }
    });
});

</script>
</body>
</html>