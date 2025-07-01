<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../css/viewFAQ.css"> 
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
            <li><a href="viewAllMessages.php">Messages</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php" class="active">FAQ</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">
            <main>
                <div class="faq-box">
                    <h1>Frequently Asked Questions (FAQ)</h1>
                    
                    <!-- Search Form -->
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search FAQs..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <br>
                        <br>
                        <button type="submit">Search</button>
                    </form>
                    
                    <?php
                    include "../connection.php";

                    // Check if the user is logged in
                    if (!isset($_SESSION["username"])) {
                        header("Location: login.php");
                        exit();
                    }

                    // Get the search term
                    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

                    // Fetch all FAQs from the database
                    $sql = "SELECT f.id, f.question, f.answer, f.submitted_by, u.username AS submitter, a.username AS answered_by
                            FROM faqs f 
                            JOIN users u ON f.submitted_by = u.id 
                            LEFT JOIN admins a ON f.answered_by = a.id
                            WHERE f.question LIKE '%$search%' 
                            ORDER BY f.id DESC";
                    $res = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($res) > 0) {
                        echo "<ul class='faq-list'>";
                        while ($row = mysqli_fetch_assoc($res)) {
                            echo "<li>";
                            
                            echo "<div class='question'>Q: " . htmlspecialchars($row['question']) . "</div>";
                            echo "<div class='answer'>A: " . htmlspecialchars($row['answer']) . "</div>";
                            echo "<div class='submitted-by'>Submitted by: " . htmlspecialchars($row['submitter']) . "</div>";
                            
                            // Show answered by admin if applicable
                            if ($row['answered_by']) {
                                echo "<div class='answered-by'>Answered by: " . htmlspecialchars($row['answered_by']) . "</div>";
                            } else {
                                echo "<div class='answered-by'>Not Being Answer Yet..</div>";
                            }

                            echo "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No FAQs found.</p>";
                    }
                    ?>
                    <div class="center">
                        <a href="submitFAQ.php" class="button">Submit a New FAQ</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
