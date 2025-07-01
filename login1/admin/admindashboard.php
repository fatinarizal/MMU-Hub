<?php
session_start();

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css"> <!-- Updated the CSS path -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- Escaping output for security -->
        </div>
        <ul>
            <li><a href="admindashboard.php" class="active">Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
        <br>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
            <nav>
                <a href="../home.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>

        <div class="dashboard-buttons">
            <a href="edit_user_profile.php" class="button">Edit User Profile</a>
            <a href="manageFAQ.php" class="button">Manage FAQ</a>
            <a href="manage_announcements.php" class="button">Manage Announcement</a>
            <a href="manage_booking.php" class="button">Manage Booking</a>
            <a href="editvenue.php" class="button">Edit Venue</a>
            <a href="handle_feedback.php" class="button">Handle Feedback</a>
        </div>
    </div>
</body>

</html>
