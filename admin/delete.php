<?php
require('../dbcred/db.php'); // Include your database connection file

// Check if the 'delete' parameter is set in the POST request
if (isset($_POST['delete'])) {
    // Sanitize the input to prevent SQL injection
    $id = mysqli_real_escape_string($db, $_POST['delete']);
    
    // Construct the SQL query to delete the row with the specified ID
    $sqldeleteitem = "DELETE FROM `tbpendingadd` WHERE id='$id'";
    
    // Execute the query
    if (mysqli_query($db, $sqldeleteitem)) {
        // If the deletion is successful, send a success response
        echo "Row deleted successfully";
    } else {
        // If an error occurs during deletion, send an error response
        echo "Error deleting row: " . mysqli_error($db);
    }
} else {
    // If the 'delete' parameter is not set, send a response indicating invalid request
    echo "Invalid request";
}


// Close the database connection (optional)
mysqli_close($db);
?>
