<?php
require('dbcred/db.php');



if (isset($_POST['loginupid'])) {
    $sqlverifyuser = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='".$_POST['loginupid']."' ;";
    $verify = mysqli_fetch_assoc(mysqli_query($db, $sqlverifyuser));
    if($verify['pincode'] == $_POST['pincodeborrow']) {
        header("location: borrow.php?userid=".$verify['id']."");
    } else {
        echo "<script>alert('Incorrect pincode');</script>";
    }
}


if (isset($_POST['returnid'])) {
    $sqlverifyuser = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='".$_POST['returnid']."' ;";
    $verify = mysqli_fetch_assoc(mysqli_query($db, $sqlverifyuser));
    if($verify['pincode'] == $_POST['pincodereturn']) {
        header("location: return.php?userid=".$verify['id']."");
    } else {
        echo "<script>alert('Incorrect pincode');</script>";
    }
}



$sqlgetup = "SELECT id,name,pincode,status FROM `tbup` WHERE status ='active' ;";
$listup = mysqli_query($db, $sqlgetup);

$sqlgetupr = "SELECT id,name,pincode,status FROM `tbup` WHERE status ='active' ;";
$listupr = mysqli_query($db, $sqlgetupr);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!--CSS-->
    <link rel="stylesheet" href="css/borrow-items.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!--JS-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="css/borrow-items.css">

    
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/borrow-items.css"> -->
    <title>Auxiliary | Borrow/Request</title>
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


    <div class="card borrow-container mx-auto mt-5 mb-5" style="width: 30rem; height: 36rem; border: 3px solid #0e0e0f; ">
        <h3 class="card-title borrow-label">REQUEST ITEMS (BORROW)</h3>
        <hr>
        <div class="card-body borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col-6">
                    <form method="post"action="">
                    <label>Name:</label><br>
                    <select id="unitDropdown" name="loginupid" class="form-select">
                        <option value="">- Name -</option>
                        <?php while ($data = mysqli_fetch_assoc($listup)) { ?>
                        <option value="<?php echo $data['id']?>"><?php echo $data['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input class="form-control" type="password" class="pinpin" id="pincode"  name="pincodeborrow" inputmode="numeric" min="0" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>



        <div class="submitBtn mx-auto">
            <button type="submit" class="borrow-submit mx-auto">Submit</button>
        </div>
            </form>
        </div>
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;">
    </div>


    <div class="card return-container mx-auto mt-5 mb-5" style="width: 30rem; height: 36rem; border: 3px solid #0e0e0f; display: none; ">
        <h3 class="card-title return-label">REQUEST ITEMS (RETURN)</h3>
        <hr>
        <div class="card-body borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col-6">
                    <label>Name:</label><br>
                    <div class="name-dropdown">
                        <form method="post" action="">
                        <select id="unitDropdown" name="returnid" class="form-select">
                            <option value="">- Name -</option>
                            <?php while ($data = mysqli_fetch_assoc($listupr)) { ?>
                                <option value="<?php echo $data['id']?>"><?php echo $data['name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input class="form-control" type="text" id="pincode" inputmode="numeric" name="pincodereturn" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>


        <div class="submitBtn mx-auto">
            <button type="submit" class="return-submit">Submit</button>
        </div>
            </form>
        </div>

        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;">

    </div>
    <div class="card optionModal mx-auto mb-5" id="optionModal" style="z-index: 2;width: 31rem; height: 20rem; border: 3px solid #0e0e0f; display:none;">
        <!-- <h2 style="position: absolute; left: 90px; top:60px;">Purpose (Borrow or Return)</h2> -->
        <h2 class="mx-auto mt-5">Purpose (Borrow or Return)</h2>
        <div class="card-body modal-content" >
            <div class="row mx-auto mt-5">
                <div class="col-6 btnForBorrow" style="z-index: 3;">
                    <button class="borrow-btn" id="borrow-btn">Borrow</button>
                </div>
                <div class="col-6 btnForReturn">
                    <button class="return-btn" id="return-btn">Return</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/borrow-items.js" type="module"></script>
</body>
</html>