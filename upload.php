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

// Debugging: Log message to verify script execution
echo "Script started.\n";

// Check for uploaded video file
if (isset($_FILES['video'])) {
    $video_filename = basename($_FILES['video']['name']);
    $video_file = $video_dir . $video_filename;

    // Move the uploaded video to the directory
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file . "\n";

        // Read the video file as binary data
        $video_data = file_get_contents($video_file);

        // Check if video data is successfully read
        if ($video_data === false) {
            die("Error reading video file.\n");
        }

        echo "Video data read successfully.\n";

        // Check for uploaded thumbnail file
        if (isset($_FILES['thumbnail'])) {
            $thumbnail_filename = basename($_FILES['thumbnail']['name']);
            $thumbnail_file = $thumbnail_dir . $thumbnail_filename;

            // Move the uploaded thumbnail to the directory
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
                echo "Thumbnail uploaded successfully: " . $thumbnail_file . "\n";

                // Read the thumbnail file as binary data
                $thumbnail_data = file_get_contents($thumbnail_file);

                // Check if thumbnail data is successfully read
                if ($thumbnail_data === false) {
                    die("Error reading thumbnail file.\n");
                }

                echo "Thumbnail data read successfully.\n";

                // Escape binary data for safe insertion into database
                $video_data = mysqli_real_escape_string($db, $video_data);
                $thumbnail_data = mysqli_real_escape_string($db, $thumbnail_data);

                echo "Data escaped for database.\n";

                // Prepare SQL query to insert video and thumbnail
                $timestamp = date('Y-m-d H:i:s');  // Current timestamp in DATETIME format
                $sql = "INSERT INTO videos (filename, timestamp, video_data, thumbnail_data) 
                        VALUES ('$video_filename', '$timestamp', '$video_data', '$thumbnail_data')";

                // Debugging: Log the SQL query to check for correctness
                echo "SQL Query: " . $sql . "\n";

                // Execute SQL query to insert data
                if (mysqli_query($db, $sql)) {
                    echo "Video and thumbnail information saved to database.\n";
                } else {
                    echo "Error saving video and thumbnail to database: " . mysqli_error($db) . "\n";
                }
            } else {
                echo "Failed to upload thumbnail.\n";
            }
        } else {
            echo "No thumbnail uploaded.\n";
        }
    } else {
        echo "Failed to upload video.\n";
    }
} else {
    echo "No video uploaded.\n";
}
?>
