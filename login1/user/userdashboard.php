<?php
session_start();

// Ensure user is logged in and has staff or student role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php"); // Redirect to login if not staff or student
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/user_dashboard.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script>
        // Toggle side navigation
        function toggleNav() {
            const sideNav = document.querySelector('.side-nav');
            sideNav.classList.toggle('active');
        }
    </script>
</head>

<body>

<!-- Top Navigation Bar -->
<header>
    <div class="navbar-header">
        <button onclick="toggleNav()" class="toggle-button">☰</button> <!-- Button to toggle side navigation -->
        <div class="navbar-brand">MMU HUB</div>
    </div>
    <div class="user-info">
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <a href="../logout.php" class="logout-btn">Log Out</a>
    </div>
</header>

<!-- Side Navigation -->
<div class="side-nav">
    <ul>
        <li><a href="userdashboard.php" class="active">Dashboard</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="user_announcements.php">Announcements</a></li>
        <li><a href="user_booking.php">Venue Bookings</a></li>
        <li><a href="book.php">Booking Slot</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="viewAllMessages.php">Messages</a></li>
        <li><a href="feedback.php">Feedback</a></li>
        <li><a href="viewFAQ.php">FAQ</a></li>
    </ul>
</div>

<!-- Main Content Area -->
<main class="main-content">
    <h1>Dashboard</h1>
    <div class="dashboard-buttons">
        <a href="profile.php" class="button">Profile</a>
        <a href="user_announcements.php" class="button">Announcements</a>
        <a href="user_booking.php" class="button">Venue Bookings</a>
        <a href="book.php" class="button">Booking Slot</a>
        <a href="cart.php" class="button">Cart</a>
        <a href="viewAllMessages.php" class="button">Messages</a>
        <a href="feedback.php" class="button">Feedback</a>
        <a href="viewFAQ.php" class="button">FAQ</a>
    </div>
</main>

</body>

</html>
