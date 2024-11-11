<?php
require('../dbcred/db.php'); // Include your database connection file

// Check if the 'name' parameter is set and not empty
if(isset($_GET['name']) && !empty($_GET['name'])) {
    $productName = $_GET['name'];

    // Perform a database query to retrieve the corresponding data for the selected product name
    $sql = "SELECT unit, category FROM tbproductlist WHERE name = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a matching product was found
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Prepare the data to be returned as JSON
        $response = array(
            'unit' => $row['unit'],
            'category' => $row['category']
        );

        // Send the JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // If no matching product was found, return an empty response
        echo json_encode(array());
    }
} else {
    // If the 'name' parameter is not set or empty, return an empty response
    echo json_encode(array());
}
?>
