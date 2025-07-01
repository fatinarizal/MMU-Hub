<?php
session_start();
include "../connection.php";

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

// Fetch user details from the database
$user_id = $_SESSION["user_id"];
$sql = "SELECT username, email, phone FROM users WHERE id = '$user_id'";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/profile.css"> 
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
            <li><a href="userdashboard.php">Dashboard</a></li>
            <li><a href="profile.php" class="active">Profile</a></li>
            <li><a href="user_announcements.php">Announcements</a></li>
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
            <div class="profile-box">
                <h2>MY PROFILE</h2>
                <form method="POST" action="">
                    <div class="profile-field">
                        <label>Username:</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>
                    <div class="profile-field">
                        <label>Email:</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="profile-field">
                        <label>Phone Number:</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                    </div>
                    <div class="profile-field">
                        <label>Password:</label>
                        <input type="password" value="********" readonly>
                    </div>
                    
                    <div class="center">
                        <a href="changepw.php" class="button">Change Password</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
