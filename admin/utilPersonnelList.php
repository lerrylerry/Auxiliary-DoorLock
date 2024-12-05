<?php
require('../dbcred/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

// if (isset($_POST['name'])) {
//     $sqlinsert = "INSERT INTO `tbup`(`name`, `pincode`,`status`) VALUES ('" . $_POST['name'] . "','" . $_POST['pincode'] . "','inactive')";
//     mysqli_query($db, $sqlinsert);
// }

if (isset($_POST['name'])) {
  // Sanitize inputs to prevent SQL injection
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $pincode = mysqli_real_escape_string($db, $_POST['pincode']);
  
  // Check if the name or pincode already exists
  $checkQuery = "SELECT COUNT(*) AS count FROM `tbup` WHERE `name` = '$name' OR `email` = '$email' OR `pincode` = '$pincode'";
  $result = mysqli_query($db, $checkQuery);
  $row = mysqli_fetch_assoc($result);
  $recordExists = $row['count'] > 0;

  if (!$recordExists) {
      // Name and pincode combination doesn't exist, so insert it
      $sqlinsert = "INSERT INTO `tbup`(`name`, `email`, `pincode`, `status`) VALUES ('$name', '$email', '$pincode', 'active')";
      mysqli_query($db, $sqlinsert);
  } else {
      // Name or pincode already exists, handle this case accordingly
      echo "<script>alert('Record with the same name, email or pincode already exists!!');</script>";
      // Or you can redirect the user or perform other actions
  }
}


if (isset($_POST['updateupid'])) {
    $sqlupdateup = "UPDATE `tbup` SET `name`='".$_POST['updatename']."' ,`email` ='".$_POST['updatemail']."' ,`pincode` ='".$_POST['updatepin']."' WHERE id='" . $_POST['updateupid'] . "'";
    mysqli_query($db, $sqlupdateup);
}

if (isset($_POST['activateid'])) {
    $sqlupdateup = "UPDATE `tbup` SET `status`='active' WHERE id='" . $_POST['activateid'] . "'";
    mysqli_query($db, $sqlupdateup);
}
if (isset($_POST['deactivateid'])) {
    $sqlupdateup = "UPDATE `tbup` SET `status`='inactive' WHERE id='" . $_POST['deactivateid'] . "'";
    mysqli_query($db, $sqlupdateup);
}

if (isset($_POST['deleteid'])) {
    $sqldeletep= "DELETE FROM `tbup` WHERE id='" . $_POST['deleteid'] . "'";
    mysqli_query($db, $sqldeletep);
}

$sqlgetup = "SELECT id,name,email,pincode, status FROM `tbup`;";
$listup = mysqli_query($db, $sqlgetup);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Utility Personnel List</title>
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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
  <h2>Utility Personnel List</h2>
  <table id="personnelTable" class="table">
    <thead class="table-dark">
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Pincode</th>
        <th>Action</th>
      </tr>
      <br>
    </thead>
    <tbody>
    <?php while ($data = mysqli_fetch_assoc($listup)) { ?>
      <tr>
        <td>
          <span class="name"><?php echo $data['name']; ?></span>
        </td>
        <td>
          <span class="email"><?php echo $data['email']; ?></span>
        </td>
        <td>
            <span class="pincode"><?php echo $data['pincode']; ?></span>
            <button type="button" class="pinVisibility" style="border: none; background: transparent; padding: 0; margin-left: 10px;">
            <i class="bi bi-eye" style="font-size: 30px; display: inline-block;"></i>
            </button>
        </td>
        <td>
          <!-- Edit Button -->
          <button type="button" class="btn btn-primary btn-sm editBtn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id']?>"> Edit</button>
            <!-- Edit Modals -->
            <div class="modal fade" id="editModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="editPersonnelModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPersonnelLabel">Edit personnel <span class="text-danger"><?php echo $data['name']?>?</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Edit Form -->
                            <form action="" method="POST">
                                <input type="hidden" class="form-control" name ="updateupid" value="<?php echo $data['id']?>">
                                <div class="mb-3">
                                    <label for="personnelName1" class="form-label">Personnel</label>
                                    <input type="text" class="form-control" id="personnelName1" name="updatename" value="<?php echo $data['name']?>" required>
                                </div>
                                <div class="mb-3">
                                  <label for="email" class="form-label">Email</label>
                                  <input type="email" name="updatemail" class="form-control" id="addpersonnelemail" value="<?php echo $data['email']?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pincode1" class="form-label">Pincode</label>
                                    <!-- <input type="password" class="form-control" id="pincode1" name="updatepin" min="0" maxlength="4" value="<?php echo $data['pincode']?>" required> -->

                                    <div class="input-group">
                                      <input type="password" class="form-control" id="addpincode" name="updatepin" min="0" maxlength="4" value="<?php echo $data['pincode']?>" required>
                                      <!-- <div class="input-group-append">
                                          <span class="input-group-text">
                                              <i class="bi bi-eye" id="togglePassword"></i>
                                          </span>
                                      </div> -->
                                    </div>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
          <!-- Delete Button -->
          <button type="button" class="btn btn-danger btn-sm deleteBtn bi bi-trash-fill" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id']?>"> Delete</button>
            <!-- Delete Modals -->
            <div class="modal fade" id="deleteModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Delete Personnel 1</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete <?php echo $data['name']?>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form method="post" action="" >
                                <input type="hidden" class="form-control" id="pincode1" name="deleteid" value="<?php echo $data['id']?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
          <!-- Status Button -->
          <?php if($data['status'] =="inactive"){ ?>
            <div class="status d-inline-block" >
              <form method="post" action="">
                <button id="toggleButton" class="btn btn-secondary btn-sm btn-icon" onclick="toggleState()">
                  <i id="toggleIcon" class="bi bi-play-fill"><span id="toggleText">Inactive</span></i> <!-- Initial icon with text -->
                    <input type="hidden" class="form-control" id="pincode1" name="activateid" value="<?php echo $data['id']?>">
                </button>
              </form>
            </div>
            
            <?php }else if($data['status'] =="active"){?>
            
            <div class="status d-inline-block" >
              <form method="post" action="">
                <button id="toggleButton" class="btn btn-success btn-sm btn-icon" onclick="toggleState()">
                    <i id="toggleIcon" class="bi bi-pause-fill"><span id="toggleText">Active</span></i> <!-- Initial icon with text -->
                    <input type="hidden" class="form-control" id="pincode1" name="deactivateid" value="<?php echo $data['id']?>">
                </button>
              </form>
            </div>
            
            <?php  } ?>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger mb-3 mt-3 bottom-button" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">Add Personnel</button>
</div>

<!-- Bootstrap JS (jQuery is required) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap Bundle (Popper.js is required) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#personnelTable').DataTable({
        "lengthMenu": [[25, 50, 100], [25, 50, 100]], // Limit to only 25, 50, or 100 entries per page
    });
});
</script>


<!-- Add Product Modal -->
<div class="modal fade" id="addPersonnelModal" tabindex="-1" aria-labelledby="addPersonnelLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPersonnelLabel">Add New Personnel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Add Product Form -->
        <form id="addProductForm" method="post" action ="">
          <div class="mb-3">
            <label for="productName" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" id="addpersonnelName" placeholder="ex: Juan dela Cruz" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="addpersonnelemail" placeholder="ex: juandelacruz@gmail.com" required>
          </div>
          <div class="mb-3">
            <!-- 
            <input type="password" name="pincode" class="form-control" id="addpincode" placeholder="Enter 4 numbers"> -->
            <label for="quantity" class="form-label">Pincode</label>
            <div class="input-group">
              <input type="password" class="form-control newpin" id="pincode1" name="pincode" min="0" maxlength="4" placeholder="Create 4 digit pincode" required >
              <div class="input-group-append">
                  <span class="input-group-text">
                      <i class="bi bi-eye" id="togglePassword2"></i>
                  </span>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="addPersonnelBtn">Create Personnel</button>
          </form>
      </div>
    </div>
  </div>
</div>
</section>

<script src="static/script.js"></script>
<!-- <script>
  $(document).ready(function() {
    $('#addpersonnelName').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
<script>
$(document).ready(function() {
    $('#personnelName1').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script> -->

</body>
</html>
