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
                    $mail->Subject = 'Minor Repair Request Assigned - Action Required';
                    $mail->Body = "
                        <h3>A repair request has been assigned to you:</h3>
                        <p><strong>Assigned by:</strong> $assignedByName</p>
                        <p><strong>Assigned To:</strong> $assignedToName</p>
                        <p>Click the link below to complete the action:</p>
                        <a href='http://127.0.0.1/Auxiliary-DoorLock/admin/assessment-form.php?token=$token'>Complete the form</a>";

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
    $sqlupdater = "UPDATE `tbminorrepair` SET `status`='Rejected' WHERE id='" . $_POST['rejectmr'] . "'";
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


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Query to fetch repair requests where dateHead is NULL
$sqlgetPendingRepairs = "SELECT * FROM `tbminorrepair` WHERE `status` = 'Assessed by Maintenance' AND (`dateHead` IS NULL OR `dateHead` = '')";
$listPendingRepairs = mysqli_query($db, $sqlgetPendingRepairs);


?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Minor Repair</title>
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/forms.css">
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
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="request-tab" data-bs-toggle="tab" href="#request" role="tab" aria-controls="request" aria-selected="true">Request</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="assessment-tab" data-bs-toggle="tab" href="#assessment" role="tab" aria-controls="assessment" aria-selected="false">Assessment</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="results-tab" data-bs-toggle="tab" href="#results" role="tab" aria-controls="results" aria-selected="false">Results</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="feedback-tab" data-bs-toggle="tab" href="#feedback-tab" role="tab" aria-controls="feedback-tab" aria-selected="false">Feedback</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabsContent">
                <!-- Request Tab -->
                <div class="tab-pane fade show active" id="request" role="tabpanel" aria-labelledby="request-tab">
                    <table class="table table-striped" id="requestTable">
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
                                        <div class="text-end">
                                            <form method="post" action="">
                                                <input type="hidden" class="form-control" name="rejectmr"
                                                        value="<?php echo $data['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            <i class="bi bi-x-circle-fill"></i> Reject
                                                        </button>
                                            </form>                                            
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                                <i class="bi bi-arrow-left-circle-fill"></i> Back
                                            </button>
                                        </div>
                                    </div>
                                </div>

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
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="display:none;"></button>
                                    </div>
                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        Do you want to reselect maintenance personnel?
                                    </div>
                                    <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <form method="post" action="">
                                            <input type="hidden" name="repairId" value="<?php echo $repairId; ?>">
                                            <button type="submit" name="reselectYes" class="btn btn-outline-danger">
                                                <i class="bi bi-check-circle-fill"></i> Yes
                                            </button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="bi bi-x-circle-fill"></i> No
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal for assigning personnel -->
                        <div class="modal fade" id="assignedModal<?php echo $repairId; ?>" tabindex="-1" aria-labelledby="assignedModalLabel<?php echo $repairId; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="assignedModalLabel<?php echo $repairId; ?>">Assign Maintenance Personnel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <?php
                                        // Query to get the current assigned personnel
                                        $query = "SELECT personnelAssigned FROM tbminorrepair WHERE id = $repairId";
                                        $result = mysqli_query($db, $query);
                                        if ($result) {
                                            $row = mysqli_fetch_assoc($result);
                                            $assignedName = $row['personnelAssigned'];
                                        } else {
                                            echo "<p>Error fetching the assigned personnel data.</p>";
                                        }

                                        // Query to get the list of active personnel
                                        $sqlgetup = "SELECT id, name FROM tbmp WHERE `status` = 'active';";
                                        $listup = mysqli_query($db, $sqlgetup);
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
                                            <input type="hidden" name="repairid" value="<?php echo $repairId; ?>">
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Assign</button>
                                            </div>
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






                <!-- Assessment Tab -->
                <div class="tab-pane fade" id="assessment" role="tabpanel" aria-labelledby="assessment-tab">
                    <table class="table table-striped" id="evaluateTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Loop through the result set and populate the table rows
                            if (mysqli_num_rows($listPendingRepairs) > 0) {
                                while ($row = mysqli_fetch_assoc($listPendingRepairs)) {
                                    // Assuming `name`, `status`, and `dateHead` are columns in your `tbminorrepair` table
                                    $repairName = $row['name'];
                                    $repairStatus = $row['status'];
                                    $repairDate = $row['dateHead'] ? $row['dateHead'] : 'Not Assigned';  // If no dateHead, show "Not Assigned"
                                    $repairId = $row['id'];  // The ID of the repair request
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($repairName); ?></td>
                                        <td><?php echo htmlspecialchars($repairStatus); ?></td>
                                        <td><?php echo htmlspecialchars($repairDate); ?></td>
                                        <td>
                                            <a href="admin-form.php?id=<?php echo $repairId; ?>" class="btn btn-primary btn-sm">Evaluate</a>
                                            <!-- Add more buttons as needed -->
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                // If no records are found
                                echo "<tr><td colspan='4'>No pending repairs found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
</div>








                <!-- Results Tab -->
                <div class="tab-pane fade" id="results" role="tabpanel" aria-labelledby="results-tab">
                    <h3>Contact Tab</h3>
                    <p>This is the content for the Contact tab. Add your contact details or a contact form here.</p>
                </div>

            </div>
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
            $('#requestTable').DataTable();
            $('#evaluateTable').DataTable();
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
        </section>
    </body>
</html>

<?php
require 'success.php';
require 'error.php';
?>