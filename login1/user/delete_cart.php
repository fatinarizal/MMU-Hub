<?php
session_start();
include "../connection.php";

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Check if cart_id is set in POST request
if (isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    // Prepare and execute the delete query
    $delete_query = "DELETE FROM cart WHERE id = $cart_id";
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to cart with success message
        header("Location: cart.php?message=Item deleted successfully");
        exit(); // Make sure to exit after the header redirect
    } else {
        // Handle the error
        header("Location: cart.php?message=Failed to delete item");
        exit();
    }
} else {
    // If no cart_id provided, redirect back to cart
    header("Location: cart.php");
    exit();
}
?>
