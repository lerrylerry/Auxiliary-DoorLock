<?php
// Start the session to access session variables like $_SESSION['username']
session_start();

require('../dbcred/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; // Include PHPMailer autoloader

// Get the token from the URL
if (!isset($_GET['token'])) {
    // Redirect to the custom 404 page
    header('Location: 404.php');
    exit();
}

$token = $_GET['token'];

// Validate the token (check if it exists and matches the assigned name)
$query = "SELECT * FROM `tbminorrepair` WHERE `token` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Token doesn't exist or has already been used, so redirect to 404 page
    header('Location: 404.php');
    exit();
}

// If the token is valid, fetch the repair details
$repair = $result->fetch_assoc();

// Check if the repair has been assigned to someone (by their name)
if (empty($repair['personnelAssigned'])) {
    // If no employee is assigned, redirect to 404 page
    header('Location: 404.php');
    exit();
}

$formSubmitted = false; // Flag to check if form was successfully submitted

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $maintenance_notes = $_POST['notes'];
    $repair_status = isset($_POST['repairStatus']) ? $_POST['repairStatus'] : NULL;
    $timestamp = $_POST['dateAssessed'];  // Get the timestamp of when the form was submitted
    $assessed_by = $_POST['personnelAssigned'];  // Get the user who assessed
    $maintenancePersonnel = $_POST['personnelAssigned'];

    // Use prepared statement to update the repair record
    $updateQuery = "UPDATE `tbminorrepair` 
                    SET `status` = 'Maintenance Assessment', 
                        `notes` = ?, 
                        `repairStatus` = ?, 
                        `dateAssessed` = ?, 
                        `assessedBy` = ?,  
                        `maintenancePersonnel` = ?, 
                        `token` = NULL 
                    WHERE `token` = ?";

    $stmtUpdate = $db->prepare($updateQuery);
    $stmtUpdate->bind_param("ssssss", $maintenance_notes, $repair_status, $timestamp, $assessed_by, $maintenancePersonnel, $token);

    if ($stmtUpdate->execute()) {
        // Send email after updating the record

        // Fetch admin email (id = 1)
        $emailQuery = "SELECT email FROM tbadmin WHERE id = 1";
        $emailResult = $db->query($emailQuery);
        $admin = $emailResult->fetch_assoc();
        $adminEmail = $admin['email'];

        if ($adminEmail) {
            // Create PHPMailer instance
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'projxacts12@gmail.com'; // Sender's email address
                $mail->Password = 'vdbwgupzfybcixsk'; // Sender's email password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('projxacts12@gmail.com', 'Auxiliary System'); // Sender email address
                $mail->addAddress($adminEmail); // Admin email address

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Repair Request Assessment Completed';
                $mail->Body    = "
                    <h2>Repair Request Assessment Completed</h2>
                    <p><strong>Repair Request Details:</strong></p>
                    <p><strong>Assigned To:</strong> {$repair['personnelAssigned']}</p>
                    <p>You may now evaluate the assessment. Check the auxiliary website for more details. Thank You!</p>
                ";

                // Send email
                if ($mail->send()) {
                    // Set flag to indicate successful form submission
                    $formSubmitted = true;
                } else {
                    error_log("Error sending email: {$mail->ErrorInfo}");
                }
            } catch (Exception $e) {
                // Handle PHPMailer error
                error_log("Error sending email: {$mail->ErrorInfo}");
            }
        } else {
            error_log("Admin email not found.");
        }

        // Redirect to the success page after successful update and email send
        if ($formSubmitted) {
            header('Location: 200.php'); // Redirect to the success page
            exit();
        }
    } else {
        error_log("Error updating repair status: " . $stmtUpdate->error);
    }

    // Redirect to a failure page or handle error as needed (optional)
    header('Location: error.php'); // Optional failure page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Assessment</title>
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
                        <h3>Assessment Form</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-4">Request Details</h4>

                        <!-- Use Bootstrap grid for a clean, responsive layout -->
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
                            <div class="col-sm-4"><strong>Complaints:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['scope']; ?></div>
                        </div>

                        <!-- Add the horizontal cut line here -->
                        <hr> <!-- Horizontal line to separate the form -->
                        <!-- Form for maintenance notes -->
                        <h4 class="mb-4 text-danger">Initial Assessment - Required</h4>
                        <div class="row">
                            <div class="col-sm-4">Assigned To:</div>
                            <div class="col-sm-8"><?php echo $repair['personnelAssigned']; ?></div>
                        </div>
                        <form action="" method="post" id="repairStatus">
                            <div class="mb-3">
                                <label class="form-label">Repair Status:</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="repairStatus" id="inHouse" value="Yes" required>
                                    <label class="form-check-label" for="inHouse">Can be Repaired/Fabricated In-house</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="repairStatus" id="cannotInHouse" value="No" required>
                                    <label class="form-check-label" for="cannotInHouse">Cannot be Repaired/Fabricated In-house</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes:</label>
                                <textarea name="notes" id="notes" rows="4" class="form-control" placeholder="Ex: Material A | 3pcs, Material B | 10cm, Material C | 3gal..." required></textarea>
                            </div>

                            <!-- Hidden input for timestamp, assessedBy, and personnelAssigned -->
                            <input type="hidden" name="dateAssessed" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            <input type="hidden" name="personnelAssigned" value="<?php echo $repair['personnelAssigned']; ?>">

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check-circle"></i> Submit Assessment
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

    <!-- Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
