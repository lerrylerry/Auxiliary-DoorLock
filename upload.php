<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('dbcred/db.php');  // Make sure to include the database credentials

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

// Check for uploaded video file
if (isset($_FILES['video'])) {
    $video_filename = basename($_FILES['video']['name']);
    $video_file = $video_dir . $video_filename;
    
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;

        // Insert video filename and timestamp into the database
        $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format
        $sql = "INSERT INTO videos (filename, timestamp) 
        VALUES ('$video_filename', '$timestamp')";
        
        if (mysqli_query($db, $sql)) {
            echo "Video information saved to database.";
        } else {
            echo "Error saving video to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload video.";
    }
}

// Check for uploaded thumbnail file
if (isset($_FILES['thumbnail'])) {
    $thumbnail_filename = basename($_FILES['thumbnail']['name']);
    $thumbnail_file = $thumbnail_dir . $thumbnail_filename;
    
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>