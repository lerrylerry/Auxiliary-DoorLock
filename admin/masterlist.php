<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

//---------------------------------------------------------------------------------------ADD PRODUCT//
if (isset($_POST['productName'])) {
  // Retrieve the product name from the POST data
  $productName = trim($_POST['productName']); // Now getting the input value

  if (empty($productName)) {
      echo "<script>alert('Product name cannot be empty!');</script>";
  } else {
      // Check if the product already exists

      
      $checkQuery = "SELECT COUNT(*) AS count FROM `tbproductlist` WHERE `name` = '$productName'";
      $result = mysqli_query($db, $checkQuery);
      $row = mysqli_fetch_assoc($result);
      $productExists = $row['count'] > 0;

      if (!$productExists) {
          // Product doesn't exist, so insert it
          $unit = $_POST['unit']; // Retrieve the unit
          $category = $_POST['cat']; // Retrieve the category
          $sqlinsertp = "INSERT INTO `tbproductlist`(`name`, `unit`, `category`) VALUES ('$productName', '$unit', '$category')";
          mysqli_query($db, $sqlinsertp);
          $statusMessage = "Product added successfully!";
          $modalTitle = "Success";
      } else {
          // Product already exists
          $statusMessage = "Product already exists!";
          $modalTitle = "Error";
          // Optionally, update the existing record
          // $updateQuery = "UPDATE `tbproductlist` SET `unit`='$unit', `category`='$category' WHERE `name`='$productName'";
          // mysqli_query($db, $updateQuery);
      }
  }
}


//---------------------------------------------------------------------------------------EDIT PRODUCT//
if (isset($_POST['updatepid'])) {
    $sqlupdateup = "UPDATE `tbproductlist` SET `name`='".$_POST['updatename']."',`unit` ='".$_POST['updateunit']."',`category` ='".$_POST['updatecat']."' WHERE id='" . $_POST['updatepid'] . "'";
    if (mysqli_query($db, $sqlupdateup)) {
        $statusMessage = "Product updated successfully";
        $modalTitle = "Success";
  } else {
        $statusMessage = "Product not updated";
        $modalTitle = "Error";
  }
}

//---------------------------------------------------------------------------------------DELETE PRODUCT//
if (isset($_POST['deleteid'])) {
    $sqldeleteitem= "DELETE FROM `tbproductlist` WHERE id='" . $_POST['deleteid'] . "'";
    if (mysqli_query($db, $sqldeleteitem)) {
      echo "<script>var updateSuccess = true;</script>";
  } else {
      echo "<script>var updateSuccess = false;</script>";
  }
}

//---------------------------------------------------------------------------------------FETCH PRODUCT//
$sqlgetp = "SELECT id,unit,name,quantity,category FROM `tbproductlist`;";
$listp = mysqli_query($db, $sqlgetp);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Masterlist</title>
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
<body>
<?php include('static/sidebar.php')?>

<section class="home-section" style="overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
<div class="container mt-5">
  <h2>MasterList</h2>
  <table id="productTable" class="table">
    <thead class="table-dark">
      <tr>
        <th>Product Name</th>
        <th>Unit</th>
        <th>Category</th>
        <th>Action</th>
      </tr>
      <br>
    </thead>
    <tbody>
    <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
      <tr>
          <td><?php echo $data['name']; ?></td>
          <td><?php echo $data['unit']; ?></td>
          <td><?php echo $data['category']; ?></td>
        <td>
          <!-- Edit Button -->
            <button type="button" class="btn btn-primary btn-sm editBtn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id']?>"></button>

            <div class="modal fade" id="editModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="editModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel1"><span class="text-danger fw-bold">Edit a product?</span></h5>
                        </div>
                        <div class="modal-body">
                            <!-- Info Note -->
                            <div class="alert alert-info" role="alert" style="text-align: justify;">
                                <strong>Note:</strong> Ensure that all fields are updated correctly. You can change the product name, unit, and category here. Once saved, the changes will be reflected in the inventory.
                            </div>

                            <!-- Edit Product Form -->
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="productName1" class="form-label">Product:</label>
                                    <input type="hidden" class="form-control" name="updatepid" value="<?php echo $data['id']?>" required>
                                    <input type="text" class="form-control" name="updatename" value="<?php echo $data['name']?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="unit1" class="form-label">Unit:</label>
                                    <select name="updateunit" class="form-select" required>
                                        <option value="<?php echo $data['unit']?>"><?php echo $data['unit']?></option>
                                        <option value="kls">kls</option>
                                        <option value="gal">gal</option>
                                        <option value="pcs">pcs</option>
                                        <option value="tubes">tubes</option>
                                        <option value="rolls">rolls</option>
                                        <option value="can">can</option>
                                        <option value="ltrs">ltrs</option>
                                        <option value="pairs">pairs</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="unit2" class="form-label">Category:</label>
                                    <select name="updatecat" class="form-select" required>
                                        <option value="<?php echo $data['category']?>"><?php echo $data['category']?></option>
                                        <option value="Consumables">Consumables (Products that can be consumed)</option>
                                        <option value="Returnables">Returnables (Products that can be returned)</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
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
        <!-- <button type="button" class="btn btn-danger btn-sm deleteBtn bi bi-trash-fill" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id']?>"></button> -->
        <div class="modal fade" id="deleteModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="deleteModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel1"><span class="text-danger fw-bold">Delete a product?</span></h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete <span class="text-primary"><?php echo $data['name']?> </span>?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="post" action="" >
                            <input type="hidden" name="deleteid" value="<?php echo $data['id']?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </td>
      </tr>
    <?php } ?>
    </tbody>
  </table>
  <button type="button" class="btn btn-danger mb-3 mt-3 bottom-button" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</button>

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel"><span class="text-danger">Add New Product?</span></h5>
                </div>
                <div class="modal-body">
                    <!-- Info Note -->
                    <div class="alert alert-info" role="alert" style="text-align: justify;">
                        <strong>Note:</strong> Ensure that all fields are filled out correctly. The product name should describe the item, the unit is the measurement used, and the category helps organize the product. After submission, the product will be added to the inventory.
                    </div>

                    <!-- Add Product Form -->
                    <form id="addProductForm" action="" method="post">
                        <div class="row">
                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="productName" name="productName" placeholder="Ex: Soap" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="unit1" class="form-label">Unit</label>
                                    <select id="unitDropdown" name="unit" class="form-select" required>
                                        <option value="">Select here</option>
                                        <option value="kls">kls</option>
                                        <option value="gal">gal</option>
                                        <option value="pcs">pcs</option>
                                        <option value="tubes">tubes</option>
                                        <option value="rolls">rolls</option>
                                        <option value="can">can</option>
                                        <option value="trs">ltrs</option>
                                        <option value="pairs">pairs</option>
                                        <option value="other">other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <label for="unit2" class="form-label">Category</label>
                                    <select id="unitDropdown" name="cat" class="form-select" required>
                                        <option value="">Select here</option>
                                        <option value="Consumables">Consumables (Products that can be scarce)</option>
                                        <option value="Returnables">Returnables (Products that can be returned)</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Add Product</button>
                </div>
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

<script>
  $(document).ready(function() {
    // Initialize DataTable
    $('#productTable').DataTable(
        {
            "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]] // Limit to only 5 entries and set "5" as one of the choices
        }
    );
    
  });
</script>

<script>
    $(document).ready(function() {
    // Check if update was successful
    if (typeof updateSuccess !== 'undefined') {
        if (updateSuccess) {
            $('#successModal').modal({
                backdrop: 'static',  // Prevents closing the modal when clicking outside
                keyboard: false      // Prevents closing the modal with ESC
            }).modal('show');

            // Redirect after a delay (e.g., 3 seconds)
            // setTimeout(function() {
            //     redirectToPage();
            // }, 3000);
        } else if (deleteSuccess) {
          $('#errorModal').modal({
                backdrop: 'static',  // Prevents closing the modal when clicking outside
                keyboard: false      // Prevents closing the modal with ESC
            }).modal('show');
        } else if (insertSuccess) {
            $('#errorModal').modal({
                backdrop: 'static',  // Prevents closing the modal when clicking outside
                keyboard: false      // Prevents closing the modal with ESC
            }).modal('show');
        } else {
            $('#errorModal').modal({
                backdrop: 'static',  // Prevents closing the modal when clicking outside
                keyboard: false      // Prevents closing the modal with ESC
            }).modal('show');
        }
    }
});

    function redirectToPage() {
        window.location.href = 'masterlist.php';
    }
</script>

<script src="static/script.js"></script>
<script src="static/swipe.js"></script>
</body>
</html>
