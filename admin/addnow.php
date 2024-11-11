<?php
require('../dbcred/db.php'); // Include your database connection file

// Start the transaction
mysqli_begin_transaction($db);

try {
    // Update query to add quantities from tbpendingadd to tbproductlist
    $sqladdnow = "
        UPDATE tbproductlist AS p
        JOIN (
            SELECT name, SUM(quantity) AS total_quantity
            FROM tbpendingadd
            GROUP BY name
        ) AS pa ON p.name = pa.name
        SET p.quantity = p.quantity + pa.total_quantity
    ";

    if (!mysqli_query($db, $sqladdnow)) {
        throw new Exception("Error updating product list: " . mysqli_error($db));
    }

    // Check if any rows were affected
    $rows_affected = mysqli_affected_rows($db);

    if ($rows_affected > 0) {
        // Delete data from tbpendingadd if rows were updated
        $delete_query = "DELETE FROM tbpendingadd";
        if (!mysqli_query($db, $delete_query)) {
            throw new Exception("Error deleting pending data: " . mysqli_error($db));
        }

        // Commit the transaction
        mysqli_commit($db);

        // Send a success response
        echo json_encode(['status' => 'success', 'message' => 'Stocks added successfully.']);
    } else {
        // Rollback the transaction if no data was updated
        mysqli_rollback($db);
        echo json_encode(['status' => 'no_data_added', 'message' => 'No data was added.']);
    }
} catch (Exception $e) {
    // Rollback the transaction on error
    mysqli_rollback($db);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Close the database connection
mysqli_close($db);
?>
