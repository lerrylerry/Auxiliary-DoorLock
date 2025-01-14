<?php
require('../dbcred/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

$statusMessage = "";
$modalTitle = "";

if (isset($_POST['addnewpersonnel'])) {
  // Sanitize inputs to prevent SQL injection
  $name = mysqli_real_escape_string($db, $_POST['name']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $pincode = mysqli_real_escape_string($db, $_POST['pincode']);

  // Check if the pincode is the default (7777)
  if ($pincode == '7777') {
      $statusMessage = "The pincode cannot be 7777, it is the default pincode!";
      $modalTitle = "Error";
  } else {
      // Check if the pincode already exists in tbparser (master pincode)
      $checkMasterPincodeQuery = "SELECT COUNT(*) AS count FROM `tbparser` WHERE `pincode` = '$pincode'";
      $masterPincodeResult = mysqli_query($db, $checkMasterPincodeQuery);
      $masterPincodeRow = mysqli_fetch_assoc($masterPincodeResult);
      if ($masterPincodeRow['count'] > 0) {
          // If the pincode already exists in tbparser, show error
          $statusMessage = "The pincode is already used as the master pincode and cannot be reused!";
          $modalTitle = "Error";
      } else {
          // Check if the name, email, or pincode already exists in tbup
          $checkQuery = "SELECT COUNT(*) AS count FROM `tbup` WHERE `name` = '$name' OR `email` = '$email' OR `pincode` = '$pincode'";
          $result = mysqli_query($db, $checkQuery);
          $row = mysqli_fetch_assoc($result);
          $recordExists = $row['count'] > 0;

          if (!$recordExists) {
              // Name, email, and pincode combination doesn't exist, so insert the record
              $sqlinsert = "INSERT INTO `tbup`(`name`, `email`, `pincode`, `status`) VALUES ('$name', '$email', '$pincode', 'active')";
              if (mysqli_query($db, $sqlinsert)) {
                  $statusMessage = "Record created successfully!";
                  $modalTitle = "Success";
              } else {
                  $statusMessage = "Error creating record!";
                  $modalTitle = "Error";
              }
          } else {
              // Name, email, or pincode already exists in tbup
              $statusMessage = "Record with the same name, email, or pincode already exists!";
              $modalTitle = "Error";
          }
      }
  }
}

if (isset($_POST['editmasterpin'])) {
  // Sanitize inputs to prevent SQL injection
  $pincode = mysqli_real_escape_string($db, $_POST['pincode']);

  // Check if the pincode is the default (7777)
  if ($pincode == '7777') {
      $statusMessage = "The master pincode cannot be 7777, it is the default pincode for utility personnel!";
      $modalTitle = "Error";
  } else {
      // Check if the pincode already exists
      $checkQuery = "SELECT COUNT(*) AS count FROM `tbup` WHERE `pincode` = '$pincode'";
      $result = mysqli_query($db, $checkQuery);
      $row = mysqli_fetch_assoc($result);
      $recordExists = $row['count'] > 0;

      if (!$recordExists) {
          // Pincode doesn't exist, so update the master pincode
          $sqlupdate = "UPDATE `tbparser` SET `pincode` = '$pincode' WHERE `id` = 1";
          if (mysqli_query($db, $sqlupdate)) {
            $statusMessage = "Master Pincode has been changed!";
            $modalTitle = "Success";
          } else {
              $statusMessage = "Error updating master pincode!";
              $modalTitle = "Error";
          }
      } else {
          // Pincode already exists in the `tbup` table
          $statusMessage = "The pincode is already in use by utility personnel!";
          $modalTitle = "Error";
      }
  }
}

if (isset($_POST['updateupid'])) {
  // Sanitize the inputs
  $updateid = $_POST['updateupid'];
  $updatename = mysqli_real_escape_string($db, $_POST['updatename']);
  $updatemail = mysqli_real_escape_string($db, $_POST['updatemail']);
  $updatepin = mysqli_real_escape_string($db, $_POST['updatepin']);

  // Check if the pincode is the default pin (7777)
  if ($updatepin == '7777') {
      $statusMessage = "The pincode cannot be 7777, it is the default pincode!";
      $modalTitle = "Error";
  } else {
      // Check if the pincode already exists in tbparser (master pincode)
      $checkMasterPincodeQuery = "SELECT COUNT(*) AS count FROM `tbparser` WHERE `pincode` = '$updatepin'";
      $masterPincodeResult = mysqli_query($db, $checkMasterPincodeQuery);
      $masterPincodeRow = mysqli_fetch_assoc($masterPincodeResult);
      if ($masterPincodeRow['count'] > 0) {
          // If the pincode is already used as the master pincode, show error
          $statusMessage = "The pincode is already used as the master pincode and cannot be reused!";
          $modalTitle = "Error";
      } else {
          // Check if the name, email, or pincode already exists (excluding the current record)
          $checkQuery = "SELECT COUNT(*) AS count FROM `tbup` WHERE (`name` = '$updatename' OR `email` = '$updatemail' OR `pincode` = '$updatepin') AND id != '$updateid'";
          $result = mysqli_query($db, $checkQuery);
          $row = mysqli_fetch_assoc($result);

          if ($row['count'] > 0) {
              // A duplicate record exists
              $statusMessage = "Record with the same name, email, or pincode already exists!";
              $modalTitle = "Error";
          } else {
              // No duplication found, proceed with the update
              $sqlupdateup = "UPDATE `tbup` SET `name`='$updatename', `email`='$updatemail', `pincode`='$updatepin' WHERE id='$updateid'";
              if (mysqli_query($db, $sqlupdateup)) {
                  $statusMessage = "Personnel updated successfully!";
                  $modalTitle = "Success";
              } else {
                  $statusMessage = "Error updating record!";
                  $modalTitle = "Error";
              }
          }
      }
  }
}

if (isset($_POST['activateid'])) {
  // Function to generate a random 4-digit pincode
  function generateRandomPin() {
      return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);  // Generates a 4-digit pin, e.g., 0421
  }

  // Initialize the pincode and check if it exists
  $uniquePinFound = false;
  $newPincode = '';
  
  while (!$uniquePinFound) {
      // Generate a random pincode
      $newPincode = generateRandomPin();
      
      // Check if the generated pincode already exists in the database
      $checkPinQuery = "SELECT COUNT(*) AS count FROM `tbup` WHERE `pincode` = '$newPincode'";
      $result = mysqli_query($db, $checkPinQuery);
      $row = mysqli_fetch_assoc($result);
      
      // If the pincode doesn't exist in the table, we have a unique pincode
      if ($row['count'] == 0) {
          $uniquePinFound = true;  // Exit the loop
      }
  }

  // Now that we have a unique pincode, proceed with the update
  $activateid = mysqli_real_escape_string($db, $_POST['activateid']);
  $sqlupdateup = "UPDATE `tbup` SET `status`='active', `pincode`='$newPincode' WHERE id='$activateid'";
  
  // Execute the query
  if (mysqli_query($db, $sqlupdateup)) {
      // echo "<script>alert('Record activated successfully with pincode: $newPincode');</script>";
  } else {
      echo "<script>alert('Error activating record!');</script>";
  }
}


if (isset($_POST['deactivateid'])) {
  $sqlupdateup = "UPDATE `tbup` SET `status`='inactive', `pincode`= '7777' WHERE id='" . $_POST['deactivateid'] . "'";
  mysqli_query($db, $sqlupdateup);
}

// if (isset($_POST['deleteid'])) {
//     $sqldeletep= "DELETE FROM `tbup` WHERE id='" . $_POST['deleteid'] . "'";
//     mysqli_query($db, $sqldeletep);
// }

$sqlgetup = "SELECT id,name,email,pincode, status FROM `tbup`;";
$listup = mysqli_query($db, $sqlgetup);

$sqlgetmp = "SELECT pincode FROM `tbparser` WHERE id = 1";
$listmp = mysqli_query($db, $sqlgetmp);
$mp = mysqli_fetch_assoc($listmp); // Get the result row

// Check if there's a valid result
$pincode = isset($mp['pincode']) ? $mp['pincode'] : ''; // Default to empty if no pincode is found

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
        <!-- <th>Pincode</th> -->
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
        <!-- <td>
            <span class="pincode"><?php echo $data['pincode']; ?></span>
            <button type="button" class="pinVisibility" style="border: none; background: transparent; padding: 0; margin-left: 10px;">
            <i class="bi bi-eye" style="font-size: 30px; display: inline-block;"></i>
            </button>
        </td> -->
        <td>
          <!-- Edit Button -->
          <button type="button" class="btn btn-primary btn-sm editBtn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id']?>"> Edit</button>
            <!-- Edit Modals -->
            <div class="modal fade" id="editModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="editPersonnelModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPersonnelLabel">Edit personnel <span class="text-danger"><?php echo $data['name']?>?</span></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <!-- Info Note -->
                          <div class="alert alert-info" role="alert" style="text-align: justify;">
                            <strong>Note:</strong> Ensure that the personnel's name, email and pincode are unique. The pincode must be a 4-digit number, and it will be used by the utility personnel to access the stockroom.
                          </div>

                            <!-- Edit Form -->
                            <form action="" method="POST">
                                <input type="hidden" class="form-control" name ="updateupid" value="<?php echo $data['id']?>">
                                <div class="mb-3">
                                    <label for="personnelName1" class="form-label">Personnel:</label>
                                    <input type="text" class="form-control" id="personnelName1" name="updatename" value="<?php echo $data['name']?>" required>
                                </div>
                                <div class="mb-3">
                                  <label for="email" class="form-label">Email:</label>
                                  <input type="email" name="updatemail" class="form-control" id="addpersonnelemail" value="<?php echo $data['email']?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="pincode1" class="form-label">Pincode:</label>
                                    <!-- <input type="password" class="form-control" id="pincode1" name="updatepin" min="0" maxlength="4" value="<?php echo $data['pincode']?>" required> -->

                                    <div class="input-group">
                                    <input type="password" class="form-control addpincode" name="updatepin" min="0" maxlength="4" value="<?php echo $data['pincode']?>" required>
                                      <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="bi bi-eye togglePassword"></i>
                                        </span>
                                      </div>
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
          <!-- <button type="button" class="btn btn-danger btn-sm deleteBtn bi bi-trash-fill" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id']?>"> Delete</button> -->
            <!-- Delete Modals -->
            <!-- <div class="modal fade" id="deleteModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
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
            </div> -->
          <!-- Status Button -->
          <?php if($data['status'] =="inactive"){ ?>
            <button id="toggleButton_<?php echo $data['id']; ?>" class="btn btn-secondary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#activeModal_<?php echo $data['id']; ?>">
              <i id="toggleIcon_<?php echo $data['id']; ?>" class="bi bi-play-fill"><span id="toggleText_<?php echo $data['id']; ?>">Inactive</span></i>
            </button>

            <!-- Active Modal -->
            <div class="modal fade" id="activeModal_<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="activeModalLabel_<?php echo $data['id']; ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="activeModalLabel_<?php echo $data['id']; ?>">Confirm Action for <span class="text-danger"><?php echo $data['name']?></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to change the status to 
                    <button id="toggleButton_<?php echo $data['id']; ?>" class="btn btn-success btn-sm btn-icon" disabled>
                      <i id="toggleIcon_<?php echo $data['id']; ?>" class="bi bi-pause-fill"><span id="toggleText_<?php echo $data['id']; ?>">Active</span></i>
                    </button>?
                    <hr>
                    <p class="mt-3 text-muted">
                      <strong>Note:</strong> The activation will automatically generate a random pin that can be used by the utility personnel.
                    </p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" action="">
                      <button type="submit" class="btn btn-primary btn-sm btn-icon" id="confirmButton_<?php echo $data['id']; ?>">
                        Confirm
                        <input type="hidden" name="activateid" value="<?php echo $data['id']; ?>">
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          <?php } else if($data['status'] == "active"){ ?>
            <button id="toggleButton_<?php echo $data['id']; ?>" class="btn btn-success btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#inactiveModal_<?php echo $data['id']; ?>">
              <i id="toggleIcon_<?php echo $data['id']; ?>" class="bi bi-pause-fill"><span id="toggleText_<?php echo $data['id']; ?>">Active</span></i>
            </button>

            <!-- Inactive Modal -->
            <div class="modal fade" id="inactiveModal_<?php echo $data['id']; ?>" tabindex="-1" aria-labelledby="inactiveModalLabel_<?php echo $data['id']; ?>" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="inactiveModalLabel_<?php echo $data['id']; ?>">Confirm Action for <span class="text-danger"><?php echo $data['name']?></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    Are you sure you want to change the status to 
                    <button id="toggleButton_<?php echo $data['id']; ?>" class="btn btn-secondary btn-sm btn-icon" disabled>
                      <i id="toggleIcon_<?php echo $data['id']; ?>" class="bi bi-play-fill"><span id="toggleText_<?php echo $data['id']; ?>">Inactive</span></i>
                    </button>?
                    <hr>
                    <!-- Info Note -->
                    <div class="alert alert-info" role="alert" style="text-align: justify;">
                      <strong>Note:</strong> The deactivation of the utility personnel will set the pincode to default and the previous pincode use will be usable again by other personnel.
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" action="">
                      <button type="submit" class="btn btn-primary btn-sm btn-icon" id="confirmButton_<?php echo $data['id']; ?>">
                        Confirm
                        <input type="hidden" name="deactivateid" value="<?php echo $data['id']; ?>">
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>

          <?php } ?>

        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger mb-3 mt-3 bottom-button" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">Add Personnel</button>
  <button type="button" class="btn btn-danger mb-3 mt-3 bottom-button" data-bs-toggle="modal" data-bs-target="#masterPinModal">Master Pin Setup</button>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addPersonnelModal" tabindex="-1" aria-labelledby="addPersonnelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPersonnelLabel">Add New Personnel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Info Note -->
        <div class="alert alert-info" role="alert" style="text-align: justify;">
          <strong>Note:</strong> Ensure that the personnel's name, email and pincode are unique. The pincode must be a 4-digit number, and it will be used by the utility personnel to access the stockroom.
        </div>

        <!-- Add Personnel Form -->
        <form id="addProductForm" method="post" action="">
          <div class="mb-3">
            <label for="productName" class="form-label">Personnel:</label>
            <input type="text" name="name" class="form-control" id="addpersonnelName" placeholder="ex: Juan dela Cruz" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" class="form-control" id="addpersonnelemail" placeholder="ex: juandelacruz@gmail.com" required>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Pincode:</label>
            <div class="input-group">
              <input type="password" class="form-control newpin" id="pincode1" name="pincode" min="0" maxlength="4" placeholder="Create 4 digit pincode" required>
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
        <button type="submit" class="btn btn-primary" id="addPersonnelBtn" name="addnewpersonnel">Create Personnel</button>
          </form>
      </div>
    </div>
  </div>
</div>

<!-- Master Pincode Modal -->
<div class="modal fade" id="masterPinModal" tabindex="-1" aria-labelledby="masterPinLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="masterPinLabel">Edit Master Pincode</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Info Note -->
        <div class="alert alert-info" role="alert" style="text-align: justify;">
          <strong>Note:</strong> You can view the master pincode by clicking the eye icon. The master pincode allows access to the stockroom without permission and can bypass security. You can edit the master pincode here.
        </div>

        <!-- Edit Pincode Form -->
        <form id="masterPinForm" method="post" action="">
          <div class="mb-3">
            <label for="quantity" class="form-label">Master Pincode:</label>
            <div class="input-group">
              <!-- Set the value of the pincode dynamically from the database -->
              <input type="password" class="form-control masterpincode" id="pincode2" name="pincode" min="0" maxlength="4" placeholder="4-Digit Master Pin" value="<?php echo htmlspecialchars($pincode); ?>" required>
              <div class="input-group-append">
                <span class="input-group-text">
                    <i class="bi bi-eye" id="togglePassword3"></i>
                </span>
              </div>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" id="masterPinBtn" name="editmasterpin">Save Changes</button>
          </form>
      </div>
    </div>
  </div>
</div>

<!-- Success/Error Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header justify-content-center">
        <h5 class="modal-title" id="statusModalLabel"></h5>
      </div>
      <div class="modal-body text-center">
        <!-- Success/Error Message Here -->
        <div id="statusMessage"></div>
      </div>
      <div class="modal-footer justify-content-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


</section>

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

<script>
  // Show status modal after form submission
  window.onload = function() {
    // PHP success/error message and title from the backend
    let statusMessage = "<?php echo $statusMessage; ?>";
    let modalTitle = "<?php echo $modalTitle; ?>";
    
    // If there is a status message, show the second modal with the message
    if (statusMessage) {
        // Update the message and title in the modal
        document.getElementById("statusMessage").innerHTML = statusMessage;
        document.getElementById("statusModalLabel").innerHTML = modalTitle;

        // Change the modal title color based on success or error
        if(modalTitle === "Success") {
            document.getElementById("statusModalLabel").style.color = "green"; // Success color
        } else {
            document.getElementById("statusModalLabel").style.color = "red"; // Error color
        }
        
        // Show the modal
        var statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
    }
  }
</script>

<script src="static/script.js"></script>
<script src="static/swipe.js"></script>

</body>
</html>
