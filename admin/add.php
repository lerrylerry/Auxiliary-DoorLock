<?php
require('../dbcred/db.php'); // Include your database connection file

// Check if the 'drnum', 'name', 'units', 'quantity', 'category', 'addedBy', and 'dateAdded' parameters are set in the POST request
if (isset($_POST['drnum'], $_POST['name'], $_POST['units'], $_POST['quantity'], $_POST['category'], $_POST['addedBy'], $_POST['dateAdded'])) {
    // Sanitize the input to prevent SQL injection
    $drnum = mysqli_real_escape_string($db, $_POST['drnum']);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $units = mysqli_real_escape_string($db, $_POST['units']);
    $quantity = mysqli_real_escape_string($db, $_POST['quantity']);
    $category = mysqli_real_escape_string($db, $_POST['category']);
    $addedBy = mysqli_real_escape_string($db, $_POST['addedBy']);
    $dateAdded = mysqli_real_escape_string($db, $_POST['dateAdded']);
    
    // Construct the SQL query to insert into the `tbpendingadd` table
    $sqlinsert = "INSERT INTO `tbpendingadd` (`drnum`, `name`, `units`, `quantity`, `category`, `addedBy`, `dateAdded`) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare the query
    if ($stmt = mysqli_prepare($db, $sqlinsert)) {
        mysqli_stmt_bind_param($stmt, "sssssss", $drnum, $name, $units, $quantity, $category, $addedBy, $dateAdded);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Get the last inserted ID
            $lastInsertedId = mysqli_insert_id($db);
            
            // Update the `tbdrnum` table, setting the `name` column to the value of `drnum`
            $sqlupdate = "UPDATE `tbdrnum` SET `name` = ? WHERE `id` = 1";  // Assuming you're updating the row with id = 1
            
            if ($stmtUpdate = mysqli_prepare($db, $sqlupdate)) {
                mysqli_stmt_bind_param($stmtUpdate, "s", $drnum);  // Bind the 'drnum' to the query
                
                // Execute the update statement
                if (mysqli_stmt_execute($stmtUpdate)) {
                    // Send a JSON response back with the ID of the inserted row
                    echo json_encode(['id' => $lastInsertedId, 'name' => $name, 'units' => $units, 'quantity' => $quantity, 'category' => $category]);
                }
            }
        } else {
            echo json_encode(['message' => 'Error inserting data']);
        }
        
        // Close the insert statement
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['message' => 'Error preparing insert statement']);
    }
} else {
    echo json_encode(['message' => 'Missing required fields']);
}

// Close the database connection
mysqli_close($db);
?>
