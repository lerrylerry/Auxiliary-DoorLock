<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

require('dbcred/db.php');  // Include the database credentials

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

// Initialize video ID
$video_id = null;

// Check for uploaded video file and thumbnail
if (isset($_FILES['video']) && isset($_FILES['thumbnail'])) {
    // Video file handling
    $video_filename = basename($_FILES['video']['name']);
    $video_file = $video_dir . $video_filename;

    // Thumbnail file handling
    $thumbnail_filename = basename($_FILES['thumbnail']['name']);
    $thumbnail_file = $thumbnail_dir . $thumbnail_filename;

    // Check for errors in the file upload process
    if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading video: " . $_FILES['video']['error']);
    }
    if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) {
        die("Error uploading thumbnail: " . $_FILES['thumbnail']['error']);
    }

    // Move the uploaded files to their respective directories
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;
    } else {
        die("Failed to upload video.");
    }

    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;
    } else {
        die("Failed to upload thumbnail.");
    }

    // Read video and thumbnail files as binary data
    $video_data = file_get_contents($video_file);
    $thumbnail_data = file_get_contents($thumbnail_file);

    // Check if video and thumbnail data were successfully read
    if ($video_data === false) {
        die("Error reading video file.");
    }
    if ($thumbnail_data === false) {
        die("Error reading thumbnail file.");
    }

    // Escape the binary data for safe insertion into the database
    $video_data = mysqli_real_escape_string($db, $video_data);
    $thumbnail_data = mysqli_real_escape_string($db, $thumbnail_data);

    // Insert video filename, timestamp, and video and thumbnail binary data into the database
    $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format
    $sql = "INSERT INTO videos (filename, timestamp, video_data, thumbnail_data) 
            VALUES ('$video_filename', '$timestamp', '$video_data', '$thumbnail_data')";

    if (mysqli_query($db, $sql)) {
        echo "Video information saved to database.";

        // After saving the video, get the last inserted video ID
        $video_id = mysqli_insert_id($db);

        // Print the video ID
        echo "<br>Video ID: " . $video_id;
    } else {
        echo "Error saving video to database: " . mysqli_error($db);
    }
} else {
    echo "Please upload both a video and a thumbnail.";
}
?>
