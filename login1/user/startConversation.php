<?php
session_start();
include '../connection.php'; // Include the database connection

// Ensure user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch users for the recipient dropdown (excluding the logged-in user)
$query = "SELECT id, username FROM users WHERE id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Error handling for query
if ($stmt->error) {
    die("Query failed: " . htmlspecialchars($stmt->error));
}

// Check if any users were found
$users = $result->fetch_all(MYSQLI_ASSOC);

// Get any error message from the URL
$error_message = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start a New Conversation</title>
    <link rel="stylesheet" href="../css/startConversation.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> <!-- Font Awesome for icons -->
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
            <li><a href="user_announcements.php">News & Announcements</a></li>
            <li><a href="user_booking.php">Venue Bookings</a></li>
            <li><a href="book.php">Booking Slot</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="viewAllMessages.php" class="active">Messages</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php">FAQ</a></li>
        </ul>
    </div>

    <div class="main-content">
    <h1>Start a New Conversation</h1>
        <div class="container">
            <?php if ($error_message): ?>
                <p class="error-message" style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form action="sendMessage.php" method="POST" enctype="multipart/form-data">
                <label for="recipient">Select Recipient:</label>
                <select name="recipient_id" id="recipient" required>
                    <option value="">Select a user...</option>
                    <?php if (empty($users)): ?>
                        <option value="">No users available</option>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id']; ?>">
                                <?php echo htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>

                <label for="message">Message:</label>
                <textarea name="message_content" id="message" rows="5" required></textarea>

                <label for="attachment">Attach a file (optional):</label>
                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf">

                <button type="submit">Send Message</button>
            </form>
            <div class="back-button-container">
                    <a href="viewAllMessages.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to All Messages</a>
            </div>
        </div>
    </div>
</body>
</html>
