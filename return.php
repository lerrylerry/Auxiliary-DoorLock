<?php
require('dbcred/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (isset($_SESSION['loginid'])) {
    header("location: admin/homepage.php");
}

// if (isset($_POST['additem'])) {
//     $sqlinsertp = "INSERT INTO `tbpendingreturn`(`itemid`, `userid`,`borrowqty`) VALUES ('" . $_POST['additem'] . "','" . $_GET['userid'] . "','" . $_POST['qty'] . "')";
//     mysqli_query($db, $sqlinsertp);
// }

if (isset($_POST['additem'])) {
    // Assuming 'returningqty' is the column to update
    $itemid = $_POST['additem'];
    $userid = $_GET['userid'];
    $qty = $_POST['qty'];

    // Update the returningqty field based on the itemid and userid
    $sqlupdatep = "UPDATE tbpendingreturn SET returningqty = returningqty + $qty WHERE itemid = '$itemid' AND userid = '$userid'";

    if (mysqli_query($db, $sqlupdatep)) {
        // Update successful, you can add a success message here
    } else {
        // Handle any errors, such as if the query failed
        echo "Error: " . mysqli_error($db);
    }
}

if (isset($_POST['finalizerequest'])) {
    // Insert a new request into tbreturn with status 'Pending'
    $sqlsubmitrequest = "INSERT INTO `tbreturn`(`userid`, `status`) VALUES ('" . $_POST['finalizerequest'] . "','Pending')";
    mysqli_query($db, $sqlsubmitrequest);

    // Get the transaction ID from the previous insert (this is used to link with tbpendingreturn)
    $transaction_id = mysqli_insert_id($db);

    // Update tbpendingreturn to link the transaction ID for items where returningqty != 0
    // Instead of setting transid = 0, we set transid to the new transaction ID
    $sqlupdatetransid = "UPDATE `tbpendingreturn` 
                         SET `transid` = '$transaction_id', `returningqty` = 0 
                         WHERE `userid` = '" . $_POST['finalizerequest'] . "' 
                         AND `returningqty` != 0";
    mysqli_query($db, $sqlupdatetransid);

    // Reset the door access to 'Rejected' for the user in tbup
    $resetaccess = "UPDATE `tbup` SET `dooraccess` = 'Rejected' WHERE id = '" . $_POST['finalizerequest'] . "'";
    mysqli_query($db, $resetaccess);

    // Step 1: Get the user details (name, email) for sending the email
    $sqlgetuser = "SELECT id, name, email FROM `tbup` WHERE id = '" . $_POST['finalizerequest'] . "';";
    $user = mysqli_fetch_assoc(mysqli_query($db, $sqlgetuser));

    // Step 2: Get the list of items the user is returning with their borrow quantity
    $sqlgetitems = "SELECT tbproductlist.name, tbpendingreturn.borrowqty 
                    FROM tbpendingreturn 
                    LEFT JOIN tbproductlist ON tbpendingreturn.itemid = tbproductlist.id 
                    WHERE tbpendingreturn.userid = '" . $_POST['finalizerequest'] . "' 
                    AND tbpendingreturn.transid = '$transaction_id';";
    $itemsresult = mysqli_query($db, $sqlgetitems);

    // Step 3: Prepare the email content
    $emailContent = "Dear " . $user['name'] . ",\n\n";
    $emailContent .= "Your return request has been successfully processed. Below are the details of the items you have returned:\n\n";

    // Debugging output to check if the items are being retrieved correctly
    if (mysqli_num_rows($itemsresult) > 0) {
    while ($item = mysqli_fetch_assoc($itemsresult)) {
    // Append item name and quantity to the email content
    $emailContent .= "Item: " . $item['name'] . " | Quantity: " . $item['borrowqty'] . "\n";
    }
    } else {
    $emailContent .= "No items were returned or there was an issue fetching the items.\n";
    }

    $emailContent .= "\nThank you for using our service.\n\nBest regards,\nTUP Auxillary System";

    // Debugging: Output the email content to ensure it's correctly populated
    echo "<pre>" . htmlspecialchars($emailContent) . "</pre>";

    // Step 4: Send email notification using PHPMailer
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
        $mail->addAddress($user['email'], $user['name']);  // Add recipient email dynamically

        // Email content
        $mail->isHTML(false);  // Plain text email
        $mail->Subject = 'Return Request Confirmation';
        $mail->Body    = $emailContent;

        // Send email
        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header("location: borrowANDreturn.php");
}

if (isset($_POST['delete'])) {  
    $sqlupdateqty = "UPDATE `tbpendingreturn` SET returningqty = 0 WHERE id = '" . $_POST['delete'] . "'";
    mysqli_query($db, $sqlupdateqty);
}


$sqlgetitems = "SELECT tbproductlist.*,tbpendingreturn.* FROM tbpendingreturn LEFT JOIN tbproductlist ON tbpendingreturn.itemid = tbproductlist.id WHERE userid = '" . $_GET['userid'] . "' AND returningqty != 0;";
$listresult = mysqli_query($db, $sqlgetitems);

$sqlgetcu = "SELECT id,name,pincode,status FROM `tbup` WHERE id ='" . $_GET['userid'] . "';";
$listcu = mysqli_fetch_assoc(mysqli_query($db, $sqlgetcu));

// return------------------------------------------------------------------------------------------------------
$userId = filter_input(INPUT_GET, 'userid', FILTER_SANITIZE_NUMBER_INT);

$sqlgetp = "
    SELECT p.*, pl.unit, pl.*  -- Include pl.unit explicitly
    FROM tbpendingreturn p
    JOIN tbproductlist pl ON p.itemid = pl.id
    WHERE p.userid = '$userId' AND p.transid != 0
    ORDER BY pl.name ASC
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



    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anta&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap"
          rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
            integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/borrow-items.css"> -->
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
        <h3 class="card-title borrow-label">REQUEST ITEMS (RETURN)</h3>
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
        <div class="borrow-table mx-auto">
            <table class="table" style="margin-top: 60px;">
                <thead style="text-align: center;">
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Return</th>
                    <th scope="col">Unit</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($data = mysqli_fetch_assoc($listresult)) { ?>
                    <tr>
                    <td><?php echo $data['name'] ?></td>
                    <td><span class="text-success"><?php echo $data['borrowqty'] ?></span></td>
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
                        <select id="unitDropdown" name="additem" class="form-select">
                            <option value="">- Select -</option>
                            <?php while ($data = mysqli_fetch_assoc($listp)) { ?>
                                echo "ID: " . $row["id"]. " - Name: " . $row["name"]. " - Returnable: " . $row["borrowqty"]. "<br>";
                                <option value="<?php echo $data["id"]; ?>" data-quantity="<?php echo $data['borrowqty'] ?>"><?php echo $data["name"]; ?> | Qty: <?php echo $data["borrowqty"]; ?></option>
                            <?php } ?>
                        </select>

                    </div>
                </div>
                <div class="col-4">
                    <label>Quantity:</label><br>
                    <input type="number" style="z-index:1;" required id="quantity" name="qty" placeholder="">
                </div>
                <div class="col-4">
                    <br>
                    <button type="submit" id="add" class="btn btn-danger">Add</button>
                </div>
                </form>
            </div>
        </div>
        <div class="submitBtn mx-auto mt-5">
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

<script src="js/borrow-items.js" type="module"></script>
<script>
    $(document).ready(function() {
    $('#unitDropdown').change(function() {
        var selectedOption = $(this).find('option:selected');
        var quantity = selectedOption.data('quantity');
        $('#quantity').attr('placeholder', 'borrowed: ' + quantity).val(''); // Update placeholder
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
</body>
</html>
