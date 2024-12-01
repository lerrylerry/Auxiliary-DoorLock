<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session to store and retrieve video_id

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

// Initialize video ID in session if it doesn't exist
if (!isset($_SESSION['video_id'])) {
    $_SESSION['video_id'] = null;
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
        $sql = "INSERT INTO videos (filename, timestamp, video_data) 
                VALUES ('$video_filename', '$timestamp', '$video_data')";

        if (mysqli_query($db, $sql)) {
            echo "Video information saved to database.";

            // After saving the video, get the last inserted video ID and store it in the session
            $_SESSION['video_id'] = mysqli_insert_id($db);

            // Print the video ID
            echo "<br>Video ID: " . $_SESSION['video_id'];
        } else {
            echo "Error saving video to database: " . mysqli_error($db);
        }
    } else {
        echo "Failed to upload video.";
    }
}

// Check for uploaded thumbnail file and ensure video ID is available in session
if (isset($_FILES['thumbnail'])) {
    if ($_SESSION['video_id'] === null) {
        echo "Error: No video uploaded. Cannot upload thumbnail without a video ID.";
    } else {
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

            // Ensure the video_id and thumbnail data are valid
            if (!empty($thumbnail_data)) {
                // Using prepared statement to prevent SQL injection
                $stmt = $db->prepare("UPDATE videos SET thumbnail_data = ? WHERE id = ?");
                $stmt->bind_param('si', $thumbnail_data, $_SESSION['video_id']);  // 'si' means string, integer
                if ($stmt->execute()) {
                    echo "Thumbnail information saved to database for video ID: " . $_SESSION['video_id'];
                } else {
                    echo "Error saving thumbnail to database: " . $stmt->error;
                }
            } else {
                echo "Error: Missing thumbnail data.";
            }
        } else {
            echo "Failed to upload thumbnail.";
        }
    }
}

// Optionally, handle errors or provide feedback if no file is uploaded
if (empty($_SESSION['video_id']) && !isset($_FILES['video'])) {
    echo "No video file uploaded.";
}

if (isset($_FILES['thumbnail']) && empty($_SESSION['video_id'])) {
    echo "Error: No video ID found. Ensure the video is uploaded before the thumbnail.";
}
?>
