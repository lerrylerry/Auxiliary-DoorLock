<?php
require('dbcred/db.php');
//db 12-16 must be null

if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
}

if (isset($_POST['name'])) {
    $sqlinsert = "INSERT INTO `tbminorrepair`(`name`, `position`, `department`, `email`, `type`, `serial`, `brandmodel`, `propertyno`, `acqdate`, `acqcost`, `scope`) VALUES ('" . $_POST['name'] . "','" . $_POST['position'] . "','" . $_POST['department'] . "','" . $_POST['email'] . "','" . $_POST['type'] . "','" . $_POST['serial'] . "','" . $_POST['model'] . "','" . $_POST['propertyno'] . "','" . $_POST['acqusitionDate'] . "','" . $_POST['acqusitionCost'] . "','" . $_POST['message'] . "')";
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
    <title>Auxiliary | Repair</title>
</head>
<body>

<nav class="navbar navbar-expand-md bg-dark">
      <div class="container-fluid">
        <div class="navLogo d-flex">
          <img src="images/TUP-LOGO-modified.png" alt="" style="width: 55px; height: 55px;">
          <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
        </div>
        <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="navdiv d-flex justify-content-center w-100">
            <div class="dropdown mx-auto">
              <button class="btn dropdown-toggle text-white serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Services
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" value="borrowANDreturn.php">Borrow/Get Items</a></li>
                <li><a class="dropdown-item" value="minor-repair.php">Request a minor repair</a></li>
              </ul>
            </div>
            <div class="divLogin d-flex justify-content-center">
              <a href="login.php" class="loginBtn" style="text-decoration: none;">
                <button class="btn btn-outline-success" type="button" id="login">Login</button>
              </a>
            </div>
          </div>
        </div>
      </div>
    </nav>

<form action="" method="post">
    <div class="card container my-3"
         style="width: 40rem; height: 55rem; border: 3px solid #0e0e0f; padding-bottom: 50px; ">
        <h4 class="card-title borrow-label">REQUEST BY</h4>
        <hr>
        <div class="borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col-7">
                    <label>Name:</label><br>
                    <input type="text" id="name" name="name" placeholder="Enter name" required autocomplete="off">
                </div>
                <div class="col-5">
                    <label>Position:</label><br>
                    <input type="text" id="position" name="position" placeholder="Enter position" required autocomplete="off">
                </div>
            </div>
            <div class="depmail">
                <label for="">Department:</label>
                <div class="col-12">
                    <input type="text" id="department" name="department" placeholder="Enter department" style="height: 30px;" required autocomplete="off">
                </div>
                <label for="">Email:</label>
                <div class="col-12">
                    <input type="text" id="email" name="email" placeholder="Enter email" style="height: 30px;" required autocomplete="off">
                </div>
            </div>
        </div>
        <h4 class="card-title desc-prop-label">DESCRIPTION OF PROPERTY</h4>
        <hr style="position: relative; top: 140px;">
        <div class="desc-property" style="z-index: 2;">
            <div class="row">
                <div class="col-6">
                    <label>Type:</label><br>
                    <input type="text" id="type" name="type"  placeholder="Enter type" required autocomplete="off">
                </div>
                <div class="col-6">
                    <label>Brand Model:</label><br>
                    <input type="text" id="model" name="model" placeholder="Enter brand model" required autocomplete="off">
                </div>
            </div>
        </div>
        <div class="desc-property" style="z-index: 2;">
            <div class="row">
                <div class="col-6">
                    <label>Serial No.:</label><br>
                    <input type="text" id="serial" name="serial"  placeholder="Enter serial" required autocomplete="off">
                </div>
                <div class="col-6">
                    <label>Property No.:</label><br>
                    <input type="text" id="property" name="propertyno" placeholder="Enter property number" required autocomplete="off">
                </div>
            </div>
        </div>
        <div class="desc-property" style="z-index: 2;">
            <div class="row">
                <div class="col-6">
                    <label>Acquisition Date:</label><br>
                    <input type="date" id="acqusitionDate" name="acqusitionDate" placeholder="Enter acqusition date" required autocomplete="off">
                </div>
                <div class="col-6">
                    <label>Acquisition Cost:</label><br>
                    <input type="text" id="acqusitionCost" name="acqusitionCost" placeholder="Enter acqusition cost" required autocomplete="off">
                </div>
            </div>
        </div>
        <h4 class="card-title desc-prop-label">DEFECTS AND COMPLAINTS</h4>
        <hr style="position: relative; top: 140px;">
        <div class="dnc">
            <label>Nature and Scope of Works to be done:</label><br>
            <textarea id="message" name="message" placeholder="Explanation message" style="height: 140px;" required autocomplete="off"></textarea>
        </div>
        <div class="submitBtn">
            <button class="borrow-submit">Submit</button>
        </div>
</form>


<img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 30rem; width: 30rem; opacity: 0.1;">


</body>
<script src="js/index.js" type="module"></script>
</html>