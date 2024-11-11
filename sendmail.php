<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('dbcred/db.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

$sqlgetmr = "SELECT * FROM `tbminorrepair` WHERE id ='2'";
$resmail= mysqli_fetch_assoc(mysqli_query($db, $sqlgetmr));
try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host	 = 'smtp.gmail.com;';
    $mail->SMTPAuth = true;
    $mail->Username = 'projxacts12@gmail.com';
    $mail->Password = 'vdbwgupzfybcixsk';
    $mail->SMTPSecure = 'tls';
    $mail->Port	 = 587;

    $mail->setFrom('projxacts12@gmail.com', 'TUP Auxillary System');
    $mail->addAddress($resmail['email'], $resmail['name']);
    $mail->isHTML(true);
    $mail->Subject = 'Minor Repair Request Approved';
    $mail->Body = 'Your Minor Repair Request has been approved <br> '.$_SERVER['SERVER_ADDR'].'/repair%20-%20userReview.php?repairid='.$_POST['approverepair'];
    $mail->send();
    echo "Mail has been sent successfully!";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>
