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

// Get the highest id from the database (last inserted video ID) and add 1
$sql = "SELECT MAX(id) AS highest_id FROM videos";
$result = mysqli_query($db, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $video_id = $row['highest_id'];  // Get the highest id from the database
    // If no records exist, set the video_id to 1
    $video_id = ($video_id !== null) ? $video_id : 1;
} else {
    die("Error fetching the highest video ID: " . mysqli_error($db));
}

// Check for uploaded video file
if (isset($_FILES['video'])) {
    $video_filename = basename($_FILES['video']['name']);
    $video_file = $video_dir . $video_filename;

    // Move the uploaded video to the directory
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;

        // Insert video filename, timestamp, and video binary data into the database
        $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format
        $video_data = file_get_contents($video_file);

        // Check if video data is successfully read
        if ($video_data === false) {
            die("Error reading video file.");
        }

        // Escape video binary data for safe insertion into the database
        $video_data = mysqli_real_escape_string($db, $video_data);

        // Insert video record into the database
        $sql = "INSERT INTO videos (filename, timestamp, video_data) 
                VALUES ('$video_filename', '$timestamp', '$video_data')";

        if (mysqli_query($db, $sql)) {
            echo "Video information saved to database.";

            // Delete the video file after saving to the database
            if (unlink($video_file)) {
                echo "Video file deleted from server.";
            } else {
                echo "Failed to delete video file from server.";
            }
        } else {
            echo "Error saving video to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload video.";
        exit; // Stop the script if the video upload fails
    }
}

// Check for uploaded thumbnail file and ensure video ID is available
if (isset($_FILES['thumbnail'])) {
    $thumbnail_filename = basename($_FILES['thumbnail']['name']);
    $thumbnail_file = $thumbnail_dir . $thumbnail_filename;

    // Move the uploaded thumbnail to the directory
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;

        // Read the thumbnail file as binary data
        $thumbnail_data = file_get_contents($thumbnail_file);

        // Check if thumbnail data is successfully read
        if ($thumbnail_data === false) {
            die("Error reading thumbnail file.");
        }

        // Escape thumbnail binary data for safe insertion into SQL query
        $thumbnail_data = mysqli_real_escape_string($db, $thumbnail_data);

        // Update the video record with the thumbnail data
        $sql = "UPDATE videos SET thumbnail_data = '$thumbnail_data' WHERE id = $video_id";

        if (mysqli_query($db, $sql)) {
            echo "Thumbnail information saved to database for video ID: " . $video_id;

            // Delete the thumbnail file after saving to the database
            if (unlink($thumbnail_file)) {
                echo "Thumbnail file deleted from server.";
            } else {
                echo "Failed to delete thumbnail file from server.";
            }
        } else {
            echo "Error saving thumbnail to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>
