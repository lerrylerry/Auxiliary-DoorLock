<?php
require('dbcred/db.php');
//db 12-16 must be null

if (isset($_POST['name'])) {
    $sqlinsert = "INSERT INTO `tbrequestvehicle`(`name`, `email`, `date`, `destination`, `passengercount`, `purpose`) VALUES ('" . $_POST['name'] . "','" . $_POST['email'] . "','" . $_POST['date'] . "','" . $_POST['destination'] . "','" . $_POST['passengers'] . "','" . $_POST['purpose'] . "')";
    mysqli_query($db, $sqlinsert);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--CSS-->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"


    <script src=""></script>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    
    <!--JS-->
    <!--Optional-->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
    <link rel="stylesheet" href="css/repair.css">
    <title>Auxiliary | Vehicle Reservation</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark ">
        <div class="container-fluid">
          <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="" style="width: 60px; height: 60px;">
            <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
          </div>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list"></i>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="dropdown">
                <button class="btn dropdown-toggle serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                  Services
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" value="borrowANDreturn.php">Borrow/Get Items</a></li>
                  <li><a class="dropdown-item" value="repair - userTOadmin.php">Request a minor repair</a></li>
                  <li><a class="dropdown-item" value="vehicle-reservation.php">Request for vehicle</a></li>
                </ul>
            </div> 
            <div class="d-flex loginBtn">
              <button class="btn btn-outline-success" type="button" id="login">Login</button>
            </div>
          </div>
        </div>
      </nav>

      
    <form class="card repair-container" action="" method="post" style="width: 40rem; height: 55rem; border: 3px solid #0e0e0f; padding-bottom: 50px; ">
        <h3 class="card-title borrow-label">VEHICLE RESERVATION</h3>
        <hr>
        <div class="borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col">
                    <label>Name:</label><br>
                    <input type="text" id="name" name="name" placeholder="Enter name" style="height: 30px;">
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <label>Email:</label><br>
                    <input type="email" id="email" name="email" placeholder="Enter email" style="height: 30px;">
                </div>
                <div class="col-5">
                    <label>Date:</label><br>
                    <input type="date" id="date" name="date" style="height: 30px;">
                </div>
            </div>
            <div class="row">
                <div class="col-7">
                    <label for="">Destination:</label><br>
                    <input type="text" id="destination" name="destination" placeholder="Enter destination" style="height: 30px;">
                </div>
                <div class="col-5">
                    <label for="">No. of Passengers:</label><br>
                    <input type="number" id="passengers" name="passengers" placeholder="ex: 3" style="height: 30px;" min="1" max="10">
                </div>
            </div>
        </div>
        
        <div class="dnc">
            <label>Purpose:</label><br>
            <textarea id="purpose" name="purpose" placeholder="Enter message" style="height: 140px;"></textarea>
        </div>
        <div class="submitBtn">
            <button class="borrow-submit">Submit</button>
        </div>
    </form>
        
        
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 30rem; width: 30rem; opacity: 0.1;">
    </div>


</body>
<script src="js/index.js" type="module"></script>
<script>
    document.getElementById('#passengers').addEventListener('change', function() {
        var inputValue = parseInt(this.value);
        if (inputValue > 10 || isNaN(inputValue)) {
            this.value = 10;
        }
    });
</script>    
</html>