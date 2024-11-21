<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if (isset($_POST['submit'])) {
    $bid = $_GET['bookid'];
    $name = $_POST['name'];
    $mobnum = $_POST['mobnum'];
    $email = $_POST['email'];
    $edate = $_POST['edate'];
    $est = $_POST['est'];
    $eetime = $_POST['eetime'];
    $vaddress = $_POST['vaddress'];
    $eventtype = $_POST['eventtype'];
    $addinfo = $_POST['addinfo'];
    $bookingid = mt_rand(100000000, 999999999);

    $sql = "INSERT INTO tblbooking(BookingID, ServiceID, Name, MobileNumber, Email, EventDate, EventStartingtime, EventEndingtime, VenueAddress, EventType, AdditionalInformation)
            VALUES (:bookingid, :bid, :name, :mobnum, :email, :edate, :est, :eetime, :vaddress, :eventtype, :addinfo)";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bookingid', $bookingid, PDO::PARAM_STR);
    $query->bindParam(':bid', $bid, PDO::PARAM_STR);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':mobnum', $mobnum, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':edate', $edate, PDO::PARAM_STR);
    $query->bindParam(':est', $est, PDO::PARAM_STR);
    $query->bindParam(':eetime', $eetime, PDO::PARAM_STR);
    $query->bindParam(':vaddress', $vaddress, PDO::PARAM_STR);
    $query->bindParam(':eventtype', $eventtype, PDO::PARAM_STR);
    $query->bindParam(':addinfo', $addinfo, PDO::PARAM_STR);

    $query->execute();
    $LastInsertId = $dbh->lastInsertId();
    
    if ($LastInsertId > 0) {
        echo '<script>
                Swal.fire({
                    title: "Booking Request Sent!",
                    text: "We will contact you soon.",
                    icon: "success",
                    confirmButtonText: "OK"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear form fields
                        document.querySelector("form").reset();
                        // Redirect to services page
                        window.location.href = "services.php";
                    }
                });
              </script>';

        // Email Configuration
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
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'Booking Confirmation';
            $mail->Body    = "<h2>Booking Confirmation</h2>
                              <p>Name: {$name}</p>
                              <p>Email: {$email}</p>
                              <p>Mobile Number: {$mobnum}</p>
                              <p>Event Date: {$edate}</p>
                              <p>Event Time: {$est} to {$eetime}</p>
                              <p>Venue: {$vaddress}</p>
                              <p>Event Type: {$eventtype}</p>
                              <p>Additional Info: {$addinfo}</p>
                              <p>We will send you the BOOKINGID through your
                              registed mail once the admin apporves your booking</p>
                              <p>Thanks & Regards</p>
                                 <p>Admin ODJMS</p>"
                              ;
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo '<script>
                Swal.fire({
                    title: "Error",
                    text: "Something went wrong. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
              </script>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Online DJ Management System || Contact Us</title>
<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
<!-- SWEET ALERTS -->
<!-- SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom Theme files -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="css/touchTouch.css" type="text/css" media="all" />
<!-- Custom Theme files -->
<script src="js/jquery.min.js"></script>

<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<!--webfont-->
<link href='http://fonts.googleapis.com/css?family=Monoton' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
<!---//End-css-style-switecher----->

</head>
<body>
<?php include_once('includes/header.php');?>
<div class="contact content">
    <div class="container">         
        <ol class="breadcrumb">
            <li><a href="index.php">Home</a></li>
            <li class="active">Book Services</li>     
        </ol>
        
        <div class="contact-grids">
            <div class="col-md-6 contact-left">
                <p>Book Your Events now. </p>
                <form method="post">
                    <ul>
                        <li class="text-info">Name: </li>
                        <li><input type="text" class="form-control" name="name" required="true"></li>
                    </ul>                     
                    <ul>
                        <li class="text-info">Email: </li>
                        <li><input type="email" class="form-control" name="email" required="true"></li>
                    </ul>
                    <ul>
                        <li class="text-info">Mobile Number: </li>
                        <li><input type="text" class="text" name="mobnum" required="true" maxlength="10" pattern="[0-9]+"></li>
                    </ul>
                    <ul>
                        <li class="text-info">Event Date: </li>
                        <li><input type="date" class="form-control" name="edate" required="true"></li>
                    </ul>                     
                    <ul>
                        <li class="text-info">Event Starting Time:</li>
                        <li><select type="text" class="form-control" name="est" required="true">
                            <option value="">Select Starting Time</option>
                            <option value="1 a.m">1 a.m</option>
                            <option value="2 a.m">2 a.m</option>
                            <option value="3 a.m">3 a.m</option>
                            <option value="4 a.m">4 a.m</option>
                            <option value="5 a.m">5 a.m</option>
                            <option value="6 a.m">6 a.m</option>
                            <option value="7 a.m">7 a.m</option>
                            <option value="8 a.m">8 a.m</option>
                            <option value="9 a.m">9 a.m</option>
                            <option value="10 a.m">10 a.m</option>
                            <option value="11 a.m">11 a.m</option>
                            <option value="12 p.m">12 a.m</option>
                            <option value="1 p.m">1 p.m</option>
                            <option value="2 p.m">2 p.m</option>
                            <option value="3 p.m">3 p.m</option>
                            <option value="4 p.m">4 p.m</option>
                            <option value="5 p.m">5 p.m</option>
                            <option value="6 p.m">6 p.m</option>
                            <option value="7 p.m">7 p.m</option>
                            <option value="8 p.m">8 p.m</option>
                            <option value="9 p.m">9 p.m</option>
                            <option value="10 p.m">10 p.m</option>
                            <option value="11 p.m">11 p.m</option>
                            <option value="12 a.m">12 a.m</option>
                        </select></li>
                    </ul>
                    <ul>
                        <li class="text-info">Event Ending Time: </li>
                        <li><select type="text" class="form-control" name="eetime" required="true">
                            <option value="">Select Ending Time</option>
                            <option value="1 a.m">1 a.m</option>
                            <option value="2 a.m">2 a.m</option>
                            <option value="3 a.m">3 a.m</option>
                            <option value="4 a.m">4 a.m</option>
                            <option value="5 a.m">5 a.m</option>
                            <option value="6 a.m">6 a.m</option>
                            <option value="7 a.m">7 a.m</option>
                            <option value="8 a.m">8 a.m</option>
                            <option value="9 a.m">9 a.m</option>
                            <option value="10 a.m">10 a.m</option>
                            <option value="11 a.m">11 a.m</option>
                            <option value="12 p.m">12 a.m</option>
                            <option value="1 p.m">1 p.m</option>
                            <option value="2 p.m">2 p.m</option>
                            <option value="3 p.m">3 p.m</option>
                            <option value="4 p.m">4 p.m</option>
                            <option value="5 p.m">5 p.m</option>
                            <option value="6 p.m">6 p.m</option>
                            <option value="7 p.m">7 p.m</option>
                            <option value="8 p.m">8 p.m</option>
                            <option value="9 p.m">9 p.m</option>
                            <option value="10 p.m">10 p.m</option>
                            <option value="11 p.m">11 p.m</option>
                            <option value="12 a.m">12 a.m</option>
                            <option value="12 a.m">12 a.m</option>
							 </select></li>
						 </ul>
						 <ul>
							 <li class="text-info">Venue Address:</li>
							 <li><textarea type="text" class="form-control" name="vaddress" required="true" ></textarea></li>
						 </ul>
						 <ul>
							 <li class="text-info">Type of Event:</li>
							 <li><select type="text" class="form-control" name="eventtype" required="true" >
							 	<option value="">Choose Event Type</option>
							 	<?php 

$sql2 = "SELECT * from   tbleventtype ";
$query2 = $dbh -> prepare($sql2);
$query2->execute();
$result2=$query2->fetchAll(PDO::FETCH_OBJ);

foreach($result2 as $row)
{          
    ?>  
<option value="<?php echo htmlentities($row->EventType);?>"><?php echo htmlentities($row->EventType);?></option>
 <?php } ?>
							 </select></li>
						 </ul>	
						 <ul>
							 <li class="text-info">Additional Information:</li>
							 <li><textarea type="text" class="form-control" name="addinfo" required="true"></textarea></li>
						 </ul>					
						 <input type="submit" name="submit" value="Book">					 
					 </form>
				 </div>
				 <div class="col-md-6 contact-right">
					 	<div class="contact-map">
						<img src="images/431427.jpg" class="img-responsive" height="900" width="500" alt=""/>
						</div>
				 </div>
				 <div class="clearfix"></div>
			 </div>
		 </div>
		<?php include_once('includes/footer.php');?>
	 </div>
</div>
<!---->

<!---->
</body>
</html>