<?php
require('dbcred/db.php');  // Include database credentials

echo '<pre>';
print_r($_FILES);
echo '</pre>';
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
if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
    $video_filename = basename($_FILES['video']['name']);
    $video_file = $video_dir . $video_filename;
    $video_data = file_get_contents($_FILES['video']['tmp_name']); // Read binary data

    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;

        // Check for uploaded thumbnail file
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $thumbnail_filename = basename($_FILES['thumbnail']['name']);
            $thumbnail_file = $thumbnail_dir . $thumbnail_filename;
            $thumbnail_data = file_get_contents($_FILES['thumbnail']['tmp_name']); // Read binary data

            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
                echo "Thumbnail uploaded successfully: " . $thumbnail_file;

                // Insert video and thumbnail into the database
                $timestamp = date('Y-m-d H:i:s');
                $stmt = $db->prepare("INSERT INTO videos (filename, timestamp, video_data, thumbnail_data) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sbbs", $video_filename, $timestamp, $video_data, $thumbnail_data);


                if ($stmt->execute()) {
                    echo "Video and thumbnail saved to database.";
                } else {
                    echo "Error saving to database: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Failed to upload thumbnail.";
            }
        } else {
            echo "No thumbnail uploaded.";
        }
    } else {
        echo "Failed to upload video.";
    }
} else {
    echo "No video file uploaded.";
}
?>
