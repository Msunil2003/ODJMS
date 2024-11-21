<!-- EMAIL SENDER CONFIGURATION FILE -->
<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';                // Set the SMTP server to send through
    $mail->SMTPAuth = true;
    $mail->Username = 'sunil.merndeveloper@gmail.com';      // Your SMTP username
    $mail->Password = 'fgscxsltzgrkoqev';       // Your SMTP password or app-specific password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('djmanagement@example.com', 'DJ Management');
    $mail->addAddress($email, $name);              // Add a recipient

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation - Online DJ Management System';
    $mail->Body    = "
        <p>Dear $name,</p>
        <p>Thank you for booking with us! Here are your booking details:</p>
        <ul>
            <li><strong>Booking ID:</strong> $bookingid</li>
            <li><strong>Name:</strong> $name</li>
            <li><strong>Email:</strong> $email</li>
            <li><strong>Mobile No:</strong> $mobnum</li>
            <li><strong>Event Date:</strong> $edate</li>
            <li><strong>Event Time:</strong> $est - $eetime</li>
            <li><strong>Venue Address:</strong> $vaddress</li>
            <li><strong>Event Type:</strong> $eventtype</li>
        </ul>
        <p>We will reach out to confirm your booking and provide any additional details.</p>
        <p>Regards,<br>Online DJ Management Team</p>
    ";

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>