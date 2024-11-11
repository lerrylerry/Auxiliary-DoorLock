<?php
require('dbcred/db.php');

if (isset($_POST['productName'])) {
    $sqlinsertp = "INSERT INTO `tbproductlist`(`name`, `unit`,`quantity`) VALUES ('" . $_POST['productName'] . "','" . $_POST['unit'] . "','" . $_POST['qty'] . "')";
    mysqli_query($db, $sqlinsertp);
}

if (isset($_POST['updatepid'])) {
    $sqlupdateup = "UPDATE `tbproductlist` SET `name`='".$_POST['updatename']."',`unit` ='".$_POST['updateunit']."',`quantity` ='".$_POST['updateqty']."' WHERE id='" . $_POST['updatepid'] . "'";
    mysqli_query($db, $sqlupdateup);
}


if (isset($_POST['deleteid'])) {
    $sqldeleteitem= "DELETE FROM `tbproductlist` WHERE id='" . $_POST['deleteid'] . "'";
    mysqli_query($db, $sqldeleteitem);
}

$sqlgetup = "SELECT id,name,pincode,status FROM `tbup` WHERE status ='active' ;";
$listup = mysqli_query($db, $sqlgetup);

?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/borrow-items.css">
    <title>Auxiliary | Borrow/Request</title>
<!-- </head>
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
                <li><a class="dropdown-item" value="repair - userTOadmin.php">Request a minor repair</a></li>
                <li><a class="dropdown-item" value="vehicle-reservation.php">Request for vehicle</a></li>
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

      
    <div class="card borrow-container" style="width: 30rem; height: 36rem; border: 3px solid #0e0e0f; ">
        <h3 class="card-title borrow-label">REQUEST ITEMS (BORROW)</h3>
        <hr>
        <div class="card-body borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col-6">
                    <label>Name:</label><br>
                    <select id="unitDropdown" name="updateunit" class="form-select">
                        <option value="">- Name -</option>
                        <?php while ($data = mysqli_fetch_assoc($listup)) { ?>
                        <option value="<?php echo $data['name']?>"><?php echo $data['name']?></option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input type="text" id="pincode" inputmode="numeric" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>
        </div>
        <hr style="position: relative; top: 60px;">
        <div class="borrow-table">
            <table class="table" style="margin-top: 60px;">
                <thead style="text-align: center;">
                  <tr>
                    <th scope="col">Items</th>
                    <th scope="col">Available</th>
                    <th scope="col">Borrowing Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Soap</td>
                    <td>3pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Alcohol</td>
                    <td>3gal</td>
                    <td>1gal</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Rags</td>
                    <td>10pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                
                  <!-- <tr>
                    <th scope="row">Item 1</th>
                    <td>4 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 2</th>
                    <td>5 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 3</th>
                    <td>10pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 4</th>
                    <td>12 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr> -->
                </tbody>
              </table>
              <div class="row">
                <div class="col-4">
                  <label>Items:</label><br>
                  <div class="name-dropdown">
                      <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                        -Select here-
                      </button>
                      <ul class="dropdown-menu" style="width: 44%;">
                        <li><a class="dropdown-item" href="#">Rags</a></li>
                        <li><a class="dropdown-item" href="#">Soap</a></li>
                        <li><a class="dropdown-item" href="#">Alcohol</a></li>
                      </ul>
                  </div>
                </div>
                <div class="col-4">
                  <label>Quantity:</label><br>
                  <input type="number" id="quantity" placeholder="Stock left: 1 pcs.">
                </div>
                <div class="col-4">
                  <br>
                  <button id="add" class="btn btn-danger">Add</button>
                </div>
              </div>
        </div>
        <div class="submitBtn">
            <button class="borrow-submit">Submit</button>
        </div>
        
        
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;">
    </div>


    <div class="card return-container" style="width: 30rem; height: 36rem; border: 3px solid #0e0e0f; display: none; ">
        <h3 class="card-title return-label">REQUEST ITEMS (RETURN)</h3>
        <hr>
        <div class="card-body borrow-content" style="z-index: 1; height: 10px;">
            <div class="row">
                <div class="col-6">
                    <label>Name:</label><br>
                    <div class="name-dropdown">
                        <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                          -Name-
                        </button>
                        <ul class="dropdown-menu" style="width: 44%;">
                          <li><a class="dropdown-item" href="#">Name 1</a></li>
                          <li><a class="dropdown-item" href="#">Name 2</a></li>
                          <li><a class="dropdown-item" href="#">Name 3</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input type="text" id="pincode" inputmode="numeric" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>
        </div>
        <hr style="position: relative; top: 60px;">
        <div class="borrow-table">
            <table class="table" style="margin-top: 60px;">
                <thead style="text-align: center;">
                  <tr>
                    <th scope="col">Items</th>
                    <th scope="col">Available</th>
                    <th scope="col">Returning Quantity</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Soap</td>
                    <td>3pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Alcohol</td>
                    <td>3gal</td>
                    <td>1gal</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Rags</td>
                    <td>10pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <!-- <tr>
                    <th scope="row">Item 1</th>
                    <td>4 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 2</th>
                    <td>5 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 3</th>
                    <td>10pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 4</th>
                    <td>12 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr> -->
                </tbody>
              </table>
              <div class="row">
                <div class="col-4">
                  <label>Items:</label><br>
                  <div class="name-dropdown">
                      <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                        -Select here-
                      </button>
                      <ul class="dropdown-menu" style="width: 44%;">
                        <li><a class="dropdown-item" href="#">Rags</a></li>
                        <li><a class="dropdown-item" href="#">Soap</a></li>
                        <li><a class="dropdown-item" href="#">Alcohol</a></li>
                      </ul>
                  </div>
                </div>
                <div class="col-4">
                  <label>Quantity:</label><br>
                  <input type="number" id="quantity" placeholder="Stock left: 1 pcs.">
                </div>
                <div class="col-4">
                  <br>
                  <button id="add" class="btn btn-danger">Add</button>
                </div>
              </div>
        </div>
        <div class="submitBtn">
            <button class="return-submit">Submit</button>
        </div>
        
        
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 20rem; width: 20rem; opacity: 0.1;">
    </div>

    <div class="card optionModal" id="optionModal" style="z-index: 2;width: 35rem; height: 20rem; border: 3px solid #0e0e0f; display:none;">
        <h2 style="position: absolute; left: 90px; top:60px;">Purpose (Borrow or Return)</h2>
        <div class="card-body modal-content" >
            <div class="row">
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
<!-- </body>
</html> --> 

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
                <li><a class="dropdown-item" value="repair - userTOadmin.php">Request a minor repair</a></li>
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
                    <label>Name:</label><br>
                    <select id="unitDropdown" name="updateunit" class="form-select">
                        <option value="">- Name -</option>
                        <?php while ($data = mysqli_fetch_assoc($listup)) { ?>
                        <option value="<?php echo $data['name']?>"><?php echo $data['name']?></option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input type="text" id="pincode" inputmode="numeric" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>
        </div>
        <hr style="position: relative; top: 60px;">
        <div class="borrow-table">
            <table class="table" style="margin-top: 60px;">
                <thead style="text-align: center;">
                  <tr>
                    <th scope="col">Items</th>
                    <th scope="col">Available</th>
                    <th scope="col">Borrowing Quantity</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Soap</td>
                    <td>3pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Alcohol</td>
                    <td>3gal</td>
                    <td>1gal</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Rags</td>
                    <td>10pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                
                  <!-- <tr>
                    <th scope="row">Item 1</th>
                    <td>4 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 2</th>
                    <td>5 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 3</th>
                    <td>10pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 4</th>
                    <td>12 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr> -->
                </tbody>
              </table>
              <div class="row">
                <div class="col-4">
                  <label>Items:</label><br>
                  <div class="name-dropdown">
                      <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                        -Select here-
                      </button>
                      <ul class="dropdown-menu" style="width: 44%;">
                        <li><a class="dropdown-item" href="#">Rags</a></li>
                        <li><a class="dropdown-item" href="#">Soap</a></li>
                        <li><a class="dropdown-item" href="#">Alcohol</a></li>
                      </ul>
                  </div>
                </div>
                <div class="col-4">
                  <label>Quantity:</label><br>
                  <input type="number" id="quantity" placeholder="Stock left: 1 pcs.">
                </div>
                <div class="col-4">
                  <br>
                  <button id="add" class="btn btn-danger">Add</button>
                </div>
              </div>
        </div>
        <div class="submitBtn mx-auto mt-2">
            <button class="borrow-submit">Submit</button>
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
                        <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                          -Name-
                        </button>
                        <ul class="dropdown-menu" style="width: 44%;">
                          <li><a class="dropdown-item" href="#">Name 1</a></li>
                          <li><a class="dropdown-item" href="#">Name 2</a></li>
                          <li><a class="dropdown-item" href="#">Name 3</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-6">
                    <label>Pincode:</label><br>
                    <input type="text" id="pincode" inputmode="numeric" maxlength="4" placeholder="Enter pincode">
                </div>
            </div>
        </div>
        <hr style="position: relative; top: 60px;">
        <div class="borrow-table">
            <table class="table" style="margin-top: 60px;">
                <thead style="text-align: center;">
                  <tr>
                    <th scope="col">Items</th>
                    <th scope="col">Available</th>
                    <th scope="col">Returning Quantity</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Soap</td>
                    <td>3pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Alcohol</td>
                    <td>3gal</td>
                    <td>1gal</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <tr>
                    <td>Rags</td>
                    <td>10pcs</td>
                    <td>2pcs</td>
                    <td><a href="" class="btn btn-danger">Delete</a></td>
                  </tr>
                  <!-- <tr>
                    <th scope="row">Item 1</th>
                    <td>4 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 2</th>
                    <td>5 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 3</th>
                    <td>10pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr>
                  <tr>
                    <th scope="row">Item 4</th>
                    <td>12 pcs</td>
                    <td><input type="number" id="itemx-quantity" inputmode="numeric" placeholder="Enter quantity"></td>
                  </tr> -->
                </tbody>
              </table>
              <div class="row">
                <div class="col-4">
                  <label>Items:</label><br>
                  <div class="name-dropdown">
                      <button class="btn dropdown-toggle nameDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false" >
                        -Select here-
                      </button>
                      <ul class="dropdown-menu" style="width: 44%;">
                        <li><a class="dropdown-item" href="#">Rags</a></li>
                        <li><a class="dropdown-item" href="#">Soap</a></li>
                        <li><a class="dropdown-item" href="#">Alcohol</a></li>
                      </ul>
                  </div>
                </div>
                <div class="col-4">
                  <label>Quantity:</label><br>
                  <input type="number" id="quantity" placeholder="Stock left: 1 pcs.">
                </div>
                <div class="col-4">
                  <br>
                  <button id="add" class="btn btn-danger">Add</button>
                </div>
              </div>
        </div>
        <div class="submitBtn mx-auto mt-2">
            <button class="return-submit">Submit</button>
        </div>
        
        
        <img src="images/TUP-LOGO-modified.png" id="bg-logo" style=" height: 25rem; width: 25rem; opacity: 0.1;">
    </div>

    <div class="card optionModal mx-auto my-5" id="optionModal" style="z-index: 2;width: 35rem; height: 20rem; border: 3px solid #0e0e0f; display:none;">
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