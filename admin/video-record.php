<?php
require('../dbcred/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

$videoId = $_GET['id'] ?? null;

if ($videoId) {
    // Fetch video data from the database based on the ID
    $query = "SELECT filename, video_data FROM videos WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $stmt->bind_result($filename, $videoData);

    if ($stmt->fetch()) {
        // Output appropriate headers for the video download
        header("Content-Type: video/mp4");
        header("Content-Length: " . strlen($videoData)); // Adjust if fetching from disk
        header("Content-Disposition: attachment; filename=\"$filename\""); // Force download

        // Output video data
        echo $videoData;
    } else {
        // Video not found
        http_response_code(404);
        echo "Video not found.";
    }

    $stmt->close();
} else {
    // Video ID not provided or invalid
    http_response_code(400);
    echo "Video ID not provided.";
}
?>
