<?php
// Start the session to access session variables like $_SESSION['username']
session_start();

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

// Get the token from the URL
if (!isset($_GET['token'])) {
    die("Invalid or missing token.");
}

$token = $_GET['token'];

// Validate the token (check if it exists and matches the assigned name)
$query = "SELECT * FROM `tbminorrepair` WHERE `token` = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("This link is invalid or has already been used.");
}

// Fetch the repair details using the valid token
$repair = $result->fetch_assoc(); // Fetch the data associated with the valid token
$id = $repair['id'];  // The id is now fetched from the token validation row

// Handle the form submission for Post Inspection Report
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $dateStarted = $_POST['dateStarted'] ?? '';
    $dateFinished = $_POST['dateFinished'] ?? '';
    $postInspectionReport = $_POST['postInspectionReport'] ?? '';

    // Validate form data (make sure they are not empty)
    if (empty($dateStarted) || empty($dateFinished) || empty($postInspectionReport)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        // Update the `tbminorrepair` table with the post-inspection details
        $updateQuery = "UPDATE `tbminorrepair` 
                        SET `dateStarted` = ?, `dateFinished` = ?, `postInspectionReport` = ?, `token` = '' 
                        WHERE `id` = ?";
        $stmt = $db->prepare($updateQuery);
        $stmt->bind_param("ssss", $dateStarted, $dateFinished, $postInspectionReport, $id);

        // Check if the update was successful
        if ($stmt->execute()) {
            echo "<script>alert('Inspection Report submitted and repair details updated successfully.');</script>";

            // Now, generate an additional token for the evaluation link
            $evaluationToken = bin2hex(random_bytes(16)); // 32-character token

            // Update the evaluation token in the database
            $updateQuery = "UPDATE `tbminorrepair` SET `token` = ? WHERE `id` = ?";
            $stmt = $db->prepare($updateQuery);
            $stmt->bind_param("si", $evaluationToken, $id);
            $stmt->execute();

            // Now, send the email notification using PHPMailer
            require '../vendor/autoload.php'; // Ensure PHPMailer is loaded

            $mail = new PHPMailer(true); // Create a new PHPMailer instance

            try {
                // Server settings
                $mail->isSMTP();                                           // Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                       // Gmail SMTP server
                $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
                $mail->Username   = 'projxacts12@gmail.com';                // Your Gmail address
                $mail->Password   = 'vdbwgupzfybcixsk';                     // Your Gmail app-specific password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Use TLS encryption
                $mail->Port       = 587;                                    // TCP port to connect to (TLS)

                // Recipients
                $mail->setFrom('projxacts12@gmail.com', 'Auxiliary System');
                $mail->addAddress($repair['email']);                        // Send to the email of the assigned personnel (assuming 'email' is in your db)

                // Content
                $mail->isHTML(true);                                        // Set email format to HTML
                $mail->Subject = 'Inspection Report Submitted for Repair ID ' . $id;
                $mail->Body    = "<p>Dear " . htmlspecialchars($repair['personnelAssigned']) . ",</p>
                                  <p>The inspection report for the repair request with ID <strong>" . $id . "</strong> has been successfully submitted.</p>
                                  <p><strong>Date Started:</strong> " . htmlspecialchars($dateStarted) . "<br>
                                  <strong>Date Finished:</strong> " . htmlspecialchars($dateFinished) . "<br>
                                  <strong>Post Inspection Report:</strong><br>" . nl2br(htmlspecialchars($postInspectionReport)) . "</p>
                                  <p><strong>Repair Completed:</strong> Your repair is now done.</p>
                                  <p>We kindly request that you evaluate the repair. Please click the link below to provide your feedback:</p>
                                  <p><a href='http://127.0.0.1/Auxiliary-DoorLock/admin/feedback-form.php?token=" . $evaluationToken . "'>Click here to evaluate the repair</a></p>
                                  <p>Best regards,<br>Auxiliary System</p>";

                // Send the email
                $mail->send();
                echo "<script>alert('Email notification sent successfully.');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Email could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('Failed to update repair details.');</script>";
        }
    }
}

// Token generation and sending email functionality (when the page is loaded or if the token doesn't exist)
if (empty($repair['token'])) {
    // Generate a 32-character random token (hexadecimal)
    $newToken = bin2hex(random_bytes(16)); // 32-character token

    // Update the token in the database (tbminorrepair table)
    $updateQuery = "UPDATE `tbminorrepair` SET `token` = ? WHERE `id` = ?";
    $stmt = $db->prepare($updateQuery);
    $stmt->bind_param("si", $newToken, $id);
    $stmt->execute();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Maintenance Repair</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/forms.css">

    <script>
        // Function to check if Date Started is earlier than or equal to Date Finished
        function validateDates() {
            var dateStarted = document.getElementById("dateStarted").value;
            var dateFinished = document.getElementById("dateFinished").value;

            if (new Date(dateStarted) > new Date(dateFinished)) {
                alert("Error: Date Started cannot be greater than Date Finished.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
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

                        <!-- Approval Status section -->
                        <div class="row mb-2">
                            <h4 class="mb-4">Admin Approval</h4>
                            <div class="col-sm-4"><strong>Head of Auxiliary:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['headOfAuxiliary']; ?></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-sm-4"><strong>Approval Status:</strong></div>
                            <div class="col-sm-8">
                                <?php 
                                    // Display the approval status based on the value
                                    if ($repair['approvalStatus'] === 'Approved') {
                                        echo "Your assessment was approved. You can now proceed to the repair!";
                                    }
                                ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Inspection Report Form -->
                        <h4 class="mb-4">Action Taken</h4>
                        <form method="POST" action="" onsubmit="return validateDates()">
                            <!-- Date Started Input -->
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Date Started:</strong></div>
                                <div class="col-sm-8">
                                    <input type="datetime-local" id="dateStarted" name="dateStarted" class="form-control" required>
                                </div>
                            </div>

                            <!-- Date Finished Input -->
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Date Finished:</strong></div>
                                <div class="col-sm-8">
                                    <input type="datetime-local" id="dateFinished" name="dateFinished" class="form-control" required>
                                </div>
                            </div>

                            <!-- Post Inspection Report Textarea -->
                            <div class="row mb-3">
                                <div class="col-sm-4"><strong>Post Inspection Report:</strong></div>
                                <div class="col-sm-8">
                                    <textarea id="postInspectionReport" name="postInspectionReport" rows="5" class="form-control" required></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check-circle"></i> Submit Inspection Report
                                </button>
                            </div>
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

</body>
</html>
