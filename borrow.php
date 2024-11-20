<?php
require('dbcred/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// if (isset($_POST['additem'])) {
//     $sqlinsertp = "INSERT INTO `tbpendingborrow`(`itemid`, `userid`,`borrowqty`) VALUES ('" . $_POST['additem'] . "','" . $_GET['userid'] . "','" . $_POST['qty'] . "')";
//     mysqli_query($db, $sqlinsertp);
// }

// Initialize message and modal type variables
$message = ""; 
$modalType = ""; 

if (isset($_POST['additem'])) {
    $itemid = $_POST['additem'];
    $userid = $_GET['userid'];
    $borrowqty = $_POST['qty'];

    // Check if the item has already been borrowed
    $sqlcheck = "SELECT * FROM `tbpendingborrow` 
                 WHERE `itemid` = '$itemid' 
                 AND `userid` = '$userid' 
                 AND `transid` = '0'";  // Check only unprocessed (transid = 0) requests

    $result = mysqli_query($db, $sqlcheck);

    if (mysqli_num_rows($result) > 0) {
        // Item already borrowed, don't insert
        $message = "This item has already been added to your borrow request. Please delete the first entry and try again. Thank You!";
        $modalType = "error"; // Set modal type to error
    } else {
        // Item not borrowed, proceed with insert
        $sqlinsertp = "INSERT INTO `tbpendingborrow`(`itemid`, `userid`, `borrowqty`) 
                       VALUES ('$itemid', '$userid', '$borrowqty')";
        if (mysqli_query($db, $sqlinsertp)) {
            // $message = "Item added to borrow request.";
            // $modalType = "success"; // Set modal type to success
        } else {
            $message = "Error adding item to borrow request.";
            $modalType = "error"; // Set modal type to error
        }
    }
}


if (isset($_POST['finalizerequest'])) {
    // Insert the borrow request into tbborrow table
    $sqlsubmitrequest = "INSERT INTO `tbborrow`(`userid`, `status`) 
                         VALUES ('" . $_POST['finalizerequest'] . "','Pending')";
    mysqli_query($db, $sqlsubmitrequest);

    // Get the transaction ID of the borrow request
    $transid = mysqli_insert_id($db);

    // Update pending borrow items with the transaction ID
    $sqlupdatetransid = "UPDATE `tbpendingborrow` 
                         SET `transid` = '$transid' 
                         WHERE userid = '" . $_POST['finalizerequest'] . "' 
                         AND transid = '0'";
    mysqli_query($db, $sqlupdatetransid);

    // Fetch user details (name and email) for sending the email
    $sqlgetcu = "SELECT id, name, email FROM `tbup` WHERE id = '" . $_POST['finalizerequest'] . "';";
    $listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

    // Fetch the list of items the user has borrowed
    $sqlgetitems = "SELECT tbproductlist.name, tbpendingborrow.borrowqty 
                    FROM tbpendingborrow 
                    LEFT JOIN tbproductlist ON tbpendingborrow.itemid = tbproductlist.id 
                    WHERE tbpendingborrow.userid = '" . $_POST['finalizerequest'] . "' 
                    AND tbpendingborrow.transid = '$transid'";
    $itemsresult = mysqli_query($db, $sqlgetitems);

    // Prepare email content
    $emailContent = "Dear " . $listcu['name'] . ",\n\n";
    $emailContent .= "Your borrow request has been processed. Below are the details of the items you have requested:\n\n";

    while ($item = mysqli_fetch_assoc($itemsresult)) {
        $emailContent .= "Item: " . $item['name'] . " | Quantity: " . $item['borrowqty'] . "\n";
    }

    $emailContent .= "\nThank you for using our service.\n\nBest regards,\nTUP Auxillary System";

    // Send email notification
    try {
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0;  // Disable debug output
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your app password (use App Password if 2FA is enabled)
        $mail->SMTPSecure = 'tls';  // TLS encryption
        $mail->Port = 587;  // SMTP port for TLS

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
        $mail->addAddress($listcu['email'], $listcu['name']);  // Add recipient email dynamically

        // Email content
        $mail->isHTML(false);  // Plain text email
        $mail->Subject = 'Borrow Request Confirmation';
        $mail->Body    = $emailContent;

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Redirect to another page after sending the email
    header("Location: borrowANDreturn.php");
}

if (isset($_POST['delete'])) {
    $sqldeleteitem = "DELETE FROM `tbpendingborrow` WHERE id='" . $_POST['delete'] . "'";
    mysqli_query($db, $sqldeleteitem);
}

$sqlgetitems = "SELECT tbproductlist.*,tbpendingborrow.* FROM tbpendingborrow LEFT JOIN tbproductlist ON tbpendingborrow.itemid = tbproductlist.id WHERE userid = '" . $_GET['userid'] . "' AND transid ='0';";
$listresult = mysqli_query($db, $sqlgetitems);

$sqlgetcu = "SELECT id,name,pincode,status,email FROM `tbup` WHERE id ='" . $_GET['userid'] . "';";
$listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

$sqlgetp = "
    SELECT id, unit, name, quantity, category 
    FROM `tbproductlist` 
    WHERE quantity > 0 
    ORDER BY name ASC;
";

$listp = mysqli_query($db, $sqlgetp);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--CSS-->
    <link rel="stylesheet" href="css/borrow-items.css">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!--JS-->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.bootstrap5.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">
    <link rel="stylesheet" href="css/borrow-items.css">
    
    <title>Auxiliary | Borrow/Request</title>
</head>
<body>

<nav class="navbar navbar-expand-md bg-dark">
      <div class="container-fluid">
        <div class="navLogo d-flex">
          <img src="images/TUP-LOGO-modified.png" alt="" style="width: 55px; height: 55px;">
          <p class="me-auto mb-2 mb-lg-0" style="color: white; padding-left: 10px; padding-right: 10px; position:relative; top:15px;">Auxiliary System</p>
        </div>
        <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <div class="navdiv d-flex justify-content-center w-100">
            <div class="dropdown mx-auto">
              <button class="btn dropdown-toggle text-white serviceDropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Services
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" value="borrowANDreturn.php">Borrow/Get Items</a></li>
                <li><a class="dropdown-item" value="minor-repair.php">Request a minor repair</a></li>
              </ul>
            </div>
            <div class="divLogin d-flex justify-content-center">
              <a href="login.php" class="loginBtn" style="text-decoration: none;">
                <button class="btn btn-outline-success" type="button" id="login">Login</button>
              </a>
            </div>
          </div>
        </div>
      </div>
</nav>

<div class="card borrow-container mx-auto mt-5 mb-5" style="width: 30rem; height: 36rem; border: 3px solid #0e0e0f; ">
    <h3 class="card-title borrow-label">REQUEST ITEMS (BORROW)</h3>
    <hr>
    <div class="card-body borrow-content" style="z-index: 1; height: 10px;">
        <div class="row">
            <div class="col-6">
                <label>Name:</label><br>
                <input type="hidden" maxlength="4" name="upid" disabled value="<?php echo $listcu['id'] ?>">
                <input type="text" maxlength="4" disabled name="upname" value="<?php echo $listcu['name'] ?>">
            </div>
        </div>
    </div>
    <hr style="position: relative; top: 60px;">
    <div class="borrow-table mx-auto mb-4">
        <table class="table" style="margin-top: 60px;">
            <thead style="text-align: center;">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Receive</th>
                <th scope="col">Unit</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($data = mysqli_fetch_assoc($listresult)) { ?>
                <tr>
                    <td><?php echo $data['name'] ?></td>
                    <td><?php echo $data['quantity'] ?><span class="text-danger"> (-<?php echo $data['borrowqty'] ?>) </span></td>
                    <td><?php echo $data['unit'] ?></td>
                    <td>
                        <form action="" method="POST">
                            <input type="hidden"
                                   class="form-control"
                                   value="<?php echo $data['id']; ?>"
                                   name="delete"
                                   required>
                            <button type="submit"
                                    class="btn btn-danger mb-1">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <img src="images/TUP-LOGO-modified.png" unselectable="on" id="bg-logo"
             style=" height: 20rem; width: 20rem; z-index:-1; opacity: 0.1;">

        <div class="row">
            <div class="col-4">
                <label>Items:</label><br>
                <div class="name-dropdown">
                    <form method="post" action="">
                        <select id="unitDropdown" name="additem" class="form-select" required>
                            <option value="">- Select -</option>
                            <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
                                <option value="<?php echo $data['id'] ?>" data-quantity="<?php echo $data['quantity'] ?>"><?php echo $data['name'] ?> |
                                    <?php echo $data['unit'] ?>: <?php echo $data['quantity'] ?></option>
                            <?php } ?>
                        </select>
                </div>
            </div>
           
            <div class="col-4">
                <label>Quantity:</label><br>
                <input type="number" style="z-index:1;" id="quantity" name="qty" placeholder="" required>
            </div>
            <div class="col-4">
                <br>
                <button type="submit" id="add" class="btn btn-danger">Add</button>
            </div>
            </form>
        </div>
    </div>
    <div class="submitBtn mx-auto mb-4">
        <form method="post" action="">
            <input type="hidden"
                   class="form-control"
                   value="<?php echo $_GET['userid']; ?>"
                   name="finalizerequest"
                   required>
            <button type="submit" class="borrow-submit">Submit</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="messageModalLabel">Error Message:</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body" id="modalMessage">
                <!-- The message will be inserted here dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="js/borrow-items.js" type="module"></script>
<script>
    $(document).ready(function() {
    $('#unitDropdown').change(function() {
        var selectedOption = $(this).find('option:selected');
        var quantity = selectedOption.data('quantity');
        $('#quantity').attr('placeholder', 'stock: ' + quantity).val(''); // Update placeholder
    });

    $('#quantity').on('input', function() {
        var placeholderText = $(this).attr('placeholder');
        var placeholderQuantity = parseInt(placeholderText.split(': ')[1], 10); // Extract quantity
        var inputVal = parseInt($(this).val(), 10); // Current input value

        if (inputVal > placeholderQuantity) {
            $(this).val(placeholderQuantity);
            // alert("You cannot enter a quantity higher than " + placeholderQuantity);
        }
    });
});

</script>

<script>
$(document).ready(function() {
    // Check if there's a message to show
    <?php if (!empty($message)) { ?>
        // Set the modal message content
        $('#modalMessage').text("<?php echo $message; ?>");
        
        // Show the modal
        $('#messageModal').modal('show');
    <?php } ?>
});
</script>

</body>
</html>
