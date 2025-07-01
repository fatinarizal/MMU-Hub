<?php
session_start();

include "../connection.php";

// Ensure user is logged in and has staff or student role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php"); // Redirect to login if not staff or student
    exit();
}

// Get the current user's ID
$username = $_SESSION['username'];
$user_query = "SELECT id FROM users WHERE username = '$username'";
$user_result = mysqli_query($conn, $user_query);
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];


// Handle adding feedback
if (isset($_POST['submit'])) {
    $feedback = $_POST['feedback'];
    $createdDate = date('Y-m-d H:i:s');

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO feedback (feedback, createdTime, users_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $feedback, $createdDate, $user_id); // Bind parameters

    if ($stmt->execute()) {
        // Show success message using JavaScript alert
        echo "<script>alert('Feedback submitted successfully!');</script>";
    } else {
        // Show error message using JavaScript alert
        $error_message = $stmt->error;
        echo "<script>alert('Error submitting feedback: $error_message');</script>";
    }
    $stmt->close(); // Close the statement

    // Prevent form resubmission by refreshing the page
    echo "<script>window.location.href = 'feedback.php';</script>";
    exit(); // Ensure no further processing occurs
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/feedback.css"> 
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
            <li><a href="user_announcements.php">News & Announcements</a></li>
            <li><a href="user_booking.php">Venue Bookings</a></li>
            <li><a href="book.php">Booking Slot</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="viewAllMessages.php">Messages</a></li>
            <li><a href="feedback.php" class="active">Feedback</a></li>
            <li><a href="viewFAQ.php">FAQ</a></li>
        </ul>
    </div>

        <div class="container">  
            <main>  
                <div class="form-container">
                    <div class="form">
                        <label for="feedback"><h1>Feedback</h1></label>
                        <form method="POST" action="">
                            <textarea name="feedback" id="feedback" required placeholder="Write your feedback here..."></textarea>
                            <div class="submit-btn">
                                <button type="submit" name="submit" class="btn-success">Submit Feedback</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
</body>

</html>