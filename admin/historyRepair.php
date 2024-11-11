<?php
require('../dbcred/db.php');

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}


$sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE status = 'Approved' OR status = 'Rejected' ;";
$listmr = mysqli_query($db, $sqlgetmr);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auxiliary | Repair History</title>
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
    <h2>Repair History</h2>
    <br>
    <table class="table table-striped" id="minorRepairTable">
        <thead class="table-dark">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($data = mysqli_fetch_assoc($listmr)) { ?>
            <tr>
                <td><a href="#" class="requestLink" data-bs-toggle="modal" data-bs-target="#requestDetailsModal<?php echo $data['id']?>"><?php echo $data['name']?></a></td>
                <!-- Form Modal -->
                <div class="modal fade" id="requestDetailsModal<?php echo $data['id']?>" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="requestModalLabel">Request by <?php echo $data['name']?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row">
                                        <h6>Request By:<span class="text-danger"><?php echo $data['name']?> - <?php echo $data['datetime']?></span></h6>
                                        <h6>Status:<span class="text-danger"><?php echo $data['status']?> | Reason:  <?php echo $data['approval']?></span></h6>
                                        <hr>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name:</label>
                                                <p class="text-success"><?php echo $data['name']?></p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="pincode" class="form-label">Position:</label>
                                                <p class="text-success"><?php echo $data['position']?></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="department" class="form-label">Department:</label>
                                                <p class="text-success"><?php echo $data['department']?></p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email:</label>
                                                <p class="text-success"><?php echo $data['email']?></p>
                                            </div>
                                        </div>

                                        <h6>Description Property:</h6>
                                        <hr>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="type" class="form-label">Type:</label>
                                                <p class="text-success"><?php echo $data['type']?></p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="serial_num" class="form-label">Serial No.:</label>
                                                <p class="text-success"><?php echo $data['serial']?></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="brandmodel" class="form-label">Brand Model:</label>
                                                <p class="text-success"><?php echo $data['brandmodel']?></p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="propertyNo" class="form-label">Property No.:</label>
                                                <p class="text-success"><?php echo $data['propertyno']?></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="acqusitionCost" class="form-label">Acquisition Cost:</label>
                                                <p class="text-success"><?php echo $data['acqcost']?></p>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="mb-3">
                                                <label for="acqusitionDate" class="form-label">Acquisition Date:</label>
                                                <p class="text-success"><?php echo $data['acqdate']?></p>
                                            </div>
                                        </div>
                                        <h6>Defects and Complaints:</h6>
                                        <hr>
                                        <div class="col-sm-12">
                                            <div class="mb-3">
                                                <label for="message" class="form-label">Nature and Scope of Works to de done:</label>
                                                <p class="text-success"><?php echo $data['scope']?></p>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
                <td><?php echo $data['status']?></td>
                <td><?php echo $data['datetime']?></td>
                <td>
                    <?php if ($data['status'] == "Pending") { ?>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#approveModal">
                            <i class="bi bi-check-circle-fill"></i> Approve
                        </button>
                        <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel<?php echo $data['id'] ?>"
                             aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="approveModalLabel<?php echo $data['id'] ?>">Approve Repair</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to approve this request?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                        </button>
                                        <form method="post" action="">
                                            <input type="hidden" class="form-control" name="approverepair"
                                                   value="<?php echo $data['id'] ?>">
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle-fill"></i> Reject
                        </button>
                    <?php } ?>
                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $data['id'] ?>"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="rejectModalLabel<?php echo $data['id'] ?>">Reject Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <form method="post" action="">
                                        <label for="productName" class="form-label">Rejection Reason</label>
                                        <input type="text" class="form-control" name="reason" required placeholder="Reason for Rejection">
                                        Are you sure you want to reject this request?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                                    </button>
                                    <form method="post" action="">
                                        <input type="hidden" class="form-control" name="rejectmr"
                                               value="<?php echo $data['id'] ?>">
                                        <button type="submit" class="btn btn-danger">Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href='../repair - userReview.php?repairid=<?php echo $data['id'];?>'><button type="submit" class="btn btn-success">View Form</button></a>
                </td>
            </tr>
        <?php } ?>
        <!-- Add more rows as needed -->
        </tbody>
    </table>
</div>
</section>
<script src="static/script.js"></script>
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

</body>
</html>
