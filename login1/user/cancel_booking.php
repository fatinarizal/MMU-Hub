<?php
session_start();
include "../connection.php";
$_SESSION['success_message'] = "Booking has been successfully canceled.";


// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Check if cart_id is set in POST request
if (isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // Prepare and execute remove query
    $delete_query = "DELETE FROM booking WHERE id = $booking_id";
    if (mysqli_query($conn, $delete_query)) {        
        $_SESSION['success_message'] = "Booking has been successfully canceled.";
        

    } else {
        $_SESSION['error_message'] = "Failed to delete booking. Please try again.";
    }

    header("Location: book.php");
exit();
}
?>
