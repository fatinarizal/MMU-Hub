<?php
session_start();
include "../connection.php";

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Check if 'id' is provided in the URL
if (isset($_GET['id'])) {
    $venue_id = intval($_GET['id']);

    // Delete the venue
    $sql = "DELETE FROM venue WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $venue_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Venue deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete venue.";
    }

    // Redirect back to the venue list page
    header("Location: editvenue.php");
    exit();
}
?>
