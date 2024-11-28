<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
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
          echo "<script>alert('Product added successfully!');</script>"; // Optional success message
      } else {
          // Product already exists
          echo "<script>alert('Product already exists!');</script>";
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
      echo "<script>var updateSuccess = true;</script>";
  } else {
      echo "<script>var updateSuccess = false;</script>";
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
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <style>
    .table {
            margin-top: 15px;
        }

        .table th, .table td {
            text-align: center;
            padding: 12px;
        }

        .table th {
            background-color: #9e1b32;
            color: white;
        }

        .table td {
            background-color: #f9f9f9;
        }
        .active>.page-link, .page-link.active{
            background-color: #9e1b32;
            border-color: #9e1b32; 
        }
        .page-link {
            color: #9e1b32;
        }
        /* Ensure modal labels are left-aligned */
        .modal-body .form-label .namezzz {
            text-align: left !important;
            font-weight: bold;
        }

        /* Ensure form controls take the full width */
        .modal-body .form-control, 
        .modal-body .form-select {
            width: 100%;
        }

        /* Adjust modal dialog and content styling */
        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .modal-content {
            padding: 20px;
            border-radius: 8px;
        }

        /* Modal header styling */
        .modal-header {
            border-bottom: 2px solid #9e1b32;
            background-color: #f8f9fa;
        }

        /* Title styling in the modal header */
        .modal-title {
            color: #9e1b32;
            font-weight: bold;
        }

        /* Button styling */
        .modal-footer .btn {
            padding: 8px 20px;
            font-weight: 600;
        }

        /* Modal footer flex to align buttons */
        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        
  </style>
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
                                    <option value="Consumables">Consumables (Products that can be consume)</option>
                                    <option value="Returnables">Returnables (Products that can be return)</option>
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
        <button type="button" class="btn btn-danger btn-sm deleteBtn bi bi-trash-fill" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id']?>"></button>
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
    $('#productTable').DataTable(
        {
            "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]] // Limit to only 5 entries and set "5" as one of the choices
        }
    );
    
  });
</script>

<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel"><span class="text-danger">Add New Product?</span></h5>
            </div>
            <div class="modal-body">
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

</section>

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
<script>
  $(document).ready(function() {
    // Check window size and disable toggle functionality for smaller screens
    if ($(window).width() <= 768) {
        // If the window is mobile-sized, disable the open/close functionality
        $(".sidebar").removeClass("close");
    }

    // Add your sidebar toggle functionality here for larger screens if needed
    $(window).resize(function() {
        if ($(window).width() <= 768) {
            $(".sidebar").removeClass("close");
        }
    });
});

</script>

<script src="static/script.js"></script>
</body>
</html>
