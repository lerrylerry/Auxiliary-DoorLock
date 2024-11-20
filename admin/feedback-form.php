<?php
// Start the session to access session variables like $_SESSION['username']
session_start();

// Include the database connection
require('../dbcred/db.php');

// Ensure that the database connection is still open before using it
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the token from the URL
if (!isset($_GET['token'])) {
    die("Invalid or missing token.");
}

$token = $_GET['token'];

// Validate the token and fetch the corresponding repair record
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
$id = $repair['id'];  // Fetch the ID from the repair details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | User Review</title>
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

                        <!-- Approval Status section -->
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

                        <!-- Inspection Report Display -->
                        <h4 class="mb-4">Action Taken</h4>

                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Date Started:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['dateStarted'];?></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Date Finished:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['dateFinished'];?></div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Post Inspection Report:</strong></div>
                            <div class="col-sm-8"><?php echo $repair['postInspectionReport'];?></div>
                        </div>
                        
                        <!-- Form to submit evaluation feedback -->
                        <form action="" method="post" id="approvalStatus">
                            <div class="mb-3">
                                <label for="evaluation" class="form-label"><strong>Your Feedback:</strong></label>
                                <textarea id="evaluation" name="evaluation" rows="5" class="form-control" required></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check-circle"></i> Submit Evaluation
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

</body>
</html>
