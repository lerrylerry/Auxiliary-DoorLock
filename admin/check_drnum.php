<?php
require('../dbcred/db.php');

// Query to check if drnum exists in the tbdrnum table
$sql = "SELECT drnum FROM tbdrnum WHERE id = 1";
$result = mysqli_query($db, $sql);

// Check if a record exists
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode(array('exists' => true, 'drnum' => $row['drnum']));
} else {
    echo json_encode(array('exists' => false));
}

// Close the database connection
mysqli_close($db);
?>
