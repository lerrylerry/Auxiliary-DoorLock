<?php
session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Live Viewing</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }

    .container {
      max-width: 1100px;
    }

    .card {
      border: none;
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
    }

    #cameraContainer {
      border: 2px solid #ccc;
      border-radius: 15px;
      overflow: hidden;
      margin-bottom: 20px;
    }

    #camera {
      width: 1000px;
      height: 500px; /* Increased height */
      border: none;
    }

    #toggleButton {
      width: 100%;
      padding: 10px;
      font-size: 18px;
    }

    .bi-camera {
      font-size: 24px;
      margin-right: 10px;
    }
  </style>
</head>
<body>
<?php include('static/sidebar.php')?>
<section class="home-section" style-"overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
<div class="container mt-5">
  <div class="text-center">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">CCTV System</h5>
        <div id="cameraContainer" class="mb-3">
          <iframe id="camera" src="http://<?php echo $_SERVER['SERVER_ADDR'];?>:5080/" width="100%" height="240" frameborder="0">

          </iframe>
        </div>
        <button id="toggleButton" class="btn btn-primary" onclick="toggleCamera()">
          <i class="bi bi-camera"></i>
          <span id="toggleButtonText">Turn On Camera</span>
        </button>
      </div>
    </div>
  </div>
</div>
</section>
<script>
  var cameraOn = false;
  var cameraUrl = "http://<?php echo $_SERVER['SERVER_ADDR'];?>:5080/"; // Replace with your actual camera stream URL
  var cameraContainer = document.getElementById("cameraContainer");

  window.onload = function() {
     cameraContainer.style.display = 'none';
  };

  function toggleCamera() {
    var cameraContainer = document.getElementById("cameraContainer");
    var toggleButton = document.getElementById("toggleButton");
    var toggleButtonText = document.getElementById("toggleButtonText");

    if (cameraOn) {
      // Turn off the camera
      cameraContainer.style.display = "none";
      toggleButton.classList.remove("btn-danger");
      toggleButton.classList.add("btn-primary");
      toggleButtonText.textContent = "Turn On Camera";
      document.getElementById("camera").src = ""; // Clear the iframe src
    } else {
      // Turn on the camera
      cameraContainer.style.display = "block";
      toggleButton.classList.remove("btn-primary");
      toggleButton.classList.add("btn-danger");
      toggleButtonText.textContent = "Turn Off Camera";
      document.getElementById("camera").src = cameraUrl; // Set the iframe src to the camera URL
    }
    cameraOn = !cameraOn;
  }
</script>
<script src="/admin/static/script.js"></script>
<!-- Bootstrap Icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.1/bootstrap-icons.min.js"></script>

</body>
</html>
