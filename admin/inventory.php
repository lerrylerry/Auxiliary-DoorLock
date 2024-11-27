<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

// Set the current user
$currentUser = $_SESSION['name']; // Adjust according to how the current user is stored




// Fetch the drnum from tbdrnum where id = 1
$query = "SELECT `name` FROM `tbdrnum` WHERE `id` = 1";
$result = mysqli_query($db, $query);
$drnum = ''; // Default to empty if no value is found

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $drnum = $row['name']; // Get the drnum from the database
}

// If drnum is not empty, set the flag to lock the field
$isLocked = !empty($drnum); // If drnum is not empty, lock the field




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the drnum from POST (either from input field or from the database value)
    $drnum = $_POST['drnum'];

    // If drnum is empty, that means the user is allowed to input a new one
    if (empty($drnum)) {
        // Fetch the value from the database (fallback to the value already set)
        $query = "UPDATE `name` FROM `tbdrnum` WHERE `id` = 1";
        $result = mysqli_query($db, $query);
        $row = mysqli_fetch_assoc($result);
        $drnum = $row['name'];  // Use the existing drnum from DB if not set by the user
    } else {
        // If the user entered a new drnum, update the database with the new value
        $query = "UPDATE `tbdrnum` SET `name` = ? WHERE `id` = 1";
        if ($stmt = mysqli_prepare($db, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $drnum);
            if (mysqli_stmt_execute($stmt)) {
                echo "DRNum updated successfully!";
            } else {
                echo "Error updating DRNum!";
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Insert or update other form data (like products) here...
}


// Fetch the latest 'drnum' from the tbdrnum table
$sql = "SELECT name FROM tbdrnum WHERE id = 1"; // Adjust the WHERE clause to fit your logic (e.g., use the latest drnum)
$result = mysqli_query($db, $sql);

// Initialize $drnum to an empty string
$drnum = '';

// Check if the query is successful and we have a result
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $drnum = $row['name']; // Assign the fetched drnum value
}

//---------------------------------------------------------------------------------------ADD STOCKS//
if (isset($_POST['insertid'])) {
  $sqlinsert = "INSERT INTO `tbpendingadd`(`name`, `quantity`) 
              VALUES ('" . $_POST['name'] . "','" . $_POST['quantity'] . "')";

  if (mysqli_query($db, $sqlinsert)) {
      echo "<script>var insertSuccess = true;</script>";
  } else {
      echo "<script>var insertSuccess = false;</script>";
  }
}

//---------------------------------------------------------------------------------------UPDATE STOCKS//
if (isset($_POST['updatepid'])) {
  $sqlupdateup = "UPDATE `tbproductlist` SET `name`='".$_POST['updatename']."', `unit`='".$_POST['updateunit']."', `quantity`='".$_POST['updateqty']."', `category`='".$_POST['updatecat']."' WHERE id='".$_POST['updatepid']."'";
  if (mysqli_query($db, $sqlupdateup)) {
      echo "<script>var updateSuccess = true;</script>";
  } else {
      echo "<script>var updateSuccess = false;</script>";
  }
}

//---------------------------------------------------------------------------------------DELETE//
if (isset($_POST['delete'])) {
  $sqldeleteitem = "DELETE FROM `tbpendingadd` WHERE id='" . $_POST['delete'] . "'";
  if (mysqli_query($db, $sqldeleteitem)) {
    echo "<script>var deleteSuccess = true;</script>";
  } else {
      echo "<script>var deleteSuccess = false;</script>";
  }
}

//---------------------------------------------------------------------------------------DELETE ALL//
if (isset($_POST['deleteall'])) {
  $sqldeleteitem = "DELETE FROM `tbpendingadd`";
  mysqli_query($db, $sqldeleteitem);
}

$sqlprod = "SELECT * FROM tbproductlist ORDER BY name ASC;";
$listprod = mysqli_query($db, $sqlprod);

$sqlgetp = "SELECT id,unit,name,quantity,category FROM `tbproductlist`;";
$listp = mysqli_query($db, $sqlgetp);


$sqltemp = "SELECT * FROM tbpendingadd";
$listtemp = mysqli_query($db, $sqltemp);

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Inventory</title>
  <link rel="stylesheet" href="../css/styles.css">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
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
<body>
<?php include('static/sidebar.php')?>

<section class="home-section" style="overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
<div class="container mt-5">
  <h2>Inventory</h2>
  <table id="productTable" class="table">
    <thead class="table-dark">
      <tr>
        <th>Product</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Category</th>
        <!-- <th>Status</th> -->
        <th>Action</th>
      </tr>
      <br>
    </thead>
    <tbody>
    <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
      <tr>
          <td><?php echo $data['name']; ?></td>
          <td><?php echo $data['unit']; ?></td>
          <td><?php echo $data['quantity']; ?></td>
          <td><?php echo $data['category']; ?></td>
        <td>
          <!-- Edit Button -->
          <button type="button" class="btn btn-primary btn-sm editBtn bi bi-pencil-fill" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $data['id']?>"></button>
            <!-- Edit Modals -->
            <div class="modal fade" id="editModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="editModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel1"><span class="text-danger fw-bold">Edit a quantity?</span></h5>
                        </div>
                        <div class="modal-body">
                            <!-- Edit Form -->
                            <form method="post" action="">
                                <div class="mb-3">
                                    <label for="productName1" class="form-label fw-bold namezzz">Product:</label>
                                    <input type="hidden" class="form-control" id="productName1" name="updatepid" value="<?php echo $data['id']?>">
                                    <input type="hidden" class="form-control" id="productName1" name="updatename" value="<?php echo $data['name']?>">
                                    <input type="text" class="form-control" id="productName1" name="updatename" value="<?php echo $data['name']?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="unit1" class="form-label fw-bold namezzz">Unit:</label>
                                    <input type="hidden" name="updateunit" class="form-select" value="<?php echo $data['unit']?>">
                                    <select id="unitDropdown" name="updateunit" class="form-select" disabled>
                                        <option value="<?php echo $data['unit']?>"><?php echo $data['unit']?></option>
                                        <option value="kls">kls</option>
                                        <option value="gal">gal</option>
                                        <option value="pcs">pcs</option>
                                        <option value="tubes">tubes</option>
                                        <option value="rolls">rolls</option>
                                        <option value="can">can</option>
                                        <option value="ltrs">ltrs</option>
                                        <option value="pairs">pairs</option>
                                        <option value="other">other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantity1" class="form-label fw-bold namezzz">Quantity:</label>
                                    <input type="number" class="form-control" name="updateqty" id="quantity1" value="<?php echo $data['quantity']?>" required min="1">
                                </div>
                                <div class="mb-3">
                                  <label for="unit2" class="form-label fw-bold namezzz">Category:</label>
                                  <input type="hidden" name="updatecat" class="form-select" value="<?php echo $data['category']?>">
                                  <select id="unitDropdown" name="updatecat" class="form-select" disabled>
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
          <!-- <button type="button" class="btn btn-danger btn-sm deleteBtn bi bi-trash-fill" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $data['id']?>"> Delete</button> -->
            <!-- Delete Modals -->
            <div class="modal fade" id="deleteModal<?php echo $data['id']?>" tabindex="-1" aria-labelledby="deleteModalLabel1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel1"><span class="text-danger">Delete a product ?</span></h5>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete <span class="text-primary"><?php echo $data['name']?> </span>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form method="post" action="" >
                                <input type="hidden" class="form-control" id="" name="deleteid" value="<?php echo $data['id']?>">
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
  <!-- <button type="button" class="btn btn-danger mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add Stocks</button> -->
<button type="button" class="btn btn-danger mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#massAddProductModal">Add Stocks</button>
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
    var table = $('#productTable').DataTable({
        "lengthMenu": [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]], // Limit to only 5 entries and set "5" as one of the choices
    });

    // Bind keyup event to the search input field
    $('#productTable_filter input').on('keyup', function () {
        // Get the value entered in the search input
        var searchValue = $(this).val().toLowerCase();

        // Filter the first column based on the search value
        table.column(0).search('^'+searchValue, true, false, true).draw();
    });
});

</script>

<!-- Mass Add Modal -->
<div class="modal fade" id="massAddProductModal" tabindex="-1" aria-labelledby="massAddProductModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" style="background: #580606;">
        <h5 class="modal-title text-white" id="massAddProductModalLabel">Receiving Items</h5>
      </div>
      <div class="modal-body">
        <form action="" method="post">
          <div class="container-fluid mt-3">
            <div class="row">
            <!-- DRNo. Input Field -->
            <div class="col-4">
            <label class="mb-2">DRNo.:</label>
            <input type="text" class="form-control" id="drnum" name="drnum" value="<?php echo htmlspecialchars($drnum); ?>" <?php echo ($isLocked) ? 'disabled' : ''; ?> required>
            <input type="hidden" id="hiddenDrnum" name="drnum" value="<?php echo htmlspecialchars($drnum); ?>">
            </div>
              <!-- Added By Input Field -->
              <div class="col-4">
                <label class="mb-2">Added by:</label>
                <input type="text" class="form-control" id="addedBy" name="addedBy" value="<?= $currentUser; ?>" disabled>
              </div>
              <!-- Date Added Input Field -->
              <div class="col-4">
                <label class="mb-2">Date Added:</label>
                <input type="text" class="form-control" id="dateAdded" name="dateAdded" value="<?= date('m/d/Y'); ?>" disabled>
              </div>
            </div>
          </div>

          <!-- Product Table and Product Fields -->
          <div class="table-responsive" style="max-height: 300px; overflow-y: auto; margin-top: 15px;">
            <table id="productsTable" class="table table-striped text-center">
              <thead class="table-dark">
                <tr>
                  <th>PRODUCT DESCRIPTION</th>
                  <th>UNIT</th>
                  <th>QUANTITY</th>
                  <th>CATEGORY</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php
                  // Display products from the database
                  while ($row = mysqli_fetch_assoc($listtemp)) {
                      echo "<tr id='" . $row['id'] . "'>";
                      echo "<td>" . $row['name'] . "</td>";
                      echo "<td>" . $row['units'] . "</td>";
                      echo "<td>" . $row['quantity'] . "</td>";
                      echo "<td>" . $row['category'] . "</td>";
                      echo "<td>";
                      echo "<button type='button' class='btn btn-danger delete-btn' data-id='" . $row['id'] . "'><i class='bi bi-trash3-fill'></i></button>";
                      echo "</td>";
                      echo "</tr>";
                  }
                ?>
              </tbody>
            </table>
          </div>

          <!-- Add Product Form -->
          <div class="container-fluid mt-3">
            <hr class="fw-bold">
            <div class="row">
              <div class="col-6">
                <label class="mb-2">Product Description:</label>
                <select class="form-select mb-3" id="name" name="name" required>
                  <option value="">Select here</option>
                  <?php
                    // List product options from the database
                    while ($row = mysqli_fetch_assoc($listprod)) {
                        echo '<option value="' . $row['name'] . '">' . $row['name'] . '</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="col">
                <label class="mb-2">Quantity:</label>
                <input type="number" class="form-control plus mb-3" id="quantity" min="1" name="quantity" required>
              </div>
            </div>
            <div class="row">
              <div class="col-5">
                <label class="mb-2">Units:</label>
                <input type="text" class="form-control" id="units" name="units" disabled>
              </div>
              <div class="col-5">
                <label class="mb-2">Category:</label>
                <input type="text" class="form-control" id="category" name="category" disabled>
              </div>
              <div class="col">
                <label class="mb-2">Add Product</label>
                <button type="button" class="form-control bg-success text-white add-btn" id="addProductBtn"><i class="bi bi-plus-lg"> Add</i></button>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="modal-footer d-flex justify-content-between">
            <button type="button" class="btn btn-outline-danger deleteall-btn" id="deleteAllBtn">Delete All</button>
            <button type="button" class="btn btn-primary addnow-btn" id="addStocksBtn">Add Stocks</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<button type="button" class="btn btn-danger mb-3 mt-3" data-bs-toggle="modal" data-bs-target="#massAddProductModal">Add Stocks</button>


<!-- response modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white" style="background: #580606;">
        <h5 class="modal-title" id="responseModalLabel">
          <i class="bi bi-info-circle-fill"></i> Response
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <p class="text-center"><strong>Your action was successful!</strong></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="confirmBtn">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Response Modal for add stocks all-->
<div class="modal fade" id="responseModal3" tabindex="-1" aria-labelledby="responseModalLabel2" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background: #580606;">
        <h5 class="modal-title text-white" id="responseModalLabel2">Response</h5>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Response message will be injected here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for displaying messages add stock indiv -->
<div class="modal fade" id="responseModal4" tabindex="-1" aria-labelledby="responseModalLabel5" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel5">Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- The response message will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>



<script src="static/script.js"></script>
<script>
    $(document).ready(function() {
    $('#confirmBtn').on('click', function() {
      // Hide the modal
      $('#responseModal').modal('hide');
    });
  });
    $(document).ready(function() {
    // Name selection change event
    document.getElementById('name').addEventListener('change', function() {
        var selectedName = this.value;

        if (selectedName !== '') {
            // AJAX request to fetch corresponding data
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetchProduct.php?name=' + encodeURIComponent(selectedName), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = JSON.parse(xhr.responseText);

                    // Update units and category fields
                    document.getElementById('units').value = data.unit;
                    document.getElementById('category').value = data.category;
                }
            };
            xhr.send();
        } else {
            // Clear units and category fields
            document.getElementById('units').value = '';
            document.getElementById('category').value = '';
        }
    });

    // Delete button click event
    $('#productsTable').on('click', '.delete-btn', function() {
        var id = $(this).data('id');

        // AJAX request to delete the row
        $.ajax({
            type: 'POST',
            url: 'delete.php',
            data: { delete: id },
            success: function(response) {
                $('#' + id).remove(); // Remove row from table
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

    // Delete all rows button click event
    $('.deleteall-btn').click(function() {
        $.ajax({
            type: 'POST',
            url: 'deleteall.php',
            success: function(response) {
                $('#productsTable tbody').empty(); // Clear table content
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });

      // Handle product description submission
  $('#submitDescription').click(function() {
    var description = $.trim($('#productDescription').val());
    if (description) {
      // Here you can use the description for further processing
      $('#descriptionModal').modal('hide');
      // You can set the description to a hidden input or use it directly
      $('select[name="name"]').val(description);
      // Optionally trigger the add-btn click to proceed with adding the product
      $('.add-btn').click();
    }
  });
  









});


</script>

<script>
$(document).ready(function() {
    $('.add-btn').click(function() {
        // Get values from the form
        var name = $.trim($('select[name="name"]').val());
        var units = $.trim($('input[name="units"]').val());
        var quantity = $.trim($('input[name="quantity"]').val());
        var category = $.trim($('input[name="category"]').val());
        var drnum = $.trim($('#hiddenDrnum').val()) || $.trim($('#drnum').val());
        var addedBy = $.trim($('#addedBy').val());
        var dateAdded = $.trim($('#dateAdded').val());

        // Validate all fields
        if (!name || !units || !quantity || !category || !drnum) {
            showModal("Error", "Please fill out all required fields.");
            return;
        }

        // AJAX Request
        $.ajax({
            type: 'POST',
            url: 'add.php',
            data: { 
                drnum: drnum, 
                name: name, 
                units: units, 
                quantity: quantity, 
                category: category, 
                addedBy: addedBy, 
                dateAdded: dateAdded 
            },
            success: function(response) {
                // Parse the response if it's JSON
                var responseData = JSON.parse(response);

                // If there is a message or error, show it in the modal
                if (responseData.message) {
                    showModal("Error", responseData.message);
                    return;
                }

                // If the insert is successful (no message in response), clear form fields and add new row
                $('select[name="name"]').val('');
                $('input[name="units"]').val('');
                $('input[name="quantity"]').val('');
                $('input[name="category"]').val('');

                // Lock the drnum field by disabling it
                $('#drnum').prop('disabled', true);
                $('#hiddenDrnum').prop('disabled', true);

                // Add new row to the table
                var newRow = "<tr id='" + responseData.id + "'>";
                newRow += "<td>" + responseData.name + "</td>";
                newRow += "<td>" + responseData.units + "</td>";
                newRow += "<td>" + responseData.quantity + "</td>";
                newRow += "<td>" + responseData.category + "</td>";
                newRow += "<td><button type='button' class='btn btn-danger delete-btn' data-id='" + responseData.id + "'><i class='bi bi-trash3-fill'></i></button></td>";
                newRow += "</tr>";

                $('#productsTable').append(newRow);
            },
            error: function(xhr, status, error) {
                showModal("Error", "An error occurred while processing the request.");
            }
        });
    });

    // Function to show the modal with dynamic message
    function showModal(title, message) {
        $('#responseModalLabel').text(title);  // Set modal title
        $('#modalBody').text(message);  // Set modal body content
        $('#responseModal').modal('show');  // Show the modal
    }
});


</script>

<script>
$(document).ready(function() {
    // Add stocks button click event
    $('.addnow-btn').click(function() {
        $.ajax({
            type: 'POST',
            url: 'addnow.php',
            dataType: 'json',
            success: function(response) {
                console.log('Response:', response); // For debugging

                if (response) {
                    // Handle the response based on the status
                    if (response.status === 'success') {
                        // Show the success message in the modal
                        $('#modalBody').text(response.message);
                        $('#responseModal').modal('show');

                        // Reload the page after 2 seconds
                        setTimeout(function() {
                            location.reload();
                        }, 2000); // Reload after 2 seconds
                    } else if (response.status === 'no_data_added') {
                        // Handle no valid data message
                        $('#modalBody').text(response.message);
                        $('#responseModal').modal('show');
                    } else if (response.status === 'error') {
                        // Handle error messages
                        $('#modalBody').text(response.message);
                        $('#responseModal').modal('show');
                    }
                } else {
                    // Handle unexpected or empty response
                    console.error('Unexpected response:', response);
                    alert('Unexpected error, please try again later.');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error in AJAX request:", xhr.responseText);
                alert("An error occurred while processing the request.");
            }
        });
    });
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
        window.location.href = 'inventory.php';
    }
</script>

</body>
</html>