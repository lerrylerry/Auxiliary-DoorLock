<?php

// Start the session at the beginning of the script
session_start();

// Ensure that the user is logged in by checking the session
if (!isset($_SESSION['loginid'])) {
    header("location: ../login.php");
    exit();
}

// Include database connection file
require('../dbcred/db.php');

// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Initialize PHPMailer
$mail = new PHPMailer(true);

// Fetch the logged-in user's name from the database
$assignedByName = 'Unknown';  // Default name in case something goes wrong

if (isset($_SESSION['loginid'])) {
    $loginId = $_SESSION['loginid'];  // Get the logged-in user's ID

    // Query the database to fetch the user's name using their login ID
    $query = "SELECT name FROM tbadmin WHERE id = '$loginId'";
    $result = mysqli_query($db, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the user's name from the result
        $userData = mysqli_fetch_assoc($result);
        $assignedByName = $userData['name'];  // Get the name of the logged-in user
    }
}

// Initialize message variables
$successMessage = "";
$errorMessage = "";

// Handle form submissions (Insert, Update, Approve, Reject)

if (isset($_POST['name'])) {
    // Insert a new record into tbmp table
    $sqlinsert = "INSERT INTO `tbmp`(`name`, `email`) VALUES ('" . $_POST['name'] . "','" . $_POST['email'] . "')";
    mysqli_query($db, $sqlinsert);
}

if (isset($_POST['updateupid'])) {
    // Update an existing record in tbmp table
    $sqlupdateup = "UPDATE `tbmp` SET `name`='" . $_POST['updatename'] . "', `email`='" . $_POST['updateemail'] . "' WHERE id='" . $_POST['updateupid'] . "'";
    mysqli_query($db, $sqlupdateup);
}

// Handle repair approval
if (isset($_POST['approverepair'])) {
    $sqlupdater = "UPDATE `tbminorrepair` SET `status`='Approved', `approval`='Approved' WHERE id='" . $_POST['approverepair'] . "'";
    mysqli_query($db, $sqlupdater);

    $sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE id ='" . $_POST['approverepair'] . "'";
    $resmail = mysqli_fetch_assoc(mysqli_query($db, $sqlgetmr));

    $subject = "Your Minor Repair Request Status";
    $body = "Dear $name,\n\nYour minor repair request has been $status.";

    $body .= "Best regards,\nTUP Auxiliary System";

    // Email sending for approval
    try {
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';
        $mail->Password = 'vdbwgupzfybcixsk';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
        $mail->addAddress($resmail['email'], $resmail['name']);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Handle repair rejection
if (isset($_POST['rejectmr'])) {
    $sqlupdater = "UPDATE `tbminorrepair` SET `status`='Rejected' WHERE id='" . $_POST['rejectmr'] . "'";
    mysqli_query($db, $sqlupdater);

    // Email sending for rejection
    $sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE id ='" . $_POST['rejectmr'] . "'";
    $resmail = mysqli_fetch_assoc(mysqli_query($db, $sqlgetmr));

    try {
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';
        $mail->Password = 'vdbwgupzfybcixsk';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
        $mail->addAddress($resmail['email'], $resmail['name']);
        $mail->isHTML(true);
        $mail->Subject = 'Minor Repair Request Rejected';
        $mail->Body = 'Your Minor Repair Request has been Rejected Due to ' . $_POST['reason'] . ' <br> ' ;
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch names from the tbmp table
$sqlgetup = "SELECT id, name FROM tbmp WHERE `status` = 'active';"; 
$listup = mysqli_query($db, $sqlgetup);

// Fetch pending repair requests from the tbminorrepair table
$sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE `status` = 'Pending';";
$listmr = mysqli_query($db, $sqlgetmr);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Minor Repair</title>
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

        

        .modal-footer {
            border-top: 2px solid #dee2e6;
        }

        .card {
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            background-color: #f8f9fa;
        }

        .text-muted {
            font-size: 0.95rem;
        }

        .fw-bold {
            font-weight: bold;
        }

        .modal-header {
            border-bottom: 2px solid #dee2e6;
        }

        .modal-footer {
            border-top: 2px solid #dee2e6;
        }

        .card {
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            background-color: #f8f9fa;
        }

        .text-muted {
            font-size: 0.95rem;
        }

        .fw-bold {
            font-weight: bold;
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
            <h2>Minor Repair</h2>
            <br>

            <table class="table table-striped" id="requestTable">
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
                <td class="w-25">
                    <a href="#" class="requestLink" data-bs-toggle="modal" data-bs-target="#requestDetailsModal<?php echo $data['id']?>"><?php echo $data['name']?></a>
                </td>

                <!-- Modal Structure -->
                <div class="modal fade" id="requestDetailsModal<?php echo $data['id']?>" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
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









                    <td class="w-25"><?php echo $data['status']?></td>
                    <td class="w-25"><?php echo date("F j, Y g:i A", strtotime($data['datetime'])); ?></td>
                    <td class="w-25">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="bi bi-check-circle-fill"></i></button>

                    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel<?php echo $data['id'] ?>"aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="approveModalLabel<?php echo $data['id'] ?>">Approve Repair Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to accept this request?
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

                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-circle-fill"></i></button>

                    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $data['id'] ?>" aria-hidden="true">
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



        <!-- Bootstrap JS (jQuery is required) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

        <!-- Bootstrap Bundle (Popper.js is required) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.10.2/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <script>
        $(document).ready(function() {
            $('#requestTable').DataTable();
        });
        </script>

        <!-- Latest compiled JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="static/script.js"></script>
        </section>
    </body>
</html>