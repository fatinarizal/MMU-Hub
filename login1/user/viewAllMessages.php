<?php
session_start();
include '../connection.php'; // Include the database connection

// Ensure user is logged in and has staff or student role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php"); // Redirect to login if not staff or student
    exit();
}

$id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Use null if not set
if ($id === null) {
    die("User ID not set in session. Please log in.");
}

// Modify the query to group messages by username and concatenate message content
$query = "SELECT messages.id AS sender_id, users.username, 
                 GROUP_CONCAT(messages.content SEPARATOR '||') AS grouped_messages, 
                 MAX(messages.createdTime) AS last_message_time 
          FROM messages 
          JOIN users ON messages.id = users.id  
          WHERE messages.recipientID = ? 
          GROUP BY users.username 
          ORDER BY last_message_time DESC";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id); // Bind the recipientID parameter
$stmt->execute();

$result = $stmt->get_result(); // Get the result set

// Error handling for query
if (!$result) {
    die("Query failed: " . $stmt->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../css/viewAllMessages.css"> 
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
        <div class="container">  
            <main>
                <h1>Messages</h1>
                <div class="messages-section">
                    <?php if ($result->num_rows === 0) : ?>
                        <p>No messages found!</p>
                        <a href="startConversation.php" class="start-new-conversation-button">Start a New Conversation</a>
                    <?php else : ?>
                        <?php while ($row = $result->fetch_assoc()) { 
                            $messages = explode('||', $row['grouped_messages']); // Split concatenated messages
                        ?>
                            <div class="message">
                                <div class="avatar"></div>
                                <div class="message-details">
                                    <h3><?php echo htmlspecialchars($row['username']); ?></h3>
                                    <?php foreach ($messages as $message) { ?>
                                        <p><?php echo htmlspecialchars(substr($message, 0, 50)) . '...'; ?></p>
                                    <?php } ?>
                                    <p><small>Last message at: <?php echo htmlspecialchars($row['last_message_time']); ?></small></p>
                                </div>
                                <a href="viewOneMessage.php?sender_id=<?php echo $row['sender_id']; ?>" class="view-button">View</a>
                                <a href="deleteMessage.php?sender_id=<?php echo $row['sender_id']; ?>" class="delete-button" onclick="return confirm('Are you sure you want to delete this chat?');">Delete</a>
                            </div>
                        <?php } ?>
                        <a href="startConversation.php" class="start-new-conversation-button">Start a New Conversation</a>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
