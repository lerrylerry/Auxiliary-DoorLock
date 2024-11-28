<?php
require('../dbcred/db.php');


session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}

$sqlhistems = "SELECT * FROM `tbprodhis`;";
$listhistemd = mysqli_query($db, $sqlhistems);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Add Item History</title>
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
<?php include('static/sidebar.php') ?>
<section class="home-section" style="overflow-y: auto;">
<div class="home-content">
<i class='bx bx-menu'></i>
</div>
<!-- Item Request Table -->
<div class="container mt-5">
    <h2>Add Items History</h2>
    <table id="addHistoryTable" class="table table-striped table-bordered">
    <thead class="table-dark">
        <tr>
            <th>DR No.</th>
            <th>Product</th>
            <th>Unit</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>Date Added</th>
            <th>Added By</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($data = mysqli_fetch_assoc($listhistemd)) { ?>
        <tr>
            <td><?php echo $data['drnum'] ?></td>
            <td><?php echo $data['name'] ?></td>
            <td><?php echo $data['units'] ?></td>
            <td><?php echo $data['quantity'] ?></td>
            <td><?php echo $data['category'] ?></td>
            <td><?php echo date("F j, Y", strtotime($data['dateAdded'])) ?></td>
            <td><?php echo $data['addedBy'] ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

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
    $(document).ready(function () {
        $('#addHistoryTable').DataTable();
    });
</script>

<script src="static/script.js"></script>

</body>
</html>
