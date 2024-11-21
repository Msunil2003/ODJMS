<?php
if (isset($_GET['bookingid'])) {
    $bookingId = $_GET['bookingid'];
    echo "<h2>Payment Cancelled</h2>";
    echo "<p>Your booking ID is: $bookingId</p>";
    // Optionally, you can log the cancellation or take any other action
} else {
    echo "Booking ID not provided.";
}
?>
