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

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_venues'])) {
    $selectedVenues = $_POST['selected_venues'];
    $errors = [];
    $bookedVenues = [];

    foreach ($selectedVenues as $cart_id) {
        // Get the details of the cart item
        $cart_query = "
            SELECT cart.venue_id, cart.date, cart.startTime, cart.endTime, venue.name 
            FROM cart
            LEFT JOIN venue ON cart.venue_id = venue.id
            WHERE cart.id = $cart_id AND cart.users_id = $user_id";
        $cart_result = mysqli_query($conn, $cart_query);
        $cart_item = mysqli_fetch_assoc($cart_result);

        if ($cart_item) {
            $venue_id = $cart_item['venue_id'];
            $selectedDate = $cart_item['date'];
            $startTime = $cart_item['startTime'];
            $endTime = $cart_item['endTime'];
            $venueName = $cart_item['name'];

            $insert_query = "
                INSERT INTO booking (status, dateCreated, bookingDate, bookingStartTime, bookingEndTime, users_id, venue_id, cart_id)
                VALUES (1, NOW(), '$selectedDate', '$startTime', '$endTime', $user_id, $venue_id, $cart_id)";
                
            if (mysqli_query($conn, $insert_query)) {

                // remove item from cart database
                $delete_cart_query = "DELETE FROM cart where id = $cart_id";
                mysqli_query($conn, $delete_cart_query);

                $success_message = "Venue(s) has been successfully booked.";

            } else {
                $errors[] = "Failed to book venue(s). Please try again.";
            }
        }
    }

    if (count($errors) > 0) {
        $error_message = implode(", ", $errors);
    }

} 

$book_query = "
    SELECT booking.id, booking.cart_id AS cart_id, venue.name AS venue_name, venue.location, 
           booking.bookingDate AS date, booking.bookingStartTime AS startTime, booking.bookingEndTime AS endTime, booking.status
    FROM booking
    JOIN venue ON booking.venue_id = venue.id
    WHERE booking.users_id = $user_id
";

$book_result = mysqli_query($conn, $book_query);

// Get success or error message from session if available
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear the session messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/book.css">
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
            <li><a href="book.php" class="active">Booking Slot</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="viewAllMessages.php">Messages</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php">FAQ</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="container">  
            <main>
                <h2>Your Booking Slots</h2>
                <div class="book">
                    <table>
                        <tr>
                            <th>Venue Name</th>
                            <th>Location</th>
                            <th>Date of Booking</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>

                        <?php
                        if (mysqli_num_rows($book_result) > 0) {
                            while ($row = mysqli_fetch_assoc($book_result)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['venue_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['startTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['endTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['status'] == 1 ? 'Booked' : 'Not Booked') . "</td>";
                                echo "<td>
                                    <button type='button' class='cancel-btn' onclick='confirmCancel(" . $row['id'] . ")'>Cancel</button>
                                </td>";
                                echo "</tr>";
                                
                            }
                        } else {
                            echo "<tr><td colspan='8' class='empty-book'>Your booking slot is empty.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </main>
        </div>      
    </div>

    <script>
        // Display alert for success or error messages
        <?php if(!empty($success_message)): ?>
            alert("<?php echo addslashes($success_message); ?>");
        <?php endif; ?>

        <?php if(!empty($error_message)): ?>
            alert("<?php echo addslashes($error_message); ?>");
        <?php endif; ?>

        function confirmCancel(bookingID) {
            if (confirm("Are you sure you want to cancel the booking?")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'cancel_booking.php'; 

                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'booking_id';
                hiddenField.value = bookingID;

                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit(); // Submit the form
            }
        }
    </script>
</body>
</html>