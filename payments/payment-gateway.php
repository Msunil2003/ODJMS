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

// Set your Stripe secret key
\Stripe\Stripe::setApiKey('sk_test_51QJYcOSIr5Ob5LdJWIKbcjirv7R4bM37BrjzECsXnK7dH190zLZJpXKQ6Z3XNjwKXzkNgnxzO577ja02o5dQNb3Q00SioHeYUg'); // Replace with your actual secret key

// Function to fetch booking details by booking ID and join with tblservice table
if (isset($_GET['bookingid']) && !empty($_GET['bookingid'])) {
    $bookingId = $_GET['bookingid'];

    // SQL query to fetch booking details
    $sqlFetch = "SELECT b.BookingID, b.Name, b.Email, b.MobileNumber, b.EventDate, b.EventType, 
                        s.ServiceName, s.ServicePrice, b.VenueAddress
                 FROM tblbooking b
                 JOIN tblservice s ON b.ServiceID = s.ID
                 WHERE b.BookingID = :bookingId";
    
    $query = $dbh->prepare($sqlFetch);
    $query->bindParam(':bookingId', $bookingId, PDO::PARAM_STR);
    $query->execute();
    
    // Check if query returns results
    $result = $query->fetch(PDO::FETCH_OBJ);
    
    if ($result) {
        try {
            // Step 1: Create a customer on Stripe
            $customer = \Stripe\Customer::create([
                'name' => $result->Name,
                'email' => $result->Email,
                'address' => [
                    'line1' => $result->VenueAddress,  // Address line 1
                    'city' => 'Your City',  // Adjust as necessary
                    'state' => 'Your State',  // Adjust as necessary
                    'postal_code' => 'Your Postal Code',  // Adjust as necessary
                    'country' => 'IN',  // Country Code for India
                ],
            ]);

            // Step 2: Create a Stripe Checkout session
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'inr',
                        'product_data' => [
                            'name' => $result->ServiceName,
                        ],
                        'unit_amount' => $result->ServicePrice * 100,  // Amount in cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer' => $customer->id,  // Stripe customer ID
                'success_url' => 'http://localhost/odjms/payments/success.php?session_id={CHECKOUT_SESSION_ID}&bookingid=' . urlencode($result->BookingID) . 
                 '&name=' . urlencode($result->Name) . 
                 '&email=' . urlencode($result->Email) . 
                 '&service_name=' . urlencode($result->ServiceName) . 
                 '&service_price=' . urlencode($result->ServicePrice) . 
                 '&venue_address=' . urlencode($result->VenueAddress) . 
                 '&event_date=' . urlencode($result->EventDate) . 
                 '&event_type=' . urlencode($result->EventType),

                'cancel_url' => 'http://localhost/odjms/payments/cancel.php?bookingid=' . urlencode($result->BookingID),
            ]);

            // Get the Stripe Checkout session URL
            $checkoutUrl = $session->url;

            // Step 3: Redirect the user to Stripe Checkout page
            header("Location: " . $checkoutUrl);
            exit;

        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        echo "No booking found with this ID.";
    }
} else {
    echo "Booking ID not provided.";
}
?>
