<?php
require('../dbcred/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
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

// Fetch video records
$sqlgetvideos = "SELECT id, filename, timestamp FROM `videos`;";
$listvideos = mysqli_query($db, $sqlgetvideos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Recorded Motion</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Video.js CSS -->
  <link href="https://vjs.zencdn.net/7.14.3/video-js.css" rel="stylesheet" />
  <style>
    /* Grid Layout: 4 thumbnails per row */
    .thumbnail-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);  /* 4 items per row */
        gap: 20px;  /* Space between thumbnails */
        margin-top: 20px;
    }

    .thumbnail-item {
        position: relative;
        text-align: center;
        background-color: #f7f7f7;  /* Add a background for better visibility */
        padding: 10px;
        border-radius: 5px;  /* Rounded corners */
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); /* Add some shadow for better look */
        overflow: hidden;
        height: 300px;  /* Fixed height for the thumbnail item */
    }

    .thumbnail-item img {
        width: 100%;
        height: auto;
        object-fit: cover;  /* Ensures the image covers the area without distortion */
    }

    .thumbnail-item .id, .thumbnail-item .timestamp {
        font-size: 16px;  /* Same font size for both ID and Timestamp */
        font-weight: bold;  /* Make text bold */
        color: black;  /* Set the font color to black */
        margin-top: 10px;
    }

    .thumbnail-item .timestamp {
        font-family: 'Arial', sans-serif; /* Use Arial font for timestamp */
    }

    /* Responsive design for smaller screens */
    @media (max-width: 1200px) {
        .thumbnail-grid {
            grid-template-columns: repeat(3, 1fr);  /* 3 items per row for medium screens */
        }
    }

    @media (max-width: 768px) {
        .thumbnail-grid {
            grid-template-columns: repeat(2, 1fr);  /* 2 items per row for small screens */
        }
    }

    @media (max-width: 576px) {
        .thumbnail-grid {
            grid-template-columns: 1fr;  /* 1 item per row for extra small screens */
        }
    }
  </style>
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
                // Define paths for video and thumbnail
                $videoPath = '/Auxiliary-DoorLock/recorded_videos/' . $video['filename'];  // Use relative path to video
                $thumbnailPath = '/Auxiliary-DoorLock/thumbnails/' . pathinfo($video['filename'], PATHINFO_FILENAME) . '.jpg';  // Use relative path to thumbnail

                // Generate thumbnail if not exists
                generateThumbnail($_SERVER['DOCUMENT_ROOT'] . $videoPath, $_SERVER['DOCUMENT_ROOT'] . $thumbnailPath); // Generate thumbnail with full path
            ?>
                <div class="thumbnail-item">
                    <!-- Change the link behavior to download the video -->
                    <a href="<?php echo $videoPath; ?>" download>
                        <img src="<?php echo $thumbnailPath; ?>" alt="Video Thumbnail" class="thumbnail-large">
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
<script>
  $(document).ready(function() {
    // Check window size and disable toggle functionality for smaller screens
    if ($(window).width() <= 768) {
        // If the window is mobile-sized, disable the open/close functionality
        $(".sidebar").removeClass("close");
    }

    // Add your sidebar toggle functionality here for larger screens if needed
    $(window).resize(function() {
        if ($(window).width() <= 768) {
            $(".sidebar").removeClass("close");
        }
    });
});

</script>
</body>
</html>
