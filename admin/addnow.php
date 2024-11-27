<?php
require('../dbcred/db.php'); // Include your database connection file

// Start the transaction
mysqli_begin_transaction($db);

try {
    // Step 1: Check if there are any rows in tbpendingadd with a quantity greater than 0
    $check_query = "SELECT COUNT(*) AS count FROM tbpendingadd WHERE quantity > 0";
    $result = mysqli_query($db, $check_query);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] == 0) {
        // If no rows with quantity > 0, rollback and ask the user to add products first
        mysqli_rollback($db);
        echo json_encode(['status' => 'no_data_added', 'message' => 'No products to add. Please add products first.']);
        exit(); // Exit if no data to process
    }

    // Step 2: Blank the 'name' column in 'tbdrnum' where 'id' = 1
    $update_drnum_query = "UPDATE tbdrnum SET name = NULL WHERE id = 1";
    if (!mysqli_query($db, $update_drnum_query)) {
        throw new Exception("Error updating inventory information.");
    }

    // Step 3: Transfer all valid data from 'tbpendingadd' to 'tbprodhis'
    $transfer_query = "
        INSERT INTO tbprodhis (drnum, name, units, quantity, category, addedBy, dateAdded)
        SELECT drnum, name, units, quantity, category, addedBy, dateAdded
        FROM tbpendingadd
        WHERE quantity > 0
    ";
    if (!mysqli_query($db, $transfer_query)) {
        throw new Exception("Error updating product history.");
    }

    // Step 4: Update the product list
    $sqladdnow = "
        UPDATE tbproductlist AS p
        JOIN (
            SELECT name, SUM(quantity) AS total_quantity
            FROM tbpendingadd
            WHERE quantity > 0
            GROUP BY name
        ) AS pa ON p.name = pa.name
        SET p.quantity = p.quantity + pa.total_quantity
    ";

    if (!mysqli_query($db, $sqladdnow)) {
        throw new Exception("Error updating product list.");
    }

    // Check if any rows were affected by the update
    $rows_affected = mysqli_affected_rows($db);

    if ($rows_affected > 0) {
        // Step 5: Clear all rows in tbpendingadd after successful transaction
        $clear_query = "DELETE FROM tbpendingadd";
        if (!mysqli_query($db, $clear_query)) {
            throw new Exception("Error clearing the pending add list.");
        }

        // Commit the transaction if all steps were successful
        mysqli_commit($db);

        // Return user-friendly success message
        echo json_encode([
            'status' => 'success', 
            'message' => 'Products have been successfully added.'
        ]);
    } else {
        // Rollback the transaction if no data was updated
        mysqli_rollback($db);
        echo json_encode(['status' => 'no_data_added', 'message' => 'No valid data to update in the product list.']);
    }

} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($db);
    echo json_encode(['status' => 'error', 'message' => 'Something went wrong. Please try again later.']);
}

// Close the database connection
mysqli_close($db);
?>
