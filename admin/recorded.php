
<?php
require('../dbcred/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

// Function to generate thumbnails
function generateThumbnail($videoPath, $thumbnailPath) {
    // Ensure the thumbnails directory exists
    if (!is_dir(dirname($thumbnailPath))) {
        mkdir(dirname($thumbnailPath), 0777, true);  // Create the directory with write permissions
    }

    // Check if the thumbnail already exists
    if (!file_exists($thumbnailPath)) {
        // Full path to ffmpeg executable (adjust this if necessary)
        $ffmpegPath = 'C:/ffmpeg-7.1-essentials_build/bin/ffmpeg.exe'; // Correct FFmpeg path
        $command = "\"$ffmpegPath\" -i \"$videoPath\" -ss 00:00:01.000 -vframes 1 \"$thumbnailPath\"";
        
        exec($command, $output, $return_var);

        // Check if the command was successful
        if ($return_var !== 0) {
            // Log the error for troubleshooting
            file_put_contents("ffmpeg_errors.log", "Error generating thumbnail for: $videoPath\n" . implode("\n", $output) . "\n", FILE_APPEND);
            return false; // Failed to generate thumbnail
        }
    }
    return true; // Thumbnail exists or was successfully created
}

// Fetch video records from the database
$sqlgetvideos = "SELECT id, filename, timestamp, video_data, thumbnail_data FROM `videos`;";
$listvideos = mysqli_query($db, $sqlgetvideos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Recorded Motion</title>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Video.js CSS -->
  <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/admin-section.css">
</head>
<body>
<?php include('static/sidebar.php')?> 

<section class="home-section" style="overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
    <div class="container mt-5">
        <h2 class="mt-5">Recorded Videos</h2>
        <?php if (mysqli_num_rows($listvideos) > 0) { ?>
            <div class="thumbnail-grid">
            <?php 
            // Loop through video records
            while ($video = mysqli_fetch_assoc($listvideos)) { 
                // Get the video and thumbnail binary data
                $videoData = $video['video_data'];
                $thumbnailData = $video['thumbnail_data'];

                // Encode binary data to base64 for display
                $videoBase64 = base64_encode($videoData);
                $thumbnailBase64 = base64_encode($thumbnailData);
            ?>
                <div class="thumbnail-item">
                    <!-- Change the link behavior to download the video -->
                    <a href="data:video/mp4;base64,<?php echo $videoBase64; ?>" download="<?php echo $video['filename']; ?>">
                        <img src="data:image/jpeg;base64,<?php echo $thumbnailBase64; ?>" alt="Video Thumbnail" class="thumbnail-large">
                    </a>
                    <p class="id"><?php echo $video['id']; ?>. <span class="timestamp"><?php echo date("F j, Y g:i A", strtotime($video['timestamp'])); ?></span></p>
                </div>
            <?php } ?>
            </div>
        <?php } else { ?>
            <p>No recorded videos available.</p>
        <?php } ?>
    </div>
</section>

<!-- Bootstrap JS (jQuery is required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="static/script.js"></script>

</body>
</html>
