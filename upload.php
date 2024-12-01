<?php
require('../dbcred/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

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

// Check for video upload
if (isset($_FILES['video'])) {
    $video_file = $video_dir . basename($_FILES['video']['name']);
    
    // Move the uploaded video file
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        // Save video details to the database
        $filename = basename($_FILES['video']['name']);
        $timestamp = date('Y-m-d H:i:s');  // Current timestamp

        $sql = "INSERT INTO videos (filename, timestamp) VALUES ('$filename', '$timestamp')";
        if (mysqli_query($db, $sql)) {
            echo "Video uploaded successfully and saved in the database: " . $video_file;
        } else {
            echo "Error saving video details to the database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload video.";
    }
}

// Check for thumbnail upload
if (isset($_FILES['thumbnail'])) {
    $thumbnail_file = $thumbnail_dir . basename($_FILES['thumbnail']['name']);
    
    // Move the uploaded thumbnail file
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>
