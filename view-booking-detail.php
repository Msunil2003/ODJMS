<?php
// Prepare email configuration with PHPMailer
        // Email Configuration
        require '../vendor/autoload.php';
        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\Exception;
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (strlen($_SESSION['odmsaid'] == 0)) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit'])) {

        $eid = $_GET['editid'];
        $bookingid = $_GET['bookingid'];
        $status = $_POST['status'];
        $remark = $_POST['remark'];

        // Update booking status in the database
        $sql = "UPDATE tblbooking SET Status = :status, Remark = :remark WHERE ID = :eid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':remark', $remark, PDO::PARAM_STR);
        $query->bindParam(':eid', $eid, PDO::PARAM_STR);
        $query->execute();

        // Fetch booking details for email
        $sqlFetch = "SELECT BookingID, Name, Email, MobileNumber, EventDate, EventStartingtime, EventEndingtime, 
                     VenueAddress, EventType, AdditionalInformation, ServiceName, ServicePrice 
                     FROM tblbooking 
                     JOIN tblservice ON tblbooking.ServiceID = tblservice.ID 
                     WHERE tblbooking.ID = :eid";
        $queryFetch = $dbh->prepare($sqlFetch);
        $queryFetch->bindParam(':eid', $eid, PDO::PARAM_STR);
        $queryFetch->execute();
        $result = $queryFetch->fetch(PDO::FETCH_OBJ);

        

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'sunil.merndeveloper@gmail.com'; // Your email
            $mail->Password = 'kcnuoaxchcbqomxa'; // Email password (use app password if using Gmail)
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Email content and recipients
            $mail->setFrom('admin.ODJMS@gmail.com', 'DJ Management');
            $mail->addAddress($result->Email, $result->Name);

            $mail->isHTML(true);
            $mail->Subject = 'Booking Status Update - Booking ID: ' . $result->BookingID;
            $mail->Body    = "
                <h2>Booking Update</h2>
                <p>Booking ID: {$result->BookingID}</p>
                <p>Name: {$result->Name}</p>
                <p>Email: {$result->Email}</p>
                <p>Mobile Number: {$result->MobileNumber}</p>
                <p>Event Date: {$result->EventDate}</p>
                <p>Event Time: {$result->EventStartingtime} to {$result->EventEndingtime}</p>
                <p>Venue: {$result->VenueAddress}</p>
                <p>Event Type: {$result->EventType}</p>
                <p>Service: {$result->ServiceName}</p>
                <p>Service Price: \${$result->ServicePrice}</p>
                <p>Status: {$status}</p>
                <p>Remark: {$remark}</p>
                <p>Thank you for choosing our services!</p><br><br><br>
                <p>Thanks & Regards</p>
                <p>Admin ODJMS </p>";

            // Send email
            $mail->send();
            echo '<script>alert("Remark updated and email sent successfully.")</script>';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        echo "<script>window.location.href ='new-booking.php'</script>";
    }
}
?>

<!doctype html>
<html lang="en" class="no-focus"> <!--<![endif]-->
<head>
    <title>Online DJ Management System - View Booking</title>
    <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
</head>
<body>
    <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">
        <?php include_once('includes/sidebar.php'); ?>
        <?php include_once('includes/header.php'); ?>

        <!-- Main Container -->
        <main id="main-container">
            <!-- Page Content -->
            <div class="content">
                <h2 class="content-heading">View Booking</h2>
                <div class="row">
                    <div class="col-md-12">
                        <div class="block block-themed">
                            <div class="block-header bg-gd-emerald">
                                <h3 class="block-title">View Booking</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="state_toggle" data-action-mode="demo">
                                        <i class="si si-refresh"></i>
                                    </button>
                                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                                </div>
                            </div>
                            <div class="block-content">
                                <?php
                                $eid = $_GET['editid'];

                                $sql = "SELECT tblbooking.BookingID, tblbooking.Name, tblbooking.MobileNumber, tblbooking.Email, tblbooking.EventDate, tblbooking.EventStartingtime, tblbooking.EventEndingtime, tblbooking.VenueAddress, tblbooking.EventType, tblbooking.AdditionalInformation, tblbooking.BookingDate, tblbooking.Remark, tblbooking.Status, tblbooking.UpdationDate, tblservice.ServiceName, tblservice.SerDes, tblservice.ServicePrice 
                                        FROM tblbooking 
                                        JOIN tblservice ON tblbooking.ServiceID = tblservice.ID  
                                        WHERE tblbooking.ID = :eid";
                                $query = $dbh->prepare($sql);
                                $query->bindParam(':eid', $eid, PDO::PARAM_STR);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) {
                                ?>
                                <table border="1" class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                    <tr>
                                        <th>Booking Number</th>
                                        <td><?php echo $row->BookingID; ?></td>
                                        <th>Client Name</th>
                                        <td><?php echo $row->Name; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Mobile Number</th>
                                        <td><?php echo $row->MobileNumber; ?></td>
                                        <th>Email</th>
                                        <td><?php echo $row->Email; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Event Date</th>
                                        <td><?php echo $row->EventDate; ?></td>
                                        <th>Event Starting Time</th>
                                        <td><?php echo $row->EventStartingtime; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Event Ending Time</th>
                                        <td><?php echo $row->EventEndingtime; ?></td>
                                        <th>Venue Address</th>
                                        <td><?php echo $row->VenueAddress; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Event Type</th>
                                        <td><?php echo $row->EventType; ?></td>
                                        <th>Additional Information</th>
                                        <td><?php echo $row->AdditionalInformation; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Service Name</th>
                                        <td><?php echo $row->ServiceName; ?></td>
                                        <th>Service Description</th>
                                        <td><?php echo $row->SerDes; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Service Price</th>
                                        <td>$<?php echo $row->ServicePrice; ?></td>
                                        <th>Apply Date</th>
                                        <td><?php echo $row->BookingDate; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Order Final Status</th>
                                        <td><?php echo $row->Status ? $row->Status : "Not Updated Yet"; ?></td>
                                        <th>Admin Remark</th>
                                        <td><?php echo $row->Status ? htmlentities($row->Status) : "Not Updated Yet"; ?></td>
                                    </tr>
                                </table>
                                <?php } } ?>

                                <?php if ($status == "") { ?>
                                    <p align="center" style="padding-top: 20px">
                                        <button class="btn btn-primary waves-effect waves-light w-lg" data-toggle="modal" data-target="#myModal">Take Action</button>
                                    </p>
                                <?php } ?>

                                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Take Action</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post" name="submit">
                                                    <div class="form-group">
                                                        <label for="status">Status</label>
                                                        <select class="form-control" name="status" id="status" required>
                                                            <option value="">Select</option>
                                                            <option value="Approved">Approved</option>
                                                            <option value="Rejected">Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="remark">Admin Remark</label>
                                                        <textarea class="form-control" id="remark" name="remark" rows="4" placeholder="Enter Remark"></textarea>
                                                    </div>
                                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/core/jquery.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>


          <?php include_once('includes/footer.php');?>
        </div>
        <!-- END Page Container -->

        <!-- Codebase Core JS -->
        <script src="assets/js/core/jquery.min.js"></script>
        <script src="assets/js/core/popper.min.js"></script>
        <script src="assets/js/core/bootstrap.min.js"></script>
        <script src="assets/js/core/jquery.slimscroll.min.js"></script>
        <script src="assets/js/core/jquery.scrollLock.min.js"></script>
        <script src="assets/js/core/jquery.appear.min.js"></script>
        <script src="assets/js/core/jquery.countTo.min.js"></script>
        <script src="assets/js/core/js.cookie.min.js"></script>
        <script src="assets/js/codebase.js"></script>
    </body>
</html>
<?php 