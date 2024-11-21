<?php
session_start();
include('./includes/dbconnection.php');
include('./vendor/autoload.php');

if (isset($_POST['search'])) {
    $bookingId = $_POST['booking_id'];
    
    // Step 1: Check payment status in the payments table
    $sqlPaymentCheck = "SELECT payment_status FROM payments WHERE booking_id = :bookingId";
    $paymentCheckQuery = $dbh->prepare($sqlPaymentCheck);
    $paymentCheckQuery->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
    $paymentCheckQuery->execute();
    $paymentStatus = $paymentCheckQuery->fetchColumn();
    
    if ($paymentStatus === 'paid') {
        $paymentProcessed = true;
    } else {
        $paymentProcessed = false;
    }
    
    // Step 2: Fetch booking details from tblbooking if payment status is not "paid"
    $sqlFetch = "SELECT b.BookingID, b.Name, b.Email, b.MobileNumber, b.EventDate, b.EventType, 
                        s.ServiceName, s.ServicePrice, b.VenueAddress
                 FROM tblbooking b
                 JOIN tblservice s ON b.ServiceID = s.ID
                 WHERE b.BookingID = :bookingId";
    
    $query = $dbh->prepare($sqlFetch);
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_OBJ); // Fetch booking details
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Online DJ Management System || PAYMENTS</title>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
    <script src="js/jquery.min.js"></script>
    <style>
        .payment-details table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: center;
            background-color: #333;
            color: white;
        }
        .payment-details th, .payment-details td {
            padding: 12px;
            border: 1px solid #444;
        }
        .payment-details th {
            background-color: #222;
        }
        .payment-details td {
            background-color: #333;
        }
        .payment-details h3 {
            text-align: center;
            color: #f8f8f8;
            margin-top: 20px;
        }
        .alert {
            width: 80%;
            margin: 20px auto;
            padding: 10px;
            color: #333;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .payment-details .btn-success {
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: 0;
        }
    </style>
</head>
<body>
<?php include_once('includes/header.php');?>

<div class="payment content">
    <div class="container">  
        <ol class="breadcrumb">
            <li><a href="index.php">Home</a></li>
            <li class="active">Payments</li>      
        </ol>
        <h2>Payments</h2>

        <!-- Search Booking Form -->
        <form method="post" action="" class="payment-search-form">
            <input type="text" name="booking_id" placeholder="Enter Booking ID" required>
            <button type="submit" name="search" class="btn btn-primary">Search</button>
        </form>

        <?php if (isset($result)): ?>
            <div class="payment-details">
                <h3>Payment Invoice</h3>
                <table>
                    <tr>
                        <th>Booking ID</th>
                        <td><?php echo htmlentities($result->BookingID); ?></td>
                    </tr>
                    <tr>
                        <th>Customer Name</th>
                        <td><?php echo htmlentities($result->Name); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlentities($result->Email); ?></td>
                    </tr>
                    <tr>
                        <th>Mobile No</th>
                        <td><?php echo htmlentities($result->MobileNumber); ?></td>
                    </tr>
                    <tr>
                        <th>Event Type</th>
                        <td><?php echo htmlentities($result->EventType); ?></td>
                    </tr>
                    <tr>
                        <th>Event Date</th>
                        <td><?php echo htmlentities($result->EventDate); ?></td>
                    </tr>
                    <tr>
                        <th>Service Name</th>
                        <td><?php echo htmlentities($result->ServiceName); ?></td>
                    </tr>
                    <tr>
                        <th>Service Price</th>
                        <td><?php echo htmlentities($result->ServicePrice); ?></td>
                    </tr>
                    <tr>
                        <th>Venue Address</th>
                        <td><?php echo htmlentities($result->VenueAddress); ?></td>
                    </tr>
                </table>
                
                <!-- Payment Status Message -->
                <?php if ($paymentProcessed): ?>
                    <div class="alert alert-success">
                        <strong>Payment has been processed successfully for Booking ID <?php echo htmlentities($result->BookingID); ?>.</strong>
                    </div>
                <?php else: ?>
                    <!-- If payment is not processed, show the pay now button -->
                    <a href="payments/payment-gateway.php?bookingid=<?php echo htmlentities($result->BookingID); ?>" class="btn btn-success">Pay Now</a>
                <?php endif; ?>
            </div>
        <?php elseif (isset($_POST['search'])): ?>
            <p>No booking found with that Booking ID.</p>
        <?php endif; ?>
    </div>
</div>

<?php include_once('includes/footer.php');?>
</body>
</html>
