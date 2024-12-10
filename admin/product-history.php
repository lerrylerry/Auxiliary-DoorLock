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
    <title>Auxiliary | Products History</title>
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
            <!-- <th>Added By</th> -->
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
            <td>
            <?php 
            echo date("F j, Y g:i A", strtotime($data['dateAdded'] . ' +8 hours')); 
            ?>
            </td>
            <!-- <td><?php echo $data['addedBy'] ?></td> -->
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
<script src="static/swipe.js"></script>

</body>
</html>
