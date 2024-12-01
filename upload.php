<?php
require('../dbcred/db.php');  // Ensure your database credentials are correct

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

        // Get video file data
        $video_data = file_get_contents($video_file);  // Read video file contents
        $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format

        // Prepare SQL query for video insert
        $sql = "INSERT INTO `videos` (`filename`, `timestamp`, `video_data`, `thumbnail_data`) 
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($db, $sql);

        // Bind parameters (s for strings, b for binary data)
        mysqli_stmt_bind_param($stmt, 'ssbb', $video_filename, $timestamp, $video_data, $thumbnail_data);

        if (mysqli_stmt_execute($stmt)) {
            echo "Video information saved to database.";
        } else {
            echo "Error saving video to database: " . mysqli_error($db);
        }

        mysqli_stmt_close($stmt);
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

        // Get thumbnail file data
        $thumbnail_data = file_get_contents($thumbnail_file);  // Read thumbnail file contents
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>
