<?php
require('../dbcred/db.php');


session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

$sqlgetborrow = "SELECT id FROM tbborrow  WHERE status ='Pending'";
$getborrowcount= mysqli_num_rows(mysqli_query($db, $sqlgetborrow));

$sqlgetreturn= "SELECT id FROM tbreturn  WHERE status ='Pending'";
$getreturncount= mysqli_num_rows(mysqli_query($db, $sqlgetreturn));

$sqlgetrepair= "SELECT id FROM tbminorrepair  WHERE status ='Pending'";
$getrepairrequest= mysqli_num_rows(mysqli_query($db, $sqlgetrepair));


$sqlgetp = "SELECT id,unit,name,quantity FROM `tbproductlist` WHERE quantity >=0;";
$listp = mysqli_query($db, $sqlgetp);

?>

<!DOCTYPE html>
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
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>
    <title>Auxiliary | Home</title>
</head>
<body>
<?php include('static/sidebar.php')?>

  <section class="home-section" style="overflow-y: auto;">
    <div class="home-content">
    <i class='bx bx-menu'></i>
   </div>
    <div class="container">
      <h1 class="text-center mb-3"><b>Welcome Back! Admin</b></h1>
      <div class="row">
        <!-- Borrow Request Card -->
        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-body text-center">
              <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
              <h5 class="card-title">Borrow Request <span class="text-danger">(<?php echo  $getborrowcount;?>)</span></h5>
              <p class="card-text">There are requesting for borrow items</p>
              <a href="borrowedItems.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
        <!-- Return Request Card -->
        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-body text-center">
              <i class="fas fa-arrow-left fa-3x mb-3"></i>
              <h5 class="card-title">Return Request <span class="text-danger">(<?php echo $getreturncount;?>)</span></h5>
              <p class="card-text">There are requesting for return items</p>
              <a href="return-items.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
        <!-- Minor Repair Request Card -->
        <div class="col-lg-4">
          <div class="card mb-3">
            <div class="card-body text-center">
              <i class="fas fa-tools fa-3x mb-3"></i>
              <h5 class="card-title">Minor Repair Request <span class="text-danger">(<?php echo $getrepairrequest;?>)</span></h5>
              <p class="card-text">There are requesting for minor repairs</p>
              <a href="minorRepair.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
      </div>
      <br>
          <div class="col-lg">
            <div class="card mb-3">
              <div class="card-body text-center">
                <i class="fas fa-boxes fa-3x mb-3"></i>
                <h5 class="card-title">Available Stocks</h5>
                <!-- Table for running out stocks -->
                <table class="table">
                  <thead>
                    <tr>
                      <th>Product Name</th>
                      <th>Unit</th>
                      <th>Quantity</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php 
                    $count = 0; // Initialize counter variable
                    $ellipsis = false; // Flag to indicate if ellipsis is needed

                    while ($data = mysqli_fetch_assoc($listp)) {
                        if($count >= 5) {
                            $ellipsis = true; // Set ellipsis flag if more than 5 rows
                            break; // Break out of the loop if count exceeds 5
                        }
                    ?>
                        <tr>
                            <td><?php echo $data['name']; ?></td>
                            <td><?php echo $data['unit']; ?></td>
                            <td><?php echo $data['quantity']; ?></td>
                        </tr>
                    <?php 
                    $count++; // Increment the counter
                    } 

                    if ($ellipsis) {
                        echo '<tr><td colspan="3">...</td></tr>'; // Display ellipsis
                    }
                    ?>
                    <!-- Add more rows as needed -->
                  </tbody>
                </table>
                <a href="addItems.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
              </div>
            </div>
          </div>
          <div class="row">
             <!-- First Right Card - Camera -->
            <div class="col-lg-6">
              <div class="card mb-3">
                <div class="card-body text-center">
                  <i class="fas fa-camera fa-3x mb-3"></i>
                  <h5 class="card-title">Camera</h5>
                  <p class="card-text">View live of the stockroom.</p>
                  <a href="camera.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
                </div>
              </div>
            </div>
            <!-- Second Right Card - Utility Personnel -->
            <div class="col-lg-6">
              <div class="card mb-3">
                <div class="card-body text-center">
                  <i class="fas fa-user-cog fa-3x mb-3"></i>
                  <h5 class="card-title">Utility Personnel</h5>
                  <p class="card-text">Set the active and inactive status.</p>
                  <a href="utilPersonnelList.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
                </div>
              </div>
            </div>
              <!-- Maintenance Personnel List Card -->
              <!-- <div class="col-lg-4">
                <div class="card mb-3">
                  <div class="card-body text-center">
                    <i class="fas fa-wrench fa-3x mb-3"></i>
                    <h5 class="card-title">Maintenance Personnel</h5>
                    <p class="card-text">View the Maintenance Team</p>
                    <a href="mainPersonnelList.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
                  </div>
                </div>
              </div> -->
    </div>
          </div>

  </section>
  <script src="static/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
