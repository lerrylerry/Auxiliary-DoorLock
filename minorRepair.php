<?php
require('dbcred/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Start session
session_start();


// Insert data into database if form is submitted
if (isset($_POST['name'])) {
    $sqlinsert = "INSERT INTO `tbminorrepair`(`name`, `position`, `department`, `email`, `type`, `serial`, `brandmodel`, `propertyno`, `acqdate`, `acqcost`, `scope`, `endUser`) 
    VALUES ('" . $_POST['name'] . "','" . $_POST['position'] . "','" . $_POST['department'] . "','" . $_POST['email'] . "','" . $_POST['type'] . "','" . $_POST['serial'] . "','" . $_POST['model'] . "','" . $_POST['propertyno'] . "','" . $_POST['acqusitionDate'] . "','" . $_POST['acqusitionCost'] . "','" . $_POST['message'] . "','" . $_POST['name'] . "')";

    // Execute the query and check for success
    if (mysqli_query($db, $sqlinsert)) {
        // Send email to the requester
        $requesterEmail = $_POST['email'];
        sendEmailToRequester($requesterEmail);

        // Send email to the admin (tbadmin where id = 1)
        sendEmailToAdmin();

        // Redirect to 200.php if successful
        header("Location: success.php");
        exit();
    } else {
        // Handle error (optional)
        echo "Error: " . mysqli_error($db);
    }
}

// Function to send email to the requester
function sendEmailToRequester($requesterEmail) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->SMTPDebug = false;  // Set to true for debugging purposes
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your Gmail email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS encryption
        $mail->Port = 587;  // Port for sending via Gmail SMTP

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($requesterEmail);  // Send email to the requester

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Repair Request Confirmation';
        $mail->Body    = "<p>Dear,</p>" . $_POST['name'] . "<p>Your request for a minor repair has been successfully submitted. We will process it as soon as possible.</p><p>Thank you,</p><p>Auxiliary System</p>";

        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

// Function to send email to the admin (tbadmin where id = 1)
function sendEmailToAdmin() {
    global $db;

    try {
        // Fetch the admin email from tbadmin where id = 1
        $result = mysqli_query($db, "SELECT email FROM tbadmin WHERE id = 1");
        $adminEmail = mysqli_fetch_assoc($result)['email'];

        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = false;  // Set to true for debugging purposes
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'projxacts12@gmail.com';  // Your Gmail email address
        $mail->Password = 'vdbwgupzfybcixsk';  // Your Gmail app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS encryption
        $mail->Port = 587;  // Port for sending via Gmail SMTP

        // Recipients
        $mail->setFrom('projxacts12@gmail.com', 'TUP Auxiliary System');
        $mail->addAddress($adminEmail);  // Send email to admin

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Repair Request';
        $mail->Body    = "<p>A new minor repair request has been submitted by" . $_POST['name'] . "</p><p><a href='https://tupcauxiliary.com'>Click here to view the request.</a></p>";

        $mail->send();
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/minorrepair.css">

    <title>Auxiliary | Repair</title>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-md navbar-dark">
    <div class="container-fluid">
        <div class="navLogo d-flex">
            <img src="images/TUP-LOGO-modified.png" alt="TUP Logo">
            <p class="me-auto mb-2 mb-lg-0">Auxiliary System</p>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navdiv d-flex justify-content-center w-100">
                <div class="dropdown mx-auto">
                    <button class="btn dropdown-toggle text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Services</button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="borrowANDreturn.php">Borrow/Get Items</a></li>
                        <li><a class="dropdown-item" href="minorRepair.php">Request a minor repair</a></li>
                    </ul>
                </div>
                <div class="divLogin d-flex justify-content-center">
                    <a href="login.php" class="loginBtn" style="text-decoration: none;">
                        <button class="btn btn-outline-light" type="button" id="login">Login</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Form for Requesting Minor Repair -->
<form action="" method="post">
    <div class="card container">
        <h4 class="card-title">REQUEST BY</h4>
        <hr>
        <div class="form-section">
            <label>Name:<span class="text-danger">*</span></label>
            <input type="text" id="name" name="name" placeholder="Enter name" required>
        </div>
        <div class="form-section">
            <label>Position:<span class="text-danger">*</span></label>
            <input type="text" id="position" name="position" placeholder="Enter position" required>
        </div>
        <div class="form-section">
            <label>Department:<span class="text-danger">*</span></label>
            <input type="text" id="department" name="department" placeholder="Enter department" required>
        </div>
        <div class="form-section">
            <label>Email:<span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" placeholder="Enter email" required>
        </div>

        <h4 class="card-title">DESCRIPTION OF PROPERTY</h4>
        <hr>
        <div class="form-section">
            <label>Type:<span class="text-danger">*</span></label>
            <input type="text" id="type" name="type" placeholder="Enter type" required>
        </div>
        <div class="form-section">
            <label>Brand Model:<span class="text-danger">*</span></label>
            <input type="text" id="model" name="model" placeholder="Enter brand model" required>
        </div>
        <div class="form-section">
            <label>Serial No. (Optional).:</label>
            <input type="text" id="serial" name="serial" placeholder="Enter serial">
        </div>
        <div class="form-section">
            <label>Property No. (Optional):</label>
            <input type="text" id="property" name="propertyno" placeholder="Enter property number">
        </div>
        <div class="form-section">
            <label>Acquisition Date (Optional):</label>
            <input type="date" id="acqusitionDate" name="acqusitionDate">
        </div>
        <div class="form-section">
            <label>Acquisition Cost (Optional):</label>
            <input type="text" id="acqusitionCost" name="acqusitionCost" placeholder="Enter acquisition cost">
        </div>

        <h4 class="card-title">DEFECTS AND COMPLAINTS</h4>
        <hr>
        <div class="form-section">
            <label>Nature and Scope of Works to be done:<span class="text-danger">*</span></label>
            <textarea id="message" name="message" placeholder="Explanation message" rows="" required></textarea>
        </div>

        <div class="submitBtn">
            <button type="submit">Submit</button>
        </div>
    </div>
</form>

<!-- Background Logo -->
<!-- <img src="images/TUP-LOGO-modified.png" id="bg-logo"> -->
 <!-- Footer -->
<footer class="footer">
    <div class="footer-bottom text-center">
        <p>&copy; 2024 Auxiliary System | All Rights Reserved</p>
    </div>
</footer>

<!-- Additional Styles for the Footer -->
<style>
    .footer {
        background-color: #2d2d2d;
        color: white;
    }

    .footer h5 {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        color: #ed8383;
    }

    .footer ul {
        list-style-type: none;
        padding: 0;
    }

    .footer ul li {
        margin-bottom: 10px;
    }

    .footer ul li a {
        color: white;
        text-decoration: none;
    }

    .footer ul li a:hover {
        color: #ed8383;
    }

    .social-links a {
        color: white;
        font-size: 1.5rem;
        margin: 0 10px;
        text-decoration: none;
    }

    .social-links a:hover {
        color: #ed8383;
    }

    .footer-bottom {
        background-color: #222;
        padding: 10px 0;
        margin-top: 20px;
    }

    .footer-bottom p {
        margin: 0;
        font-size: 1rem;
    }

    /* Mobile Adjustments */
    @media (max-width: 767px) {
        .footer .container {
            text-align: center;
        }

        .footer .social-links {
            margin-top: 20px;
        }
    }
</style>


<!-- JavaScript Files -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>