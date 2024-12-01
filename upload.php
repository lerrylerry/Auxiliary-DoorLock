<?php
require('dbcred/db.php');

// Set the upload directories
$video_dir = "uploads/videos/";
$thumbnail_dir = "uploads/thumbnails/";

// Ensure the directories exist
if (!is_dir($video_dir)) {
    mkdir($video_dir, 0777, true);
}
if (!is_dir($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0777, true);
}

// Check for uploaded files and process them
if (isset($_FILES['video']) && isset($_FILES['thumbnail'])) {
    // Process Video
    $video_file = $video_dir . basename($_FILES['video']['name']);
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;

        // Get video content as BLOB
        $video_content = file_get_contents($video_file);
    } else {
        echo "Failed to upload video.";
        exit;  // Exit if video upload fails
    }

    // Process Thumbnail
    $thumbnail_file = $thumbnail_dir . basename($_FILES['thumbnail']['name']);
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;

        // Get thumbnail content as BLOB
        $thumbnail_content = file_get_contents($thumbnail_file);
    } else {
        echo "Failed to upload thumbnail.";
        exit;  // Exit if thumbnail upload fails
    }

    // Insert data into the database
    $filename = basename($_FILES['video']['name']);
    $timestamp = date('Y-m-d H:i:s');  // Current timestamp

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO videos (filename, timestamp, video_data, thumbnail_data) 
            VALUES (?, ?, ?, ?)";

    // Prepare the statement
    $stmt = mysqli_prepare($db, $sql);
    if ($stmt) {
        // Bind the parameters
        mysqli_stmt_bind_param($stmt, "ssss", $filename, $timestamp, $video_content, $thumbnail_content);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            echo "Video and thumbnail successfully stored in the database.";
        } else {
            echo "Error: " . mysqli_error($db);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($db);
    }
} else {
    echo "Please upload both a video and a thumbnail.";
}
?>
