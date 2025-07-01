<?php
session_start();
include "../connection.php"; // Include the database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // Delete related faqs first
    $delete_faqs_sql = "DELETE FROM faqs WHERE submitted_by = ?";
    $stmt_faqs = mysqli_prepare($conn, $delete_faqs_sql);
    
    if ($stmt_faqs) {
        mysqli_stmt_bind_param($stmt_faqs, 'i', $user_id);
        mysqli_stmt_execute($stmt_faqs);
        mysqli_stmt_close($stmt_faqs);
    }

    // Delete related messages next
    $delete_messages_sql = "DELETE FROM messages WHERE id = ?";
    $stmt_messages = mysqli_prepare($conn, $delete_messages_sql);
    
    if ($stmt_messages) {
        mysqli_stmt_bind_param($stmt_messages, 'i', $user_id);
        mysqli_stmt_execute($stmt_messages);
        mysqli_stmt_close($stmt_messages);
    }

    // Now delete the user from the database
    $delete_user_sql = "DELETE FROM users WHERE id = ?";
    $stmt_user = mysqli_prepare($conn, $delete_user_sql);
    
    if ($stmt_user) {
        mysqli_stmt_bind_param($stmt_user, 'i', $user_id);
        
        if (mysqli_stmt_execute($stmt_user)) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting user. Please try again.";
        }
        
        mysqli_stmt_close($stmt_user);
    } else {
        $_SESSION['error'] = "Error preparing the SQL statement.";
    }
}

// Redirect back to the edit user profile page
header("Location: edit_user_profile.php");
exit();
?>
