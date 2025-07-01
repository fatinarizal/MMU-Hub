<?php
session_start();
include '../connection.php'; // Include the database connection

// Ensure user is logged in
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php");
    exit();
}

// Get the current user ID and recipient ID from the form
$user_id = $_SESSION['user_id'] ?? null;
$recipient_id = $_POST['recipient_id'] ?? null;
$message_content = $_POST['message_content'] ?? null;

if ($user_id === null || $recipient_id === null || $message_content === null) {
    die("Missing required fields.");
}

// Handle file upload
$attachment_path = null;
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['attachment']['tmp_name'];
    $file_name = basename($_FILES['attachment']['name']);
    $upload_dir = '../uploads/'; // Make sure this directory exists and is writable

    // Move the uploaded file
    if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
        $attachment_path = $upload_dir . $file_name;
    } else {
        die("Failed to upload file.");
    }
}

// Insert the new message into the messages table
$query = "INSERT INTO messages (id, recipientID, content, createdTime, attachment) VALUES (?, ?, ?, NOW(), ?)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiss", $user_id, $recipient_id, $message_content, $attachment_path);
$success = $stmt->execute();

// Error handling
if ($success) {
    header("Location: viewOneMessage.php?sender_id=$recipient_id"); // Redirect to the conversation page
    exit();
} else {
    die("Failed to send message: " . $stmt->error);
}
?>
