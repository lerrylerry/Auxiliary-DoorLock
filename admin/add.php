<?php
require('../dbcred/db.php'); // Include your database connection file

// Check if the 'add' parameter is set in the POST request
if (isset($_POST['name'], $_POST['units'], $_POST['quantity'], $_POST['category'])) {
    // Sanitize the input to prevent SQL injection
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $units = mysqli_real_escape_string($db, $_POST['units']);
    $quantity = mysqli_real_escape_string($db, $_POST['quantity']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    
    // Construct the SQL query with prepared statements
    $sqlinsert = "INSERT INTO `tbpendingadd` (`name`, `units`, `quantity`, `category`) VALUES (?, ?, ?, ?)";
    
    // Prepare the statement
    if ($stmt = mysqli_prepare($db, $sqlinsert)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "ssis", $name, $units, $quantity, $category);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // If the adding is successful, send a success response along with the inserted item's ID
            // After successful insertion
            $inserted_id = mysqli_insert_id($db); // Get the ID of the last inserted row
            echo json_encode(array("message" => "Data inserted successfully", "id" => $inserted_id));
        } else {
            // If an error occurs during insertion, send an error response
            echo "Error adding row: " . mysqli_error($db);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        // If the prepared statement fails, send an error response
        echo "Error preparing statement: " . mysqli_error($db);
    }
} else {
    // If the required parameters are not set, send a response indicating invalid request
    echo "Invalid request";
}

// Close the database connection (optional)
mysqli_close($db);
?>
