<?php
require('../dbcred/db.php');


session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}


$sqlgetbr= "SELECT tbborrow.id as mainid,tbup.name,tbborrow.userid,tbup.id,tbborrow.status,tbborrow.datetime FROM `tbborrow` LEFT JOIN tbup ON tbborrow.userid = tbup.id WHERE tbborrow.status ='Approved' OR tbborrow.status = 'Rejected' ORDER BY mainid DESC;";
$listsimply = mysqli_query($db, $sqlgetbr);

$sqlgetreturn = "SELECT tbreturn.id as mainid,tbup.name,tbreturn.userid,tbup.id,tbreturn.status,tbreturn.datetime FROM `tbreturn` LEFT JOIN tbup ON tbreturn.userid = tbup.id WHERE tbreturn.status ='Approved' OR tbreturn.status = 'Rejected'ORDER BY mainid DESC;";
$listreturn= mysqli_query($db, $sqlgetreturn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Borrow/Return Request History</title>
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
    <h2>Request History</h2>
    <table id="itemRequestTable" class="table table-striped table-bordered">
        <thead class="table-dark">
        <tr>
            <th>Person</th>
            <th>Service</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
        <br>
        </thead>
        <tbody>
        <?php while ($data = mysqli_fetch_assoc($listsimply)) { ?>
            <tr>
                <td><a href="#" class="personLink" data-bs-toggle="modal"
                       data-bs-target="#personDetailsModal<?php echo $data['mainid'] ?>"><?php echo $data['name'] ?></a>
                    <?php
                    $sqlgetitems = "SELECT * FROM tbborrow INNER JOIN  tbpendingborrow ON tbpendingborrow.transid = tbborrow.id INNER JOIN tbproductlist ON tbproductlist.id = tbpendingborrow.itemid WHERE tbpendingborrow.userid = '" . $data['userid'] . "' AND tbborrow.id = '" . $data['mainid'] . "'";
                    $listitems = mysqli_query($db, $sqlgetitems);
                    //                    echo $sqlgetitems;
                    ?>
                    <!-- Person Details Modal -->
                    <div class="modal fade" id="personDetailsModal<?php echo $data['mainid'] ?>" tabindex="-1"
                         aria-labelledby="personDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="personDetailsModalLabel">Items Requested</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped" id="personModal">
                                        <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php while ($itelis = mysqli_fetch_assoc($listitems)) { ?>
                                            <tr>
                                                <td><?php echo $itelis['name'] ?></td>
                                                <td><?php echo $itelis['unit'] ?></td>
                                                <td><?php echo $itelis['borrowqty'] ?></td>
                                            </tr>
                                        <?php } ?>
                                        <!-- Additional rows can be added dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </td>
                <td>Borrow</td>
                <td><?php echo $data['status'] ?></td>
                <td><?php echo date("F j, Y g:i A", strtotime($data['datetime'])); ?></td>
            </tr>
        <?php } ?>
        <?php while ($datar = mysqli_fetch_assoc($listreturn)) { ?>
            <tr>
                <td><a href="#" class="personLink" data-bs-toggle="modal"
                       data-bs-target="#personDetailsModalret<?php echo $datar['mainid'] ?>"><?php echo $datar['name'] ?></a>
                    <?php
                    $sqlgetitemsret = "SELECT * FROM tbborrow INNER JOIN  tbpendingborrow ON tbpendingborrow.transid = tbborrow.id INNER JOIN tbproductlist ON tbproductlist.id = tbpendingborrow.itemid WHERE tbpendingborrow.userid = '" . $datar['userid'] . "' AND tbborrow.id = '" . $datar['mainid'] . "'";
                    $listitemsret = mysqli_query($db, $sqlgetitemsret);
                    //                    echo $sqlgetitems;
                    ?>
                    <!-- Person Details Modal -->
                    <div class="modal fade" id="personDetailsModalret<?php echo $datar['mainid'] ?>" tabindex="-1"
                         aria-labelledby="personDetailsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="personDetailsModalLabel">Items Requested</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-striped" id="personModal">
                                        <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit</th>
                                            <th>Quantity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php while ($itelisret = mysqli_fetch_assoc($listitemsret)) { ?>
                                            <tr>
                                                <td><?php echo $itelisret['name'] ?></td>
                                                <td><?php echo $itelisret['unit'] ?></td>
                                                <td><?php echo $itelisret['borrowqty'] ?></td>
                                            </tr>
                                        <?php } ?>
                                        <!-- Additional rows can be added dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                </td>
                <td>Return</td>
                <td><?php echo $datar['status'] ?></td>
                <td><?php echo date("F j, Y g:i A", strtotime($datar['datetime'] . ' +8 hours')); ?></td>
            </tr>
        <?php } ?>

        <!-- Additional rows can be added dynamically -->
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
        $('#itemRequestTable').DataTable();

        // Handle click event on person link to populate modal with product details
        $('.personLink').on('click', function () {
            $('#productName').val('Product A').prop('disabled', true);
            $('#unit').val('PCS').prop('disabled', true);
            $('#quantity').val('10').prop('disabled', true);
        });
    });
    $(document).ready(function () {
        $('#personModal').DataTable({
            "paging": false, // Disable pagination
            "searching": false, // Disable search
            "info": false // Disable show entries info
        });
    });
</script>

<script src="static/script.js"></script>
<script src="static/swipe.js"></script>
</body>
</html>
