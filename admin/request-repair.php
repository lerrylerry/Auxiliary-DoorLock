<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
}


$sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE status = 'Approved' OR status = 'Rejected' ;";
$listmr = mysqli_query($db, $sqlgetmr);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Minor Repair History</title>
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
    <h2>Repair History</h2>
    <br>
    <table class="table table-striped" id="minorRepairTable">
        <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Date</th>
            <th>Status</th>
            <!-- <th>Action</th> -->
        </tr>
        </thead>
        <tbody>
        <?php while ($data = mysqli_fetch_assoc($listmr)) { ?>
            <tr>
                <td><a href="#" class="requestLink" data-bs-toggle="modal" data-bs-target="#requestDetailsModal<?php echo $data['id']?>"><?php echo $data['name']?></a></td>
                <!-- Modal Structure -->
                <div class="modal fade" id="requestDetailsModal<?php echo $data['id']?>" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                        <div class="modal-body">
                            <!-- Center the card inside the modal -->
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-6">
                                    <!-- Card container with shadow and rounded corners -->
                                    <div class="card shadow-md border border-secondary rounded">
                                        <!-- Card Header with title -->
                                        <div class="card-header text-center">
                                            <h3>Assessment Form</h3>
                                        </div>

                                        <!-- Card Body with form details -->
                                        <div class="card-body" style="background-color: #f5f5f5; border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
                                            <h4 class="mb-4 text-center" style="color: #9e1b32;">Request Details</h4>

                                            <!-- Form Rows for request details (Inline Layout) -->
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Name:</strong></label>
                                                <div><?php echo $data['name']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Position:</strong></label>
                                                <div><?php echo $data['position']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Department:</strong></label>
                                                <div><?php echo $data['department']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Type:</strong></label>
                                                <div><?php echo $data['type']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Serial No:</strong></label>
                                                <div><?php echo $data['serial']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Brand/Model:</strong></label>
                                                <div><?php echo $data['brandmodel']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Property No:</strong></label>
                                                <div><?php echo $data['propertyno']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Acquisition Cost:</strong></label>
                                                <div><?php echo $data['acqcost']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Acquisition Date:</strong></label>
                                                <div><?php echo $data['acqdate']; ?></div>
                                            </div>
                                            <div class="form-group mb-3 p-2 border rounded" style="background-color: #ffffff; display: flex; justify-content: space-between; align-items: center;">
                                                <label><strong>Defects and Complaints:</strong></label>
                                                <div><?php echo $data['scope']; ?></div>
                                            </div>
                                        </div>

                                        <!-- Card Footer with Close button -->
                                        <div class="card-footer text-center">
                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <td><?php echo $data['status']?></td>
                <td><?php echo date("F j, Y g:i A", strtotime($data['datetime'] . ' +8 hours')); ?></td>
                <td>
                <a class="btn btn-secondary btn-sm" href="generatepdf.php?id=<?php echo $data['id']; ?>&name=<?php echo urlencode($data['name']); ?>
                                            &position=<?php echo urlencode($data['position']); ?>&department=<?php echo urlencode($data['department']); ?>
                                            &email=<?php echo urlencode($data['email']); ?>&type=<?php echo urlencode($data['type']); ?>
                                            &serial=<?php echo urlencode($data['serial']); ?>&brandmodel=<?php echo urlencode($data['brandmodel']); ?>
                                            &propertyno=<?php echo urlencode($data['propertyno']); ?>&acqcost=<?php echo urlencode($data['acqcost']); ?>
                                            &acqdate=<?php echo urlencode($data['acqdate']); ?>&scope=<?php echo urlencode($data['scope']); ?>
                                            &datetime=<?php echo urlencode($data['datetime']); ?>">
                                            <i class="bi bi-printer"></i>
                                        </a>
                </td>
            </tr>
        <?php } ?>
        <!-- Add more rows as needed -->
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
    $(document).ready(function() {
        $('#minorRepairTable').DataTable();
    });
</script>

<script>
    function toggleFields(action) {
        var repairFields = document.getElementById("repairFields");
        var remarks = document.getElementById("remarks");
        if (action === 'approved') {
            repairFields.style.display = "block";
        } else {
            repairFields.style.display = "none";
            remarks.style.display = "block";
        }
    }
</script>

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<div class="modal fade" id="declineModal" tabindex="-1" aria-labelledby="declineModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="declineModalLabel">Decline Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" rows="3" placeholder="Enter your remarks"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Decline</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="static/script.js"></script>
<script src="static/swipe.js"></script>
</body>
</html>
