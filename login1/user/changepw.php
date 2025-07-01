<?php
session_start();
include "../connection.php";

// Check if the user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    // Verify current password
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && $current_password === $user['password']) {
        if ($new_password === $confirm_password) {
            // Update password in the database
            $update_sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
            if (mysqli_query($conn, $update_sql)) {
                $message = "Password updated successfully.";
            } else {
                $message = "Error updating password: " . mysqli_error($conn);
            }
        } else {
            $message = "New password and confirm password do not match! Please Try Again.";
        }
    } else {
        $message = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        function validateForm() {
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            
            if (newPassword !== confirmPassword) {
                alert("New password and confirm password do not match!");
                return false;
            }
            return true;
        }

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
            <li><a href="profile.php" class="active">Profile</a></li>
            <li><a href="user_announcements.php">News & Announcements</a></li>
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
                <h2>Update Password</h2>
                <?php if ($message): ?>
                    <p class="message"><?php echo $message; ?></p>
                <?php endif; ?>
                <form method="POST" action="" onsubmit="return validateForm()">
                    <div class="profile-field">
                        <label>Current Password:</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="profile-field">
                        <label>New Password:</label>
                        <input type="password" name="new_password" id="new_password" required>
                    </div>
                    <div class="profile-field">
                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                    <div class="center">
                        <button type="submit" class="button">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>