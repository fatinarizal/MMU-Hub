<?php
session_start();
include '../connection.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'staff' && $_SESSION['role'] !== 'student')) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$user_query = "SELECT id FROM users WHERE username = '$username'";
$user_result = mysqli_query($conn, $user_query);
$user_row = mysqli_fetch_assoc($user_result);
$user_id = $user_row['id'];

if (isset($_POST['add_to_cart'])) {
    $venue_id = $_POST['venue_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $check_query = "SELECT * FROM booking 
                    WHERE venue_id = $venue_id 
                    AND bookingDate = '$date' 
                    AND (bookingStartTime < '$end_time' AND bookingEndTime > '$start_time') 
                    AND status = 1";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) == 0) {
        $insert_query = "INSERT INTO cart (users_id, venue_id, date, startTime, endTime) 
                         VALUES ($user_id, $venue_id, '$date', '$start_time', '$end_time')";
        if (mysqli_query($conn, $insert_query)) {
            echo "<script>alert('Venue added to cart successfully!');</script>";
        } else {
            echo "<script>alert('Failed to add venue to cart.');</script>";
        }
    } else {
        echo "<script>alert('This venue is already booked for the selected time.');</script>";
    }
}

$search_query = "";
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $search_query = "WHERE name LIKE '%$search_term%' OR location LIKE '%$search_term%'";
}

$sql = "SELECT * FROM venue $search_query";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Booking</title>
    <link rel="stylesheet" href="../css/user_booking.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
            <li><a href="user_booking.php" class="active">Venue Bookings</a></li>
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
                <h2>Available Venues</h2>
            <!-- Search Form -->
            <form method="POST" action="">
                <input type="text" name="search_term" placeholder="Search by venue or location">
                <button type="submit" name="search">Search</button>
            </form>

            <div class="venues">
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $venue_id = $row['id'];
                            echo "<div class='venue'>
                                    <h3>{$row['name']}</h3>
                                    <p><strong>Location</strong>: {$row['location']}</p>
                                    <form method='POST' action='' onsubmit='return validateTime(this)'>
                                        <input type='hidden' name='venue_id' value='{$row['id']}'>
                                        <label for='date'>Date:</label>
                                        <input type='date' name='date' required><br>
                                        <label for='start_time'>Start Time:</label>
                                        <input type='time' name='start_time' required><br>
                                        <label for='end_time'>End Time:</label>
                                        <input type='time' name='end_time' required><br>
                                        <button type='submit' name='check_availability'>Check Availability</button>
                                    </form>";

                            if (isset($_POST['check_availability']) && $_POST['venue_id'] == $venue_id) {
                                $check_date = $_POST['date'];
                                $check_start_time = $_POST['start_time'];
                                $check_end_time = $_POST['end_time'];

                                $availability_query = "SELECT * FROM booking 
                                                       WHERE venue_id = $venue_id 
                                                       AND bookingDate = '$check_date' 
                                                       AND (bookingStartTime < '$check_end_time' AND bookingEndTime > '$check_start_time') 
                                                       AND status = 1";
                                $availability_result = mysqli_query($conn, $availability_query);

                                if (mysqli_num_rows($availability_result) > 0) {
                                    echo "<p><strong>Availability:</strong> Unavailable</p>";
                                } else {
                                    echo "<p><strong>Availability:</strong> Available</p>";
                                    echo "<form method='POST' action=''>
                                            <input type='hidden' name='venue_id' value='{$row['id']}'>
                                            <input type='hidden' name='date' value='$check_date'>
                                            <input type='hidden' name='start_time' value='$check_start_time'>
                                            <input type='hidden' name='end_time' value='$check_end_time'>
                                            <button type='submit' name='add_to_cart'>Add to Cart</button>
                                          </form>";
                                }
                            }

                            echo "</div>";
                        }
                    } else {
                        echo "<p>No venues available.</p>";
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Ensure the minimum date allowed is today for all date inputs
    const today = new Date().toISOString().split('T')[0];  // Get today's date in 'YYYY-MM-DD' format
    document.querySelectorAll('input[type="date"]').forEach(dateField => {
        dateField.setAttribute('min', today);
    });

    // Validate the start and end time before form submission
    function validateTime(form) {
        const startTime = form.start_time.value;
        const endTime = form.end_time.value;

        if (startTime >= endTime) {
            alert("Please enter a valid start time and end time.");
            return false;
        }
        return true;
    }
    </script>

<?php $conn->close(); ?>
</body>
</html>