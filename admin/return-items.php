<?php
require('../dbcred/db.php');
session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: ../index.html");
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Function to send email notification
function sendEmailNotification($email, $name, $status, $itemDetails) {
    $subject = "Your Return Request Status";
    $body = "Dear $name,\n\nYour return request has been $status.\n\nDetails:\n$itemDetails\n\nBest regards,\nTUP Auxiliary System";

    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your app password (use App Password if 2FA is enabled)
        $mail->SMTPSecure = 'tls';  // TLS encryption
        $mail->Port = 587;  // SMTP port for TLS

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($email, $name);  // Add recipient email dynamically

        // Email content
        $mail->isHTML(false);  // Plain text email
        $mail->Subject = $subject;
        $mail->Body = $body;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['approvereturn'])) {
    // Update the return request status to Approved
    $sqlupdateb = "UPDATE `tbreturn` SET `status`='Approved' WHERE id='" . $_POST['approvereturn'] . "'";
    mysqli_query($db, $sqlupdateb);

    $sqlupdatedooraccess = "UPDATE `tbup` SET `dooraccess`='Approved' WHERE id='" . $_POST['userid'] . "'";
    mysqli_query($db, $sqlupdatedooraccess);

    // Update stock and fetch item details
    $sqlgetitems = "
        SELECT 
            tbreturn.id AS return_id, 
            tbpendingreturn.itemid, 
            tbpendingreturn.borrowqty, 
            tbpendingreturn.transid 
        FROM 
            tbreturn 
        INNER JOIN 
            tbpendingreturn ON tbpendingreturn.transid = tbreturn.id 
        WHERE 
            tbpendingreturn.userid = '" . $_POST['userid'] . "' 
            AND tbreturn.id = '" . $_POST['approvereturn'] . "'
    ";
    $listitems = mysqli_query($db, $sqlgetitems);

    $itemDetails = ''; // To hold the item details for the email

    while ($itelis = mysqli_fetch_assoc($listitems)) {
        // Fetch the item name and category
        $sqlCategory = "SELECT name, category FROM tbproductlist WHERE id='" . $itelis['itemid'] . "'";
        $resultCategory = mysqli_query($db, $sqlCategory);

        if ($resultCategory && mysqli_num_rows($resultCategory) > 0) {
            $rowCategory = mysqli_fetch_assoc($resultCategory);

            // Append item details to the email content
            $itemDetails .= "Item: " . $rowCategory['name'] . "\n";
            $itemDetails .= "Quantity: " . $itelis['borrowqty'] . "\n";
            $itemDetails .= "Category: " . $rowCategory['category'] . "\n\n";

            // Update stock based on the return quantity
            $sqlupdatestock = "UPDATE `tbproductlist` SET `quantity`=`quantity` + " . $itelis['borrowqty'] . " WHERE id='" . $itelis['itemid'] . "'";
            mysqli_query($db, $sqlupdatestock);
        }
    }

    // Get user's email and name for email notification
    $sqlgetuser = "SELECT email, name FROM tbup WHERE id = '" . $_POST['userid'] . "';";
    $user = mysqli_fetch_assoc(mysqli_query($db, $sqlgetuser));

    // Send email notification with item details
    $emailStatus = sendEmailNotification($user['email'], $user['name'], 'Approved', $itemDetails);
    if ($emailStatus) {
        // Success: Show success message and redirect to the same page
        echo "<script>alert('Email sent successfully! The return request has been approved.'); window.location = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        // Failure: Show error message and redirect to the same page
        echo "<script>alert('Failed to send email!'); window.location = '" . $_SERVER['PHP_SELF'] . "';</script>";
    }
}

if (isset($_POST['rejectreturn'])) {
    // Update the return request status to Rejected
    $sqlupdater = "UPDATE `tbreturn` SET `status`='Rejected' WHERE id='" . $_POST['rejectreturn'] . "'";
    mysqli_query($db, $sqlupdater);

    // Get user's email and name for email notification
    $sqlgetuser = "SELECT email, name FROM tbup WHERE id = '" . $_POST['userid'] . "';";
    $user = mysqli_fetch_assoc(mysqli_query($db, $sqlgetuser));

    // Send email notification
    $emailStatus = sendEmailNotification($user['email'], $user['name'], 'Rejected', '');
    if ($emailStatus) {
        // Success: Show success message and redirect to the same page
        echo "<script>alert('Email sent successfully! The return request has been rejected.'); window.location = '" . $_SERVER['PHP_SELF'] . "';</script>";
    } else {
        // Failure: Show error message and redirect to the same page
        echo "<script>alert('Failed to send email!'); window.location = '" . $_SERVER['PHP_SELF'] . "';</script>";
    }
}

$sqlgetsimplifiedlist = "SELECT tbreturn.id as mainid,tbup.name,tbreturn.userid,tbup.id,tbreturn.status FROM `tbreturn` LEFT JOIN tbup ON tbreturn.userid = tbup.id Where tbreturn.status = 'Pending' ORDER BY mainid DESC;";
$listsimply = mysqli_query($db, $sqlgetsimplifiedlist);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Auxiliary | Return Request</title>
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
  <i class ='bx bx-menu'></i>
  </div>
<!-- Item Request Table -->
<div class="container mt-5">
  <h2>Return Request</h2>
  <table id="itemRequestTable" class="table table-striped table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Person</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
      <br>
    </thead>
    <tbody>
    <?php while ($data = mysqli_fetch_assoc($listsimply)) { ?>
      <tr>
          <td><a href="#" class="personLink" data-bs-toggle="modal"
                 data-bs-target="#personDetailsModal<?php echo $data['mainid'] ?>"><?php echo $data['name'] ?></a>
              <?php
              $sqlgetitems = "SELECT * FROM tbreturn INNER JOIN  tbpendingreturn ON tbpendingreturn.transid = tbreturn.id INNER JOIN tbproductlist ON tbproductlist.id = tbpendingreturn.itemid WHERE tbpendingreturn.userid = '" . $data['userid'] . "' AND tbreturn.id = '" . $data['mainid'] . "'";
              $listitems = mysqli_query($db, $sqlgetitems);
              ?>
              <!-- Person Details Modal -->
              <div class="modal fade" id="personDetailsModal<?php echo $data['mainid'] ?>" tabindex="-1"
                   aria-labelledby="personDetailsModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="personDetailsModalLabel">Items Returning</h5>
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
          <td><?php echo $data['status'] ?></td>
          <td>
              <?php if ($data['status'] == "Pending") { ?>
                  <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                          data-bs-target="#approveModal<?php echo $data['mainid'] ?>">
                      <i class="bi bi-check-circle-fill"></i> Approve
                  </button>
                  <div class="modal fade" id="approveModal<?php echo $data['mainid'] ?>" tabindex="-1" aria-labelledby="approveModalLabel"
                       aria-hidden="true">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
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
                                      <input type="hidden" class="form-control" name="approvereturn"
                                             value="<?php echo $data['mainid'] ?>">
                                      <input type="hidden" class="form-control" name="userid"
                                             value="<?php echo $data['userid'] ?>">
                                      <button type="submit" class="btn btn-success">Approve</button>
                                  </form>

                              </div>
                          </div>
                      </div>
                  </div>
              <?php } ?>
              <?php if ($data['status'] == "Pending") { ?>
                  <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                          data-bs-target="#rejectModal<?php echo $data['mainid'] ?>">
                      <i class="bi bi-x-circle-fill"></i> Reject
                  </button>
              <?php } ?>
              <!-- Reject Modal -->
              <div class="modal fade" id="rejectModal<?php echo $data['mainid'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel"
                   aria-hidden="true">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                      aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              Are you sure you want to reject this request?
                          </div>
                          <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel
                              </button>
                              <form method="post" action="">
                                  <input type="hidden" class="form-control" name="rejectreturn"
                                         value="<?php echo $data['mainid'] ?>">
                                  <button type="submit" class="btn btn-danger">Reject</button>
                              </form>
                          </div>
                      </div>
                  </div>
              </div>

          </td>
      </tr>
    <?php } ?>
      <!-- Additional rows can be added dynamically -->
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
    $('#itemRequestTable').DataTable();

    // Handle click event on person link to populate modal with product details
    $('.personLink').on('click', function() {
      $('#productName').val('Product A').prop('disabled', true);
      $('#unit').val('PCS').prop('disabled', true);
      $('#quantity').val('10').prop('disabled', true);
    });
  });
  $(document).ready(function() {
    $('#personModal').DataTable({
      "paging": false, // Disable pagination
      "searching": false, // Disable search
      "info": false // Disable show entries info
    });
  });
</script>

<!-- Person Details Modal -->
<div class="modal fade" id="personDetailsModal" tabindex="-1" aria-labelledby="personDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="personDetailsModalLabel">Items Requested</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

            <tr>
              <td>Product A</td>
              <td>PCS</td>
              <td>10</td>
            </tr>
            <tr>
              <td>Product B</td>
              <td>KLS</td>
              <td>5</td>
            </tr>
            <!-- Additional rows can be added dynamically -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Approve Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to approve this request?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success">Approve</button>
      </div>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to reject this request?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger">Reject</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
