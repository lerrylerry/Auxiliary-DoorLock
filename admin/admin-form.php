<?php
// Start the session to access session variables like $_SESSION['username']
session_start();

// Ensure that the user is logged in by checking the session
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
    exit();
}

// Include the database connection
require('../dbcred/db.php');

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Ensure that the database connection is still open before using it
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch the name of the 'headOfAuxiliary' dynamically from tbadmin where id = 1
$query = "SELECT `name` FROM `tbadmin` WHERE `id` = 1";
$result = mysqli_query($db, $query);

if ($result) {
    $admin = mysqli_fetch_assoc($result);
    $headOfAuxiliary = $admin['name'];
} else {
    $headOfAuxiliary = '';  // Default value if the query fails
}

// Get the repair request ID from the URL (passed as a GET parameter)
if (!isset($_GET['id'])) {
    die("Invalid or missing ID.");
}

$id = $_GET['id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected approval status from the form
    $approvalStatus = $_POST['approvalStatus'];
    $notes2 = isset($_POST['notes2']) ? $_POST['notes2'] : '';
    $headOfAuxiliary = $_POST['headOfAuxiliary'];  // Using the dynamic value
    $dateHead = $_POST['dateHead'];

    // Prepare SQL query to update the repair status
    $query = "UPDATE `tbminorrepair` SET `approvalStatus` = ?, `notes2` = ?, `headOfAuxiliary` = ?, `dateHead` = ? WHERE `id` = ?";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param("ssssi", $approvalStatus, $notes2, $headOfAuxiliary, $dateHead, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // If update was successful, show a success message and reload the page
            echo "<script>alert('Repair request updated successfully.');</script>";

            // Now, send the email notification
            sendEmailNotification($approvalStatus, $id);

            // Reload the page
            echo "<script>window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('No changes made.');</script>";
        }

        // Close the prepared statement
        $stmt->close();
    } else {
        echo "<script>alert('Error preparing SQL statement.');</script>";
    }
}

// Validate the id (check if it exists in the tbminorrepair table)
$query = "SELECT * FROM `tbminorrepair` WHERE `id` = ?";
$stmt = $db->prepare($query);

// Ensure the connection is open before executing
if ($stmt) {
    $stmt->bind_param("i", $id); // Use "i" for integer binding
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        die("No repair request found for the given ID.");
    }

    // If the ID is valid, fetch the repair details
    $repair = $result->fetch_assoc();
} else {
    die("Failed to prepare statement.");
}

// Function to send the email notification
function sendEmailNotification($approvalStatus, $repairId) {
    global $db;

    // Get the repair details from the database using the provided repair ID
    $query = "SELECT * FROM `tbminorrepair` WHERE `id` = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $repairId);
    $stmt->execute();
    $result = $stmt->get_result();
    $repair = $result->fetch_assoc();

    // Get the name of the personnel assigned from the tbminorrepair table
    $personnelAssigned = $repair['personnelAssigned'];

    // Query the tbmp table to get the email address of the assigned personnel
    $query = "SELECT `email` FROM `tbmp` WHERE `name` = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $personnelAssigned); // Assuming personnelAssigned is a string (name)
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if we found a matching record
    if ($result->num_rows > 0) {
        $personnel = $result->fetch_assoc();
        $recipientEmail = $personnel['email']; // Get the email address
    } else {
        echo "<script>alert('Personnel not found in tbmp.');</script>";
        return;
    }

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com'; // Your Gmail address
        $mail->Password = 'vdbwgupzfybcixsk'; // Your Gmail app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'Auxiliary System');
        $mail->addAddress($recipientEmail); // Recipient's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Repair Request Approval Status Updated';

        if ($approvalStatus === 'Approved') {
            // Generate a unique token for the approval status (a one-time link)
            $token = bin2hex(random_bytes(16));  // Generate a token (32 bytes = 64 characters)
            // Store this token in the database or some other location (optional)
            
            // Create a link to access the repair action page with the token
            $link = "http://127.0.0.1/Auxiliary-DoorLock/admin/repair-form.php?token=" . $token;

            // Update the database with the generated token (optional, for later validation)
            $query = "UPDATE `tbminorrepair` SET `token` = ? WHERE `id` = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param("si", $token, $repairId);
            $stmt->execute();

            $mail->Body = '
                <h2>Repair Request Update</h2>
                <p><strong>Name:</strong> ' . $repair['name'] . '</p>
                <p><strong>Position:</strong> ' . $repair['position'] . '</p>
                <p><strong>Approval Status:</strong> ' . $approvalStatus . '</p>
                <p><strong>Notes:</strong> ' . nl2br(htmlspecialchars($repair['notes'])) . '</p>
                <p><strong>Action Link:</strong> <a href="' . $link . '">Click here to perform the repair</a></p>
            ';
        } elseif ($approvalStatus === 'Disapproved') {
            $mail->Body = '
                <h2>Repair Request Update</h2>
                <p><strong>Name:</strong> ' . $repair['name'] . '</p>
                <p><strong>Position:</strong> ' . $repair['position'] . '</p>
                <p><strong>Approval Status:</strong> ' . $approvalStatus . '</p>
                <p><strong>Message:</strong> The repair request has been disapproved.</p>
            ';
        } elseif ($approvalStatus === 'Resubmit') {
            $mail->Body = '
                <h2>Repair Request Update</h2>
                <p><strong>Name:</strong> ' . $repair['name'] . '</p>
                <p><strong>Position:</strong> ' . $repair['position'] . '</p>
                <p><strong>Approval Status:</strong> ' . $approvalStatus . '</p>
                <p><strong>Message:</strong> The repair request needs to be resubmitted with further details.</p>
                <p><strong>Notes:</strong> ' . nl2br(htmlspecialchars($repair['notes2'])) . '</p>
            ';
        }

        // Send the email
        $mail->send();
        echo "<script>alert('Email sent successfully.');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Admin Evaluation</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/forms.css">
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center">
                        <h3>Minor Repair Form</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-4">Repair Request Information</h4>

                        <!-- Use Bootstrap grid for a clean, responsive layout -->
                        <div class="row">
                            <div class="col-sm-4"><strong>Assigned To:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['personnelAssigned']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Name:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['name']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Position:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['position']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Department:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['department']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Type:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['type']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Serial No:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['serial']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Brand/Model:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['brandmodel']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Property No:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['propertyno']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Acquisition Cost:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['acqcost']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Acquisition Date:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['acqdate']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4"><strong>Scope of Work:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['scope']; ?></div>
                        </div>

                        <!-- Horizontal cut line to separate content -->
                        <hr>

                        <!-- Repair Status section -->
                        <div class="row mb-2">
                            <h4 class="mb-4">Maintenance Assessment</h4>
                            <div class="col-sm-4"><strong>Repair Status:</strong></div>
                            <div class="col-sm-8">
                                <?php 
                                    // Display the repair status based on the value
                                    if ($repair['repairStatus'] === 'Yes') {
                                        echo "Can be Repaired/Fabricated In-house";
                                    } else {
                                        echo "Cannot be Repaired/Fabricated In-house";
                                    }
                                ?>
                            </div>
                        </div>

                        <!-- Maintenance Notes section -->
                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Maintenance Notes:</strong></div>
                            <div class="col-sm-8">
                                <?php echo nl2br(htmlspecialchars($repair['notes'])); ?>
                            </div>
                        </div>

                        <hr>

                        <h4 class="mb-4">Approval Status</h4>

                        <form action="" method="post" id="approvalStatus">
                            <div class="mb-3">
                                <label class="form-label"><strong>Approval Status:</strong></label><br>
                                <!-- Radio buttons for approval status -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="approvalStatus" id="approved" value="Approved" required>
                                    <label class="form-check-label" for="approved">Approved</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="approvalStatus" id="disapproved" value="Disapproved" required>
                                    <label class="form-check-label" for="disapproved">Disapproved</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="approvalStatus" id="resubmit" value="Resubmit" required>
                                    <label class="form-check-label" for="resubmit">Resubmit</label>
                                </div>
                            </div>

                            <!-- Resubmit Notes -->
                            <div class="mb-3" id="notes2Section" style="display:none;">
                                <label class="form-label"><strong>Notes:</strong></label>
                                <textarea class="form-control" name="notes2" id="notes2" rows="4"></textarea>
                            </div>

                            <!-- Hidden input for dateHead and headOfAuxiliary -->
                            <input type="hidden" name="dateHead" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            <input type="hidden" name="headOfAuxiliary" id="headOfAuxiliary" value="<?php echo $headOfAuxiliary; ?>">

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check-circle"></i> Repair Completed
                            </button>
                        </form>

                    </div>
                    <div class="card-footer">
                        <small>&copy; 2024 Auxiliary System</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional, for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS to show/hide the Resubmit notes textarea -->
    <script>
        document.querySelectorAll('input[name="approvalStatus"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.value === 'Resubmit') {
                    document.getElementById('notes2Section').style.display = 'block';
                } else {
                    document.getElementById('notes2Section').style.display = 'none';
                }
            });
        });
    </script>

</body>
</html>
