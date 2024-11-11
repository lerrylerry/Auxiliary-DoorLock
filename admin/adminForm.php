<?php
// Include your database connection file
require('../dbcred/db.php');

// Handle form submission and database update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    // var_dump($_POST);  
    // Uncomment this for debugging purpose
    // Collect form data
    $id = $_POST['id'];
    $repairStatus = isset($_POST['repairStatus']) ? $_POST['repairStatus'] : '';
    $assessedBy = isset($_POST['assessedBy']) ? $_POST['assessedBy'] : '';
    $personnelAssigned = isset($_POST['personnelAssigned']) ? $_POST['personnelAssigned'] : '';
    $partsToSupplies = isset($_POST['partsToSupplies']) ? $_POST['partsToSupplies'] : '';
    $approvalStatus = isset($_POST['approvalStatus']) ? $_POST['approvalStatus'] : '';
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
    $headOfAuxiliary = isset($_POST['headOfAuxiliary']) ? $_POST['headOfAuxiliary'] : '';
    $dateHead = isset($_POST['dateHead']) ? $_POST['dateHead'] : '';
    $maintenancePersonnel = isset($_POST['maintenancePersonnel']) ? $_POST['maintenancePersonnel'] : '';
    $dateStarted = isset($_POST['dateStarted']) ? $_POST['dateStarted'] : '';
    $dateFinished = isset($_POST['dateFinished']) ? $_POST['dateFinished'] : '';
    $postInspectionReport = isset($_POST['postInspectionReport']) ? $_POST['postInspectionReport'] : '';
    $acceptanceStatus = isset($_POST['acceptanceStatus']) ? $_POST['acceptanceStatus'] : '';
    $endUser = isset($_POST['endUser']) ? $_POST['endUser'] : '';
    $dateEndUser = isset($_POST['dateEndUser']) ? $_POST['dateEndUser'] : '';

    // Prepare the SQL query to update the record
    $sql = "UPDATE tbminorrepair 
            SET repairStatus = ?, 
                assessedBy = ?, 
                personnelAssigned = ?, 
                partsToSupplies = ?, 
                approvalStatus = ?, 
                notes = ?, 
                headOfAuxiliary = ?, 
                dateHead = ?, 
                maintenancePersonnel = ?, 
                dateStarted = ?, 
                dateFinished = ?, 
                postInspectionReport = ?, 
                acceptanceStatus = ?, 
                endUser = ?, 
                dateEndUser = ? 
            WHERE id = ?";

    // Prepare the statement
    if ($stmt = $db->prepare($sql)) {
        // Bind the parameters to the query
        $stmt->bind_param("ssssssssssssssss", 
            $repairStatus, 
            $assessedBy, 
            $personnelAssigned, 
            $partsToSupplies, 
            $approvalStatus, 
            $notes, 
            $headOfAuxiliary, 
            $dateHead, 
            $maintenancePersonnel, 
            $dateStarted, 
            $dateFinished, 
            $postInspectionReport, 
            $acceptanceStatus, 
            $endUser, 
            $dateEndUser, 
            $id
        );

        // Execute the query
        if ($stmt->execute()) {
            $successMessage = 'Record updated successfully.';
        } else {
            $errorMessage = 'Error updating record: ' . $db->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        $errorMessage = 'Error preparing statement: ' . $db->error;
    }
}

// Get the repair data if ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id']; // Get the ID from the URL

    // Prepare the SQL statement to prevent SQL injection
    $sqlgetrepair = "SELECT * FROM `tbminorrepair` WHERE `id` = ?";
    
    if ($stmt = $db->prepare($sqlgetrepair)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Fetch the repair data
        if ($repair = $result->fetch_assoc()) {
            // Extracting repair details into variables
            $namex = $repair['name']; // Adjust field names as per your database
            $positionx = $repair['position'];
            $departmentx = $repair['department'];
            $emailx = $repair['email'];
            $typex = $repair['type'];
            $modelx = $repair['brandmodel'];
            $serialx = $repair['serial'];
            $propertyx = $repair['propertyno'];
            $acquisitionDatex = $repair['acqdate']; // Adjust according to your field
            $acquisitionCostx = $repair['acqcost']; // Adjust according to your field
            $scopex = $repair['scope'];
        } else {
            // Handle case where no data is found
            echo "No repair found for the given ID.";
            exit;
        }
    } else {
        // Handle SQL preparation error
        echo "Error preparing the SQL statement.";
        exit;
    }
} else {
    // Handle case where ID is not set
    echo "No ID provided.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/repair.css">
    <title>Auxiliary | Repair</title>
</head>
<body>
<div class="card container my-3"
        style="width: 43rem; height: 150rem; border: 3px solid #0e0e0f; padding-bottom: 50px; ">

        <h4 class="card-title borrow-label">REQUESTED BY</h4>
    <hr>
    <div class="form-group row">
        <div class="col-sm-6">
            <label for="name">Name:</label>
            <p class="text-success fw-bold" id="namex"><?php echo htmlspecialchars($namex); ?></p>
        </div>
        <div class="col-sm-6">
            <label for="position">Position:</label>
            <p class="text-success fw-bold" id="positionx"><?php echo htmlspecialchars($positionx); ?></p>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label for="department">Department:</label>
            <p class="text-success fw-bold" id="departmentx"><?php echo htmlspecialchars($departmentx); ?></p>
        </div>
        <div class="col-sm-6">
            <label for="email">Email:</label>
            <p class="text-success fw-bold" id="emailx"><?php echo htmlspecialchars($emailx); ?></p>
        </div>
    </div>
    <h4 class="card-title borrow-label">DESCRIPTION OF PROPERTY</h4>
    <hr>
    <div class="form-group row">
        <div class="col-sm-6">
            <label for="type">Type:</label>
            <p class="text-success fw-bold" id="typex"><?php echo htmlspecialchars($typex); ?></p>
        </div>
        <div class="col-sm-6">
            <label for="model">Brand/Model:</label>
            <p class="text-success fw-bold" id="modelx"><?php echo htmlspecialchars($modelx); ?></p>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label for="serial">Serial No.:</label>
            <p class="text-success fw-bold" id="serialx"><?php echo htmlspecialchars($serialx); ?></p>
        </div>
        <div class="col-sm-6">
            <label for="property">Property No.:</label>
            <p class="text-success fw-bold" id="propertyx"><?php echo htmlspecialchars($propertyx); ?></p>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label for="acquisitionDate">Acquisition Date:</label>
            <p class="text-success fw-bold" id="acquisitionDatex"><?php echo htmlspecialchars($acquisitionDatex); ?></p>
        </div>
        <div class="col-sm-6">
            <label for="acquisitionCost">Acquisition Cost:</label>
            <p class="text-success fw-bold" id="acquisitionCostx"><?php echo htmlspecialchars($acquisitionCostx); ?></p>
        </div>
    </div>
    <h4 class="card-title borrow-label">DEFECTS AND COMPLAINTS</h4>
    <hr>
    <div class="form-group row">
        <div class="col-sm-12">
            <label for="scope">Scope:</label>
            <p class="text-success fw-bold" id="scopex"><?php echo htmlspecialchars($scopex); ?></p>
        </div>
    </div>
    

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <h4 class="card-title borrow-label">INITIAL ASSESSMENT</h4>
    <hr>
    <label for="repairlabelstatus">Where it should be repaired?</label>
    <hr>
    <div class="form-group row">
        <div class="form-group row mb-3">
            <div class="col-sm d-flex align-items-center">
                <div class="form-check form-check-inline">
                    <input required class="form-check-input" type="radio" name="repairStatus" id="canBeRepaired" value="yes">
                    <label class="form-check-label" for="canBeRepaired">Can be Repaired/Fabricated In-house</label>
                </div>
                <div class="form-check form-check-inline">
                    <input required class="form-check-input" type="radio" name="repairStatus" id="cannotBeRepaired" value="no">
                    <label class="form-check-label" for="cannotBeRepaired">Cannot be Repaired/Fabricated In-house</label>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row mb-3">
        <div class="col-sm-6">
            <label class="col-form-label" for="assessedBy">Assessed by:</label>
            <input required name="assessedBy" type="text" class="form-control" id="assessedBy" placeholder="Assessed by" value="<?php echo isset($assessedBy) ? $assessedBy : ''; ?>" autocomplete="off">
        </div>
        <div class="col-sm-6">
            <label class="col-form-label" for="personnelAssigned">Personnel Assigned:</label>
            <input required name="personnelAssigned" type="text" class="form-control" id="personnelAssigned" placeholder="Personnel Assigned" value="<?php echo isset($personnelAssigned) ? $personnelAssigned : ''; ?>" autocomplete="off">
        </div>
    </div>

    <!-- Supplies and Materials -->
    <div class="form-group mb-3">
        <label for="partsToSupplies">Parts to be Supplied</label>
        <textarea required name="partsToSupplies" class="form-control" id="partsToSupplies" placeholder="ex: Quantity | Unit | Particulars"><?php echo isset($partsToSupplies) ? $partsToSupplies : ''; ?></textarea>
    </div>

    <!-- Approval Status -->
    <h4 class="card-title borrow-label">APPROVAL</h4>
    <hr>
    <label>Approval Status:</label>
    <br>
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input required class="form-check-input" type="radio" name="approvalStatus" id="approved" value="approved" <?php echo (isset($approvalStatus) && $approvalStatus == 'approved') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="approved">APPROVED</label>
        </div>
        <div class="form-check form-check-inline">
            <input required class="form-check-input" type="radio" name="approvalStatus" id="disapproved" value="disapproved" <?php echo (isset($approvalStatus) && $approvalStatus == 'disapproved') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="disapproved">DISAPPROVED</label>
        </div>
        <div class="form-check form-check-inline">
            <input required class="form-check-input" type="radio" name="approvalStatus" id="resubmit" value="resubmit" <?php echo (isset($approvalStatus) && $approvalStatus == 'resubmit') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="resubmit">RESUBMIT REQUEST</label>
        </div>
    </div>

    <!-- Notes -->
    <div class="form-group">
        <label for="notes">Notes</label>
        <textarea name="notes" required class="form-control" id="notes"><?php echo isset($notes) ? $notes : ''; ?></textarea>
    </div>

    <!-- Head of Auxiliary -->
    <div class="form-group row mb-3">
        <div class="col-sm-6">
            <label for="headOfAuxiliary">Head of Auxiliary Services</label>
            <input name="headOfAuxiliary" required type="text" class="form-control" id="headOfAuxiliary" value="<?php echo isset($headOfAuxiliary) ? $headOfAuxiliary : ''; ?>">
        </div>
        <div class="col-sm-6">
            <label for="dateHead">Date</label>
            <input name="dateHead" required type="datetime-local" class="form-control" id="dateHead" value="<?php echo isset($dateHead) ? $dateHead : ''; ?>">
        </div>
    </div>

    <!-- Maintenance Personnel -->
    <h4 class="card-title borrow-label">ACTION TAKEN</h4>
    <hr>
    <div class="form-group">
        <label for="maintenancePersonnel">Maintenance Personnel</label>
        <input name="maintenancePersonnel" required type="text" class="form-control" id="maintenancePersonnel" value="<?php echo isset($maintenancePersonnel) ? $maintenancePersonnel : ''; ?>">
    </div>

    <!-- Date Started and Date Finished -->
    <div class="form-group row">
        <div class="col-md-6">
            <label for="dateStarted">Date Started</label>
            <input name="dateStarted" required type="datetime-local" class="form-control" id="dateStarted" value="<?php echo isset($dateStarted) ? $dateStarted : ''; ?>" onchange="validateDates()">
        </div>
        <div class="col-md-6">
            <label for="dateFinished">Date Finished</label>
            <input name="dateFinished" required type="datetime-local" class="form-control" id="dateFinished" value="<?php echo isset($dateFinished) ? $dateFinished : ''; ?>" onchange="validateDates()">
        </div>
    </div>

    <!-- Post-Inspection Report -->
    <div class="form-group">
        <label for="postInspectionReport">POST-INSPECTION REPORT</label>
        <textarea name="postInspectionReport" required class="form-control" id="postInspectionReport"><?php echo isset($postInspectionReport) ? $postInspectionReport : ''; ?></textarea>
    </div>

    <!-- Acceptance Status -->
    <br>
    <label for="approvallabelstatus">Approval:</label>
    <hr>
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input required class="form-check-input" type="radio" name="acceptanceStatus" id="accepted" value="accepted" <?php echo (isset($acceptanceStatus) && $acceptanceStatus == 'accepted') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="accepted">ACCEPTED</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="acceptanceStatus" id="rejected" value="rejected" <?php echo (isset($acceptanceStatus) && $acceptanceStatus == 'rejected') ? 'checked' : ''; ?>>
            <label class="form-check-label" for="rejected">REJECTED</label>
        </div>
    </div>

    <!-- End-User -->
    <div class="form-group row">
        <div class="col-md-6">
            <label for="endUser">END-USER</label>
            <input name="endUser" required type="text" class="form-control" id="endUser" value="<?php echo isset($endUser) ? $endUser : ''; ?>">
        </div>
        <div class="col-md-6">
            <label for="dateEndUser">Date</label>
            <input name="dateEndUser" required type="datetime-local" class="form-control" id="dateEndUser" value="<?php echo isset($dateEndUser) ? $dateEndUser : ''; ?>">
        </div>
    </div>

    <div class="submitBtn">
        <button class="borrow-submit" type="submit">Submit</button>
    </div>
</form>

<!-- Success Modal -->
<div class="modal" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (isset($successMessage)) { echo $successMessage; } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Error Modal -->
<div class="modal" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if (isset($errorMessage)) { echo $errorMessage; } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
    function validateDates() {
        const dateStarted = new Date(document.getElementById('dateStarted').value);
        const dateFinished = new Date(document.getElementById('dateFinished').value);
        if (dateFinished < dateStarted) {
            alert('Date Finished cannot be earlier than Date Started.');
            document.getElementById('dateFinished').value = '';
        }
    }
</script>

<script>
    // Show modals based on PHP conditions
    <?php if (isset($successMessage)) { echo 'var successModal = new bootstrap.Modal(document.getElementById("successModal")); successModal.show();'; } ?>
    <?php if (isset($errorMessage)) { echo 'var errorModal = new bootstrap.Modal(document.getElementById("errorModal")); errorModal.show();'; } ?>

    // Form validation function
    function validateForm() {
        // Check if required fields are filled
        var repairStatus = document.querySelector('input[name="repairStatus"]:checked');
        var assessedBy = document.getElementById("assessedBy").value;
        var personnelAssigned = document.getElementById("personnelAssigned").value;

        if (!repairStatus || !assessedBy || !personnelAssigned) {
            alert("Please fill out all required fields.");
            return false;  // Prevent form submission
        }
        return true;
    }
</script>

</body>
</html>
