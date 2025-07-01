<?php
session_start();
include '../connection.php'; // Include the database connection

// Ensure user is logged in
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php"); // Redirect to login if not staff or student
    exit();
}

$id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Logged-in user's ID
$sender_id = isset($_GET['sender_id']) ? $_GET['sender_id'] : null; // Sender ID passed via URL

if ($id === null || $sender_id === null) {
    die("User ID or sender ID not set in session or URL. Please log in.");
}

// Delete all messages between the logged-in user and the selected sender
$query = "DELETE FROM messages 
          WHERE (id = ? AND recipientID = ?) 
             OR (id = ? AND recipientID = ?)";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiii", $sender_id, $id, $id, $sender_id); // Bind parameters for both sender and recipient
$stmt->execute();

// Redirect back to the view all messages page
header("Location: viewAllMessages.php");
exit();
?>
