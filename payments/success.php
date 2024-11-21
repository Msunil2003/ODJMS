<?php
session_start();

// Error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
include('../includes/dbconnection.php');  // Adjust path if necessary
include('../vendor/autoload.php');        // Adjust path if necessary

// Include Stripe PHP library (ensure it's installed)
require_once('../vendor/autoload.php');  // Adjust path if necessary

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51QJYcOSIr5Ob5LdJWIKbcjirv7R4bM37BrjzECsXnK7dH190zLZJpXKQ6Z3XNjwKXzkNgnxzO577ja02o5dQNb3Q00SioHeYUg'); // Replace with your actual secret key

// Check if session_id is provided
if (isset($_GET['session_id']) && !empty($_GET['session_id'])) {
    $sessionId = $_GET['session_id'];

    try {
        // Retrieve the Stripe Checkout session details
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        // Get payment details from the session
        $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

        // Fetch other URL parameters
        $bookingId = isset($_GET['bookingid']) ? $_GET['bookingid'] : 'Not Provided';
        $name = isset($_GET['name']) ? $_GET['name'] : 'Not Provided';
        $email = isset($_GET['email']) ? $_GET['email'] : 'Not Provided';
        $serviceName = isset($_GET['service_name']) ? $_GET['service_name'] : 'Not Provided';
        $servicePrice = isset($_GET['service_price']) ? $_GET['service_price'] : 'Not Provided';
        $venueAddress = isset($_GET['venue_address']) ? $_GET['venue_address'] : 'Not Provided';
        $eventDate = isset($_GET['event_date']) ? $_GET['event_date'] : 'Not Provided';
        $eventType = isset($_GET['event_type']) ? $_GET['event_type'] : 'Not Provided';

        // Insert payment data into the database
        $sqlInsert = "INSERT INTO payments (booking_id, payment_id, payment_status, name, email, service_name, service_price, venue_address, event_date, event_type) 
                      VALUES (:bookingId, :paymentId, :paymentStatus, :name, :email, :serviceName, :servicePrice, :venueAddress, :eventDate, :eventType)";

        $stmt = $dbh->prepare($sqlInsert);
        $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
        $stmt->bindParam(':paymentId', $paymentIntent->id, PDO::PARAM_STR);
        $stmt->bindParam(':paymentStatus', $paymentIntent->status, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':serviceName', $serviceName, PDO::PARAM_STR);
        $stmt->bindParam(':servicePrice', $servicePrice, PDO::PARAM_STR);
        $stmt->bindParam(':venueAddress', $venueAddress, PDO::PARAM_STR);
        $stmt->bindParam(':eventDate', $eventDate, PDO::PARAM_STR);
        $stmt->bindParam(':eventType', $eventType, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Prepare the email content
        $subject = "Payment Successful - ODJMS";
        $message = "
        <html>
        <head>
            <title>Payment Confirmation</title>
        </head>
        <body>
            <p>Dear $name,</p>
            <p>Your payment has been successfully processed through the ODJMS payment gateway.</p>
            <h3>Payment Details:</h3>
            <ul>
                <li><strong>Booking ID:</strong> $bookingId</li>
                <li><strong>Name:</strong> $name</li>
                <li><strong>Email:</strong> $email</li>
                <li><strong>Service Name:</strong> $serviceName</li>
                <li><strong>Service Price:</strong> ₹$servicePrice</li>
                <li><strong>Payment ID:</strong> " . $paymentIntent->id . "</li>
                <li><strong>Payment Status:</strong> " . $paymentIntent->status . "</li>
            </ul>
            <p>To confirm your payment, we will verify the transaction on our side and send the payment invoice to your registered email shortly.</p>
            <p>Thank you for choosing our services!</p>
            <p>Thanks and Regards,<br>Admin ODJMS</p>
        </body>
        </html>
        ";

        // Email Configuration using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Use your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sunil.merndeveloper@gmail.com';  // Replace with your email
            $mail->Password = 'kcnuoaxchcbqomxa';  // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Sender and recipient settings
            $mail->setFrom('admin.ODJMS@gmail.com', 'DJ Management');
            $mail->addAddress($email, $name);

            // Email format
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            // Send the email
            $mail->send();

            echo "<h2>Payment Successful</h2>";
            echo "<p>Thank you for your payment! A confirmation email has been sent to your registered email address.</p>";

            // Redirect after 5 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'http://localhost/odjms';
                }, 5000); // 5000 milliseconds = 5 seconds
            </script>";

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo 'Error retrieving session: ' . $e->getMessage();
    }
} else {
    echo "Session ID not provided.";
}
?>
