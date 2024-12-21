<?php
require('../dbcred/db.php'); // Include your database connection file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

// Function to send email notification
function sendEmailNotification($email, $name, $status, $itemDetails, $optionalMessage = '') {
    $subject = "Your Borrow Request Status";
    $body = "Dear $name,\n\nYour borrow request has been $status.!";

    if (!empty($optionalMessage)) {
        $body .= "Message from Admin: $optionalMessage\n\n"; // Add the optional message if present
    }

    $body .= "Best regards,\nTUP Auxiliary System";

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


// Close the database connection
mysqli_close($db);
?>
