<?php
session_start();
error_reporting(0);
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('includes/dbconnection.php');
include('../vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure that the session is active
if (strlen($_SESSION['odmsaid'] == 0)) {
    header('location:logout.php');
} else {
    // Mail sending logic
    if (isset($_POST['sendInvoice'])) {
        $invid = $_POST['invid'];

        // Query to get booking, service, and payment details
        $sql = "SELECT tblbooking.BookingID, tblbooking.Name, tblbooking.MobileNumber, tblbooking.Email, 
                    tblbooking.EventDate, tblbooking.EventStartingtime, tblbooking.EventEndingtime, 
                    tblbooking.VenueAddress, tblbooking.EventType, tblbooking.AdditionalInformation, 
                    tblbooking.BookingDate, tblbooking.Remark, tblbooking.Status, tblbooking.UpdationDate,
                    tblservice.ServiceName, tblservice.SerDes, tblservice.ServicePrice,
                    payments.payment_status, payments.payment_id, payments.service_name, payments.service_price, 
                    payments.venue_address, payments.event_date, payments.created_at 
                FROM tblbooking 
                JOIN tblservice ON tblbooking.ServiceID=tblservice.ID 
                JOIN payments ON tblbooking.BookingID=payments.booking_id 
                WHERE tblbooking.ID=:invid";

        $query = $dbh->prepare($sql);
        $query->bindParam(':invid', $invid, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            $row = $results[0]; // Fetch the first result (since BookingID is unique)

            // PHPMailer Configuration
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'sunil.merndeveloper@gmail.com'; // Replace with your email
                $mail->Password = 'kcnuoaxchcbqomxa'; // Replace with your email password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('admin.ODJMS@gmail.com', 'DJ Management');
                $mail->addAddress($row->Email, $row->Name);

                $mail->isHTML(true);
                $mail->Subject = 'Your Invoice for Booking #' . $row->BookingID;
                
                // Email body content
                $mail->Body = "
                    <h2>Invoice for Booking #" . $row->BookingID . "</h2>
                    <p>Dear " . $row->Name . ",</p>
                    <p>Your payment has been received. Below are the details of your booking:</p>
                    <table>
                        <tr><th>Name of Client</th><td>" . $row->Name . "</td></tr>
                        <tr><th>Mobile Number</th><td>" . $row->MobileNumber . "</td></tr>
                        <tr><th>Email</th><td>" . $row->Email . "</td></tr>
                        <tr><th>Event Date</th><td>" . $row->EventDate . "</td></tr>
                        <tr><th>Service Name</th><td>" . $row->ServiceName . "</td></tr>
                        <tr><th>Service Price</th><td>" . $row->ServicePrice . "</td></tr>
                        <tr><th>Payment Status</th><td>" . $row->payment_status . "</td></tr>
                        <tr><th>Venue Address</th><td>" . $row->venue_address . "</td></tr>
                        <tr><th>Payment Date</th><td>" . ($row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : 'N/A') . "</td></tr>
                    </table>
                    <p>Thank you for choosing our service.</p>
                    <p>Best Regards,<br>Admin ODJMS</p>
                ";

                // Send email
                $mail->send();
                echo "Invoice has been sent to " . $row->Email;
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }

    ?>
    <!doctype html>
    <html lang="en" class="no-focus">
        <head>
            <title>Online DJ Management System - View Invoice</title>
            <link rel="stylesheet" id="css-main" href="assets/css/codebase.min.css">
            <script language="javascript" type="text/javascript">
                function f2() {
                    window.close();
                }

                function f3() {
                    window.print();
                }
            </script>
        </head>
        <body>
            <div id="page-container" class="sidebar-o sidebar-inverse side-scroll page-header-fixed main-content-narrow">

                <?php include_once('includes/sidebar.php'); ?>
                <?php include_once('includes/header.php'); ?>

                <!-- Main Container -->
                <main id="main-container">
                    <!-- Page Content -->
                    <div class="content">
                        <!-- Register Forms -->
                        <h2 class="content-heading">View Invoice</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Bootstrap Register -->
                                <div class="block block-themed">
                                    <div class="block-header bg-gd-emerald">
                                        <h3 class="block-title">View Invoice</h3>
                                    </div>
                                    <div class="block-content">
                                        <?php
                                        // Fetch booking ID from URL
                                        $invid = $_GET['invid'];

                                        // Query to get booking, service, and payment details
                                        $sql = "SELECT tblbooking.BookingID, tblbooking.Name, tblbooking.MobileNumber, tblbooking.Email, 
                                                        tblbooking.EventDate, tblbooking.EventStartingtime, tblbooking.EventEndingtime, 
                                                        tblbooking.VenueAddress, tblbooking.EventType, tblbooking.AdditionalInformation, 
                                                        tblbooking.BookingDate, tblbooking.Remark, tblbooking.Status, tblbooking.UpdationDate,
                                                        tblservice.ServiceName, tblservice.SerDes, tblservice.ServicePrice,
                                                        payments.payment_status, payments.payment_id, payments.service_name, payments.service_price, 
                                                        payments.venue_address, payments.event_date, payments.created_at 
                                                FROM tblbooking 
                                                JOIN tblservice ON tblbooking.ServiceID=tblservice.ID 
                                                JOIN payments ON tblbooking.BookingID=payments.booking_id 
                                                WHERE tblbooking.ID=:invid";

                                        // Prepare and execute the query
                                        $query = $dbh->prepare($sql);
                                        $query->bindParam(':invid', $invid, PDO::PARAM_STR);
                                        $query->execute();
                                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                                        // Check if the query returned results
                                        if ($query->rowCount() > 0) {
                                            $grandtotal = 0; // Initialize grand total

                                            foreach ($results as $row) {
                                                ?>
                                                <table border="1" class="table table-bordered table-striped table-vcenter js-dataTable-full-pagination">
                                                    <tr>
                                                        <th colspan="5" style="text-align: center;color: red;font-size: 20px">Booking Number: <?php echo $row->BookingID; ?></th>
                                                    </tr>

                                                    <tr>
                                                        <th>Name of Client</th>
                                                        <td><?php echo $row->Name; ?></td>
                                                        <th>Mobile Number</th>
                                                        <td><?php echo $row->MobileNumber; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Email</th>
                                                        <td><?php echo $row->Email; ?></td>
                                                        <th>Event Date</th>
                                                        <td><?php echo $row->EventDate; ?></td>
                                                    </tr>

                                                    <tr>
                                                        <th style="text-align: center;" colspan="2">Service Name</th>
                                                        <th style="text-align: center;" colspan="2">Service Price</th>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: center;" colspan="2"><?php echo $row->ServiceName; ?></td>
                                                        <td style="text-align: center;" colspan="2"><?php echo $total = $row->ServicePrice; ?></td>
                                                    </tr>

                                                    <!-- Payment Details -->
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Payment Status</th>
                                                        <td colspan="2"><?php echo $row->payment_status; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Transaction ID</th>
                                                        <td colspan="2"><?php echo $row->payment_id; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Service Name </th>
                                                        <td colspan="2"><?php echo $row->service_name; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Service Price </th>
                                                        <td colspan="2"><?php echo $row->service_price; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Venue Address</th>
                                                        <td colspan="2"><?php echo $row->venue_address; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th colspan="2" style="text-align: center;">Payment Date</th>
                                                        <td colspan="2"><?php echo $row->created_at ? date('Y-m-d H:i:s', strtotime($row->created_at)) : 'N/A'; ?></td>
                                                    </tr>

                                                    <?php 
                                                        $grandtotal += $total; // Add service price to grand total
                                            }
                                        ?>
                                        <tr>
                                            <th colspan="2" style="text-align:center;color: blue">Grand Total</th>
                                            <td colspan="2" style="text-align: center;"><?php echo $grandtotal; ?></td>
                                        </tr>
                                        </table> 
                                        <?php
                                        } else {
                                            echo "<p>No details found for this booking.</p>";
                                        }
                                        ?>

                                        <!-- Invoice actions -->
                                        <p>
                                            <form method="POST">
                                                <input type="hidden" name="invid" value="<?php echo $invid; ?>" />
                                                <input name="sendInvoice" type="submit" class="txtbox4" value="Send Invoice" style="cursor: pointer; background-color: #007bff; color: white; border: none; padding: 10px 20px; font-size: 16px; position: absolute; right: 30px; bottom: 30px;" />

                                            </form>
                                            <input name="Submit2" type="submit" class="txtbox4" value="Close" onClick="return f2();" style="cursor: pointer;" />
                                            <input name="Submit2" type="submit" class="txtbox4" value="Print" onClick="return f3();" style="cursor: pointer;" />
                                        </p>
                                    </div>
                                </div>
                                <!-- END Bootstrap Register -->
                            </div>
                        </div>
                    </div>
                    <!-- END Page Content -->
                </main>
                <!-- END Main Container -->

                <?php include_once('includes/footer.php'); ?>
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
<?php } ?>
