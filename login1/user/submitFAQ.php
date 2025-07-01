<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit FAQ</title>
    <link rel="stylesheet" href="../css/submitFAQ.css">
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
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php" class="active">FAQ</a></li>
        </ul>
    </div>

    <div class="container">
            <div class="faq-box box">
                <main>
                <h1>FAQ Question Form</h1>
                <form method="POST" action="">
                    <div class="faq-box">
                        <?php
                        include "../connection.php";

                        // Check if the user is logged in
                        if (!isset($_SESSION["username"])) {
                            header("Location: login.php");
                            exit();
                        }
                        
                        // Handle FAQ submission
                        if (isset($_POST["submit_faq"])) {
                            $question = $_POST["question"];
                            $submitted_by = $_SESSION["user_id"];
                        
                            if (!empty($question)) {
                                $sql = "INSERT INTO faqs (question, submitted_by) VALUES ('$question', '$submitted_by')";
                                $res = mysqli_query($conn, $sql);
                                if ($res) {
                                    echo "<p>FAQ submitted successfully. Redirect to FAQ..</p>";
                                    // Redirect to dashboard after 2 seconds
                                    header("refresh:2; url=viewFAQ.php");
                                    exit(); // Ensure no further code is executed
                                } else {
                                    echo "<p>Failed to submit FAQ: " . mysqli_error($conn) . "</p>";
                                }
                            } else {
                                echo "<p>Please enter a question.</p>";
                            }
                        } else {
                        ?>

                            <div class="userBox">
                                <label>Type your question here:</label>
                                <textarea name="question" required></textarea>
                            </div>
                            <br>
                            <div class="center">
                                <button class="button" type="submit" name="submit_faq">Submit FAQ</button>
                            </div>
                            <br>
                    </div>
                </main>
                </form>
            </div>
            <?php
            }
            ?>
    </div>
</body>
</html>
