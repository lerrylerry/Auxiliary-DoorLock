<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

$sqlgetborrow = "SELECT id FROM tbborrow  WHERE status ='Pending'";
$getborrowcount= mysqli_num_rows(mysqli_query($db, $sqlgetborrow));

$sqlgetreturn= "SELECT id FROM tbreturn  WHERE status ='Pending'";
$getreturncount= mysqli_num_rows(mysqli_query($db, $sqlgetreturn));

$sqlgetrepair= "SELECT id FROM tbminorrepair  WHERE status ='Pending'";
$getrepairrequest= mysqli_num_rows(mysqli_query($db, $sqlgetrepair));

$sqlgetp = "SELECT id, unit, name, quantity FROM `tbproductlist` WHERE quantity >= 0 ORDER BY quantity ASC LIMIT 5;";
$listp = mysqli_query($db, $sqlgetp);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Home</title>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/admin-section.css">
</head>
</head>
<body>
<?php include('static/sidebar.php')?>

  <section class="home-section" style="overflow-y: auto;">
    <div class="home-content">
    <i class='bx bx-menu'></i>
   </div>
    <div class="container">
      <h1 class="text-center mb-3"><b>Welcome Back, Admin!</b></h1>
      <div class="row">
        <!-- Borrow Request Card -->
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-body text-center">
              <i class="fas fa-hand-holding-heart fa-3x mb-3"></i>
              <h5 class="card-title">Borrow Request <span class="text-danger">(<?php echo  $getborrowcount;?>)</span></h5>
              <p class="card-text">There are requesting for borrow items</p>
              <a href="borrowedItems.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
        <!-- Return Request Card -->
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-body text-center">
              <i class="fas fa-arrow-left fa-3x mb-3"></i>
              <h5 class="card-title">Return Request <span class="text-danger">(<?php echo $getreturncount;?>)</span></h5>
              <p class="card-text">There are requesting for return items</p>
              <a href="return-items.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
        <!-- Minor Repair Request Card -->
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-body text-center">
              <i class="fas fa-tools fa-3x mb-3"></i>
              <h5 class="card-title">Minor Repair Request <span class="text-danger">(<?php echo $getrepairrequest;?>)</span></h5>
              <p class="card-text">There are requesting for minor repairs</p>
              <a href="minor-repair.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Available Stocks Section -->
      <div class="col-lg">
        <div class="card mb-3">
          <div class="card-body text-center">
            <i class="fas fa-boxes fa-3x mb-3"></i>
            <h5 class="card-title">Running Out of Stocks</h5>
            <!-- Table for running out stocks or showing available stocks -->
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>Unit</th>
                  <th>Quantity</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = mysqli_fetch_array($listp)) { ?>
                <tr>
                  <td><?php echo $row['name'];?></td>
                  <td><?php echo $row['unit'];?></td>
                  <td  class="text-danger"><?php echo $row['quantity'];?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
            <a href="inventory.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
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
                  <p class="card-text">View the record of the stockroom.</p>
                  <a href="recorded.php" type="button" class="btn btn-danger"><i class="fas fa-info-circle"></i> More Info</a>
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
  </section>
  <!-- Bootstrap JS (jQuery is required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap Bundle (Popper.js is required) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


  <script src="static/script.js"></script>
  <script>
$(document).ready(function() {
  // When the hamburger icon is clicked, toggle the sidebar visibility
  $('.hamburger-icon').click(function() {
    $('.sidebar').toggleClass('open');
  });
});

  </script>
</body>
</html>
