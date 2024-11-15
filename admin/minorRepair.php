<?php

// Start the session at the beginning of the script
session_start();

// Ensure that the user is logged in by checking the session
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
    exit();
}

// Include database connection file
require('../dbcred/db.php');

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Initialize PHPMailer
$mail = new PHPMailer(true);

// Fetch the logged-in user's name from the database
$assignedByName = 'Unknown';  // Default name in case something goes wrong

if (isset($_SESSION['loginid'])) {
    $loginId = $_SESSION['loginid'];  // Get the logged-in user's ID

    // Query the database to fetch the user's name using their login ID
    $query = "SELECT name FROM tbadmin WHERE id = '$loginId'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the user's name from the result
        $userData = mysqli_fetch_assoc($result);
        $assignedByName = $userData['name'];  // Get the name of the logged-in user
    }
}

// Initialize message variables
$successMessage = "";
$errorMessage = "";

if (isset($_POST['assignedTo']) && isset($_POST['repairid'])) {
    $repair_id = $_POST['repairid'];
    $assignedToId = $_POST['assignedTo'];

    // Fetch the maintenance person's name and email based on the assigned ID
    $mainteQuery = "SELECT name, email FROM `tbmp` WHERE `id` = '$assignedToId'";
    $mainteResult = mysqli_query($db, $mainteQuery);

    if ($mainteResult) {
        $mainteData = mysqli_fetch_assoc($mainteResult);
        $assignedToName = $mainteData['name'];
        $assignedToEmail = $mainteData['email'];

        // Generate a new unique token for the assignment
        $token = bin2hex(random_bytes(16)); // 32-character token

        // Get the current token from the database (in case you need to invalidate it)
        $query = "SELECT `token` FROM `tbminorrepair` WHERE `id` = '$repair_id'";
        $result = mysqli_query($db, $query);
        if ($result) {
            // Fetch the old token (if needed for any invalidation logic)
            $row = mysqli_fetch_assoc($result);
            $old_token = $row['token'];  // You could log this if you need

            // Update the repair request with the new maintenance person's name and new token
            $query = "UPDATE `tbminorrepair` 
                      SET `personnelAssigned` = '$assignedToName', `token` = '$token' 
                      WHERE `id` = '$repair_id'";

            if (mysqli_query($db, $query)) {
                // Set success message
                $successMessage = "Repair request has been successfully assigned to $assignedToName. The maintenance person has been notified.";
                // Send email notification to the newly assigned maintenance person
                try {
                    $mail = new PHPMailer(true); // Create a new PHPMailer instance
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'projxacts12@gmail.com';
                    $mail->Password = 'vdbwgupzfybcixsk';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipient
                    $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
                    $mail->addAddress($assignedToEmail, $assignedToName);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Repair Request Assigned - Action Required';
                    $mail->Body = "
                        <h3>A repair request has been assigned to you:</h3>
                        <p><strong>Assigned by:</strong> $assignedByName</p>
                        <p><strong>Assigned To:</strong> $assignedToName</p>
                        <p>Click the link below to complete the action:</p>
                        <a href='http://127.0.0.1/var/www/html/admin/maintenance_form.php?token=$token'>Complete the form</a>";

                    // Send the email
                    $mail->send();
                } catch (Exception $e) {
                    // Set error message if email fails
                    $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}. Please try again.";
                }
            } else {
                // Set error message if DB update fails
                $errorMessage = "Error updating repair request: " . mysqli_error($db);
            }
        }
    } else {
        // Set error message if maintenance person data fetching fails
        $errorMessage = "Error fetching maintenance data. Please try again later.";
    }
}

// If the form is submitted
if (isset($_POST['reselectYes'])) {
    $repairId = $_POST['repairId'];

    // Directly update personnel without checking assigned time
    $updateQuery = "UPDATE tbminorrepair SET personnelAssigned = NULL WHERE id = $repairId";

    if (mysqli_query($db, $updateQuery)) {
        // Successfully updated
        $successMessage = "The personnel has been successfully removed from the repair request. You can now reassign them.";
    } else {
        // Error updating the database
        $errorMessage = "There was an error processing your request. Please try again later.";
    }
}
// Handle form submissions (Insert, Update, Approve, Reject)

if (isset($_POST['name'])) {
    // Insert a new record into tbmp table
    $sqlinsert = "INSERT INTO `tbmp`(`name`, `email`) VALUES ('" . $_POST['name'] . "','" . $_POST['email'] . "')";
    mysqli_query($db, $sqlinsert);
}

if (isset($_POST['updateupid'])) {
    // Update an existing record in tbmp table
    $sqlupdateup = "UPDATE `tbmp` SET `name`='" . $_POST['updatename'] . "', `email`='" . $_POST['updateemail'] . "' WHERE id='" . $_POST['updateupid'] . "'";
    mysqli_query($db, $sqlupdateup);
}

// Handle repair approval
if (isset($_POST['approverepair'])) {
    $sqlupdater = "UPDATE `tbminorrepair` SET `status`='Approved', `approval`='Approved' WHERE id='" . $_POST['approverepair'] . "'";
    mysqli_query($db, $sqlupdater);

    $sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE id ='" . $_POST['approverepair'] . "'";
    $resmail = mysqli_fetch_assoc(mysqli_query($db, $sqlgetmr));

    // Email sending for approval
    try {
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';
        $mail->Password = 'vdbwgupzfybcixsk';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
        $mail->addAddress($resmail['email'], $resmail['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Minor Repair Request Approved';
        $mail->Body = 'Your Minor Repair Request has been approved <br> ' . $_SERVER['SERVER_ADDR'] . '/repair%20-%20userReview.php?repairid=' . $_POST['approverepair'];
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Handle repair rejection
if (isset($_POST['rejectmr'])) {
    $sqlupdater = "UPDATE `tbminorrepair` SET `status`='Rejected', `approval`='" . $_POST['reason'] . "' WHERE id='" . $_POST['rejectmr'] . "'";
    mysqli_query($db, $sqlupdater);

    // Email sending for rejection
    $sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE id ='" . $_POST['rejectmr'] . "'";
    $resmail = mysqli_fetch_assoc(mysqli_query($db, $sqlgetmr));

    try {
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';
        $mail->Password = 'vdbwgupzfybcixsk';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
        $mail->addAddress($resmail['email'], $resmail['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Minor Repair Request Rejected';
        $mail->Body = 'Your Minor Repair Request has been Rejected Due to ' . $_POST['reason'] . ' <br> ' . $_SERVER['SERVER_ADDR'] . '/repair%20-%20userReview.php?repairid=' . $_POST['rejectmr'];
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch names from the tbmp table
$sqlgetup = "SELECT id, name FROM tbmp WHERE `status` = 'active';"; 
$listup = mysqli_query($db, $sqlgetup);

// Fetch pending repair requests from the tbminorrepair table
$sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE `status` = 'Pending';";
$listmr = mysqli_query($db, $sqlgetmr);

?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Minor Repair</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
    <body>

        <?php include('static/sidebar.php')?>
        <section class="home-section" style="overflow-y: auto;">
            <div class="home-content">
                <i class='bx bx-menu'></i>
            </div>
            <div class="container mt-5">
            <h2>Minor Repair</h2>
            <br>
            <table class="table table-striped" id="minorRepairTable">
                <thead class="table-dark">
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($data = mysqli_fetch_assoc($listmr)) { ?>
                <tr>
                    <td><a href="#" class="requestLink" data-bs-toggle="modal" data-bs-target="#requestDetailsModal<?php echo $data['id']?>"><?php echo $data['name']?></a></td>
                    <!-- Form Modal -->
                    <div class="modal fade" id="requestDetailsModal<?php echo $data['id']?>" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="requestModalLabel">Request by <?php echo $data['name']?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="row">
                                            <!-- <h6>Request By:<span class="text-danger"><?php echo $data['name']?> - <?php echo $data['datetime']?></span></h6>
                                            <h6>Status:<span class="text-danger"><?php echo $data['status']?> | Reason:  <?php echo $data['approval']?></span></h6> -->
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label">Name:</label>
                                                    <p class="text-success"><?php echo $data['name']?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="pincode" class="form-label">Position:</label>
                                                    <p class="text-success"><?php echo $data['position']?></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="department" class="form-label">Department:</label>
                                                    <p class="text-success"><?php echo $data['department']?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">Email:</label>
                                                    <p class="text-success"><?php echo $data['email']?></p>
                                            
                                                </div>
                                            </div>

                                            <h6>Description Property:</h6>
                                            <hr>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="type" class="form-label">Type:</label>
                                                    <p class="text-success"><?php echo $data['type']?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="serial_num" class="form-label">Serial No.:</label>
                                                    <p class="text-success"><?php echo $data['serial']?></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="brandmodel" class="form-label">Brand Model:</label>
                                                    <p class="text-success"><?php echo $data['brandmodel']?></p>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="propertyNo" class="form-label">Property No.:</label>
                                                    <p class="text-success"><?php echo $data['propertyno']?></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="acqusitionCost" class="form-label">Acquisition Cost:</label>
                                                    <p class="text-success"><?php echo $data['acqcost']?></p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="mb-3">
                                                    <label for="acqusitionDate" class="form-label">Acquisition Date:</label>
                                                    <p class="text-success"><?php echo $data['acqdate']?></p>
                                                </div>
                                            </div>
                                            <h6>Defects and Complaints:</h6>
                                            <hr>
                                            <div class="col-sm-12">
                                                <div class="mb-3">
                                                    <label for="message" class="form-label">Nature and Scope of Works to de done:</label>
                                                    <p class="text-success"><?php echo $data['scope']?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div> -->

                        </div>
                    </div>
                    <td><?php echo $data['status']?></td>
                    <td><?php echo $data['datetime']?></td>
                    <td>
                    <?php
                    $repairId = $data['id'];
                    $query = "SELECT personnelAssigned FROM tbminorrepair WHERE id = $repairId";
                    $result = mysqli_query($db, $query);

                    // Check if the query returns a result
                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        $personnelAssigned = $row['personnelAssigned']; // Get the personnelAssigned value
                    }

                    // Render the button based on whether personnel is assigned
                    if (empty($personnelAssigned)) {
                        // If no personnel is assigned
                        echo '<button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#assignedModal' . $repairId . '">
                                <i class="bi bi-check-circle-fill"></i> Assigned
                            </button>';
                    } else {
                        // If personnel is already assigned, show "reselect" button
                        echo '<button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#reselectModal' . $repairId . '">
                                <i class="bi bi-pencil-fill"></i> Reselect
                            </button>';
                    }
                    ?>
                        
                        <!-- Reselect Modal -->
                        <div class="modal fade" id="reselectModal<?php echo $repairId; ?>" tabindex="-1" aria-labelledby="reselectModalLabel<?php echo $repairId; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reselectModalLabel<?php echo $repairId; ?>">Confirmation</h5>
                                        <!-- The close button will not appear, we use custom functionality -->
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="display:none;"></button>
                                    </div>
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        Do you want to reselect maintenance personnel? This can be changed only after 1 day.
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <form method="post" action="">
                                            <input type="hidden" name="repairId" value="<?php echo $repairId; ?>">
                                            
                                            <!-- Yes Button with Icon -->
                                            <button type="submit" name="reselectYes" class="btn btn-outline-danger">
                                                <i class="bi bi-check-circle-fill"></i> Yes
                                            </button>
                                            
                                            <!-- No Button with Icon -->
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle-fill"></i> No
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        
                        <!-- Modal for assigning personnel -->
                        <div class="modal fade" id="assignedModal<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="assignedModalLabel<?php echo $data['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="assignedModalLabel<?php echo $data['id']; ?>">Assign Maintenance Personnel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Query to get the current assigned personnel
                                        $repairId = $data['id'];
                                        $query = "SELECT personnelAssigned FROM tbminorrepair WHERE id = $repairId";
                                        $result = mysqli_query($db, $query);
                                        if ($result) {
                                            $row = mysqli_fetch_assoc($result);
                                            $assignedName = $row['personnelAssigned'];
                                        } else {
                                            echo "<p>Error fetching the assigned personnel data.</p>";
                                        }
                                        ?>

                                        <!-- Form for assigning personnel -->
                                        <form method="post" action="">
                                            <div class="mb-3">
                                                <label for="assignedTo" class="form-label">Assign to:</label>
                                                <select class="form-select" name="assignedTo" required>
                                                    <option value="">Select here</option>
                                                    <?php while ($up = mysqli_fetch_assoc($listup)) { ?>
                                                        <option value="<?php echo $up['id']; ?>"><?php echo $up['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <input type="hidden" name="repairid" value="<?php echo $data['id']; ?>">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Assign</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <?php if ($data['status'] == "Pending") { ?>
                            <a href="adminForm.php?id=<?php echo $data['id']; ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-circle-fill"></i> Edit
                            </a>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                <i class="bi bi-check-circle-fill"></i> Approve
                            </button>

                            <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel<?php echo $data['id'] ?>"aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="approveModalLabel<?php echo $data['id'] ?>">Approve Repair Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to accept this request?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                            </button>
                                            <form method="post" action="">
                                                <input type="hidden" class="form-control" name="approverepair"
                                                    value="<?php echo $data['id'] ?>">
                                                <button type="submit" class="btn btn-success">Approve</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle-fill"></i> Reject
                            </button>

                            <a href="generatepdf.php?id=<?php echo $data['id']; ?>&name=<?php echo urlencode($data['name']); ?>&position=<?php echo urlencode($data['position']); ?>&department=<?php echo urlencode($data['department']); ?>&email=<?php echo urlencode($data['email']); ?>&type=<?php echo urlencode($data['type']); ?>&serial=<?php echo urlencode($data['serial']); ?>&brandmodel=<?php echo urlencode($data['brandmodel']); ?>&propertyno=<?php echo urlencode($data['propertyno']); ?>&acqcost=<?php echo urlencode($data['acqcost']); ?>&acqdate=<?php echo urlencode($data['acqdate']); ?>&scope=<?php echo urlencode($data['scope']); ?>&datetime=<?php echo urlencode($data['datetime']); ?>" class="btn btn-secondary btn-sm">
                                <i class="bi bi-printer"></i> Print
                            </a>


                        <?php } ?>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $data['id'] ?>"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel<?php echo $data['id'] ?>">Reject Request</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">

                                        <form method="post" action="">
                                            <label for="productName" class="form-label">Rejection Reason</label>
                                            <input type="text" class="form-control" name="reason" required placeholder="Reason for Rejection">
                                            Are you sure you want to reject this request?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                        </button>
                                        <form method="post" action="">
                                            <input type="hidden" class="form-control" name="rejectmr"
                                                    value="<?php echo $data['id'] ?>">
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                <!-- Add more rows as needed -->
                </tbody>
            </table>
            </div>

        <!-- Bootstrap JS (jQuery is required) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

        <!-- Bootstrap Bundle (Popper.js is required) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <script>
        $(document).ready(function() {
            $('#minorRepairTable').DataTable();
        });
        </script>

        <script>
        function toggleFields(action) {
            var repairFields = document.getElementById("repairFields");
            var remarks = document.getElementById("remarks");
            if (action === 'approved') {
            repairFields.style.display = "block";
            } else {
            repairFields.style.display = "none";
            remarks.style.display = "block";
            }
        }
        </script>

        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="static/script.js"></script>
            <div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="declineModalLabel">Decline Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" rows="3" placeholder="Enter your remarks"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Decline</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
                </div>
            </div>
            </div>
        </section>
    </body>
</html>

<?php
require 'success.php';
require 'error.php';
?>