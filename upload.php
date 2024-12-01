<?php
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

// Check for uploaded files
if (isset($_FILES['video'])) {
    $video_file = $video_dir . basename($_FILES['video']['name']);
    if (move_uploaded_file($_FILES['video']['tmp_name'], $video_file)) {
        echo "Video uploaded successfully: " . $video_file;
    } else {
        echo "Failed to upload video.";
    }
}

if (isset($_FILES['thumbnail'])) {
    $thumbnail_file = $thumbnail_dir . basename($_FILES['thumbnail']['name']);
    if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $thumbnail_file)) {
        echo "Thumbnail uploaded successfully: " . $thumbnail_file;
    } else {
        echo "Failed to upload thumbnail.";
    }
}
?>
