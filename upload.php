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
    // Add 1 to the highest ID for the next video ID
    $video_id = ($video_id !== null) ? $video_id : 1;  // If no records exist, start with 1
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

        // Read the video file as binary data
        $video_data = file_get_contents($video_file);

        // Check if video data is successfully read
        if ($video_data === false) {
            die("Error reading video file.");
        }

        // Insert video filename, timestamp, and video binary data into the database
        $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format
        $video_data = mysqli_real_escape_string($db, $video_data);  // Escape binary data for safe insertion
        $sql = "INSERT INTO videos (id, filename, timestamp, video_data) 
                VALUES ('$video_id', '$video_filename', '$timestamp', '$video_data')";

        if (mysqli_query($db, $sql)) {
            echo "Video information saved to database.";

            // After saving the video, you can still update the video_id if needed
        } else {
            echo "Error saving video to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload video.";
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
        $sql = "UPDATE videos SET thumbnail_data = '$thumbnail_data' WHERE id = $video_id ";

        if (mysqli_query($db, $sql)) {
            echo "Thumbnail information saved to database for video ID: " . $video_id ;
        } else {
            echo "Error saving thumbnail to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>
