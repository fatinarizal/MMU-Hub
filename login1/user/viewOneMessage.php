<?php
session_start();
include '../connection.php'; // Include the database connection

// Ensure user is logged in and has staff or student role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php"); // Redirect to login if not staff or student
    exit();
}

$id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Use null if not set
$sender_id = isset($_GET['sender_id']) ? $_GET['sender_id'] : null; // Get the sender_id from the URL

if ($id === null || $sender_id === null) {
    die("User ID or sender ID not set in session or URL. Please log in.");
}

// Fetch all messages between the logged-in user and the selected sender
$query = "SELECT messages.content, messages.createdTime, 
                 messages.attachment, 
                 sender.username AS sender_username, 
                 recipient.username AS recipient_username 
          FROM messages 
          JOIN users AS sender ON messages.id = sender.id 
          JOIN users AS recipient ON messages.recipientID = recipient.id 
          WHERE (messages.id = ? AND messages.recipientID = ?) 
             OR (messages.id = ? AND messages.recipientID = ?) 
          ORDER BY messages.createdTime ASC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("iiii", $sender_id, $id, $id, $sender_id); // Bind the sender and recipient IDs
$stmt->execute();

$result = $stmt->get_result(); // Get the result set

// Error handling for query
if (!$result) {
    die("Query failed: " . $stmt->error);
}

// Fetch the first row to get the sender's username
$first_row = $result->fetch_assoc();
$sender_username = $first_row ? $first_row['sender_username'] : "Unknown Sender";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../css/viewOneMessage.css"> 
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


    <!-- <a href="viewAllMessages.php">View All Messages</a> | <a href="../logout.php">Log Out</a> -->
    <div class="main-content">
        <div class="container">  
            <main>
                <h1>Messages</h1>
                <div class="messages-section">
                <?php if ($result->num_rows === 0) : ?>
                    <p>No messages found!</p>
                <?php else : ?>
                    <?php do { ?>
                        <div class="message">
                            <strong><?php echo htmlspecialchars($first_row['sender_username']); ?>:</strong>
                            <p><?php echo htmlspecialchars($first_row['content']); ?></p>
                            <?php if (!empty($first_row['attachment'])): ?>
                                <p>
                                    <a href="../uploads/<?php echo htmlspecialchars($first_row['attachment']); ?>" target="_blank">View Attachment</a>
                                </p>
                            <?php endif; ?>
                            <p><small><?php echo htmlspecialchars($first_row['createdTime']); ?></small></p>
                        </div>
                    <?php } while ($first_row = $result->fetch_assoc()); ?>
                <?php endif; ?>
                </div>

            <div class="reply-section">
                <form action="sendMessage.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="recipient_id" value="<?php echo htmlspecialchars($sender_id); ?>">
                    <textarea name="message_content" placeholder="Type your message..." required></textarea>
                    <br>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="attachment">
                    <button type="submit" class="send-btn">Send</button>
                </form>
            </div>

            <!-- Back to All Messages Button -->
            <div class="back-button-container">
                <a href="viewAllMessages.php" class="back-btn"><i class="fa fa-arrow-left"></i> Back to All Messages</a>
            </div>

            </main>
        </div>
    </div>
</body>
</html>
