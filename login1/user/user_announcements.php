<?php
session_start();
include "../connection.php";

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch all announcements
$result = mysqli_query($conn, "SELECT * FROM announcements");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Announcement</title>
    <link rel="stylesheet" href="../css/user_announcement.css"> 
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
            <button onclick="toggleNav()" class="toggle-button">☰</button> 
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
            <li><a href="userdashboard.php">Dashboard</a></li>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="user_announcements.php" class="active">News & Announcements</a></li>
            <li><a href="user_booking.php">Venue Bookings</a></li>
            <li><a href="book.php">Booking Slot</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="viewAllMessages.php">Messages</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php">FAQ</a></li>
        </ul>
    </div>
       
    <div class="main-content">
        <div class="container">
            <main>
                <h2>Current News & Announcements </h2>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="announcement">
                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                            <?php if ($row['image']): ?>
                                <img src="../uploads/<?php echo htmlspecialchars(basename($row['image'])); ?>" alt="Announcement Image" width="100">
                            <?php endif; ?>
                            <?php if ($row['file']): ?>
                                <a href="../uploads/<?php echo htmlspecialchars(basename($row['file'])); ?>" download>Download File</a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No announcements available at this time.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>

    
</body>
</html>
