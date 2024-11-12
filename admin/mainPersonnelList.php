<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

if (isset($_POST['name'])) {
  // Sanitize inputs to prevent SQL injection
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  
  // Check if the name or email already exists in the database
  $checkQuery = "SELECT COUNT(*) AS count FROM `tbmp` WHERE `name` = '$name' OR `email` = '$email'";
  $result = mysqli_query($db, $checkQuery);
  $row = mysqli_fetch_assoc($result);
  $recordExists = $row['count'] > 0;

  if (!$recordExists) {
      // Name and email combination doesn't exist, so insert it
      $sqlinsert = "INSERT INTO `tbmp`(`name`, `email`, `status`) VALUES ('$name', '$email', 'active')";
      if (mysqli_query($db, $sqlinsert)) {
          echo "<script>alert('Record added successfully');</script>";
      } else {
          echo "<script>alert('Error inserting record');</script>";
      }
  } else {
      // Name or email already exists, handle this case accordingly
      echo "<script>alert('Record with the same name or email already exists!');</script>";
  }
}


// if (isset($_POST['name'])) {
//     $sqlinsert = "INSERT INTO `tbmp`(`name`, `email`, `status`) VALUES ('" . $_POST['name'] . "','" . $_POST['email'] . "', 'active')";
//     mysqli_query($db, $sqlinsert);
// }

if (isset($_POST['updateupid'])) {
    $sqlupdateup = "UPDATE `tbmp` SET `name`='".$_POST['updatename']."',`email` ='".$_POST['updateemail']."' WHERE id='" . $_POST['updateupid'] . "'";
    mysqli_query($db, $sqlupdateup);
}


if (isset($_POST['deleteid'])) {
    $sqldeletep= "DELETE FROM `tbmp` WHERE id='" . $_POST['deleteid'] . "'";
    mysqli_query($db, $sqldeletep);
}

if (isset($_POST['activateid'])) {
  $sqlupdateup = "UPDATE `tbmp` SET `status`='active' WHERE id='" . $_POST['activateid'] . "'";
  mysqli_query($db, $sqlupdateup);
}
if (isset($_POST['deactivateid'])) {
  $sqlupdateup = "UPDATE `tbmp` SET `status`='inactive' WHERE id='" . $_POST['deactivateid'] . "'";
  mysqli_query($db, $sqlupdateup);
}

$sqlgetmp = "SELECT id,name,email,status FROM `tbmp`;";
$listmp = mysqli_query($db, $sqlgetmp);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Maintenance Personnel List</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
<?php include('static/sidebar.php')?>

<section class="home-section" style="overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
<div class="container mt-5">
  <h2>Maintenance Personnel List</h2>
  <table id="personnelTable" class="table">
    <thead class="table-dark">
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Action</th>
      </tr>
      <br>
    </thead>
    <tbody>
    <?php while ($data = mysqli_fetch_assoc($listmp)) { ?>
        <tr>
            <td><?php echo $data['name']; ?></td>
            <td><?php echo $data['email']; ?></td>
            <td>
                <!-- Edit Button -->
                <button type="button" class="btn btn-primary btn-sm editBtn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id']?>"> Edit</button>
                <!-- Edit Modals -->
                <div class="modal fade" id="editModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="editPersonnelModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editPersonnelLabel">Edit <?php echo $data['name']?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Edit Form -->
                                <form action="" method="POST">
                                    <input type="hidden" class="form-control" name ="updateupid" value="<?php echo $data['id']?>">
                                    <div class="mb-3">
                                        <label for="personnelName1" class="form-label">Personnel</label>
                                        <input required type="text" class="form-control" id="personnelName1" name="updatename" value="<?php echo $data['name']?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input required type="email" class="form-control" id="email" name="updateemail" value="<?php echo $data['email']?>">
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
  <button type="button" class="btn btn-danger mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">Add Personnel</button>
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
    $('#personnelTable').DataTable();
  });
</script>

<script>
  var isActive = false;

  function toggleState() {
    isActive = !isActive; // Toggle the state
    var toggleButton = document.getElementById("toggleButton");
    var toggleIcon = document.getElementById("toggleIcon");
    var toggleText = document.getElementById("toggleText");

    if (isActive) {
      // Change button to active state
      toggleButton.classList.remove("btn-secondary");
      toggleButton.classList.add("btn-success");
      toggleIcon.classList.remove("bi-play-fill");
      toggleIcon.classList.add("bi-pause-fill");
      toggleText.textContent = "Active";
    } else {
      // Change button to inactive state
      toggleButton.classList.remove("btn-success");
      toggleButton.classList.add("btn-secondary");
      toggleIcon.classList.remove("bi-pause-fill");
      toggleIcon.classList.add("bi-play-fill");
      toggleText.textContent = "Inactive";
    }
  }
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
        <form id="addProductForm" method="post"action="">
          <div class="mb-3">
            <label for="productName" class="form-label">Name</label>
            <input required type="text" class="form-control" name="name" id="addpersonnelName">
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Email</label>
            <input required type="email" class="form-control" name="email" id="addemail">
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