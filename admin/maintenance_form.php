<?php
require('../dbcred/db.php');

// Get the token from the URL
if (!isset($_GET['token'])) {
    die("Invalid or missing token.");
}

$token = $_GET['token'];

// Validate the token (check if it exists and matches the assigned name)
$query = "SELECT * FROM `tbminorrepair` WHERE `token` = '$token'";
$result = mysqli_query($db, $query);

if (mysqli_num_rows($result) == 0) {
    die("This link is invalid or has already been used.");
}

// If the token is valid, fetch the repair details
$repair = mysqli_fetch_assoc($result);

// Check if the repair has been assigned to someone (by their name)
if (empty($repair['personnelAssigned'])) {
    die("No employee assigned to this repair request.");
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $maintenance_notes = $_POST['notes'];

    // Update the repair record to indicate that the form has been submitted
    $updateQuery = "UPDATE `tbminorrepair` SET `status` = 'Completed', `notes` = '$maintenance_notes', `token` = NULL WHERE `token` = '$token'";
    
    if (mysqli_query($db, $updateQuery)) {
        // Confirmation message
        echo "Thank you! The repair request has been completed.";
    } else {
        echo "Error updating repair status: " . mysqli_error($db);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Completion Form</title>
</head>
<body>
    <h2>Repair Completion Form</h2>
    <h4>Details:</h4>
    <p><strong>Assigned To:</strong> <?php echo $repair['personnelAssigned']; ?></p>
    <p><strong>Name:</strong> <?php echo $repair['name']; ?></p>
    <p><strong>Position:</strong> <?php echo $repair['position']; ?></p>
    <p><strong>Department:</strong> <?php echo $repair['department']; ?></p>
    <p><strong>Type:</strong> <?php echo $repair['type']; ?></p>
    <p><strong>Serial No:</strong> <?php echo $repair['serial']; ?></p>
    <p><strong>Brand/Model:</strong> <?php echo $repair['brandmodel']; ?></p>
    <p><strong>Property No:</strong> <?php echo $repair['propertyno']; ?></p>
    <p><strong>Acquisition Cost:</strong> <?php echo $repair['acqcost']; ?></p>
    <p><strong>Acquisition Date:</strong> <?php echo $repair['acqdate']; ?></p>
    <p><strong>Scope of Work:</strong> <?php echo $repair['scope']; ?></p>

    <form action="" method="post">
        <label for="notes">Maintenance Notes:</label>
        <textarea name="notes" id="notes" rows="4" cols="50" required></textarea>
        <br><br>
        <button type="submit">Submit Completion</button>
    </form>
</body>
</html>
