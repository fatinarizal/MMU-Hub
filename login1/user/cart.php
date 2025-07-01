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

// Check if the form was submitted with selected venues
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected_venues'])) {
    $selectedVenues = $_POST['selected_venues'];
    $errors = [];
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

            // Insert into booking table
            $insert_query = "
                INSERT INTO booking (status, dateCreated, bookingDate, bookingStartTime, bookingEndTime, users_id, venue_id, cart_id)
                VALUES (1, NOW(), '$selectedDate', '$startTime', '$endTime', $user_id, $venue_id, $cart_id)";

                if (mysqli_query($conn, $insert_query)) {
                    // Remove item from cart after booking
                    $delete_cart_query = "DELETE FROM cart WHERE id = $cart_id";
                    mysqli_query($conn, $delete_cart_query);
    
                    // Store success message in session
                    $_SESSION['success_message'] = "Venue(s) have been successfully booked.";
                } else {
                    $errors[] = "Failed to book venue(s). Please try again.";
                }
        }
    }

    if (count($errors) > 0) {
        $error_message = implode(", ", $errors);
    }

    // Redirect back to the cart page after processing the form to prevent form resubmission
    header("Location: cart.php");
    exit();
}

$cart_query = "
    SELECT cart.id AS cart_id, venue.name AS venue_name, venue.location, cart.venue_id, cart.date, cart.startTime, cart.endTime,
        IF (
            EXISTS (
                SELECT 1 FROM booking 
                WHERE booking.venue_id = cart.venue_id 
                AND booking.bookingDate = cart.date
                AND (
                    booking.bookingStartTime < cart.endTime 
                    AND booking.bookingEndTime > cart.startTime
                )
                AND booking.status = 1
            ), 'Unavailable', 'Available'
        ) AS availability
        FROM cart
        LEFT JOIN venue ON cart.venue_id = venue.id
        WHERE cart.users_id = $user_id
        ORDER BY cart.date ASC, cart.startTime ASC
";

$cart_result = mysqli_query($conn, $cart_query);

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Cart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <script>

        // Toggle side navigation
        function toggleNav() {
            const sideNav = document.querySelector('.side-nav');
            sideNav.classList.toggle('active');
        }

        // Display success or error messages from URL query parameters
        const urlParams = new URLSearchParams(window.location.search);
        const message = urlParams.get('message');
        if (message) {
            alert(message);
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
            <li><a href="cart.php" class="active">Cart</a></li>
            <li><a href="viewAllMessages.php">Messages</a></li>
            <li><a href="feedback.php">Feedback</a></li>
            <li><a href="viewFAQ.php">FAQ</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">  
            <main>
                <h2>Cart</h2>
                <div class="cart">
                    <form method="POST" action="cart.php" onsubmit="return confirmBooking()">
                    <table>
                        <tr>
                            <th>Select</th>
                            <th>Venue Name</th>
                            <th>Location</th>
                            <th>Date to Book</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Availability</th>
                            <th>Action</th>
                        </tr>

                        <?php
                        if (mysqli_num_rows($cart_result) > 0) {
                            while ($row = mysqli_fetch_assoc($cart_result)) {
                                echo "<tr>";
                                echo "<td><input type='checkbox' name='selected_venues[]' value='" . $row['cart_id'] . "'data-availability='" . $row['availability'] . "' onchange='updateSelectedCount()'></td>"; // Checkbox for selecting venues
                                echo "<td>" . htmlspecialchars($row['venue_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['startTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['endTime']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['availability']) . "</td>";
                                echo "<td>
                                    <button type='button' class='delete-btn red-btn' onclick='confirmDelete(" . $row['cart_id'] . ")'>Delete</button>
                                </td>";
                                
                            }
                        } else {
                            echo "<tr><td colspan='8' class='empty-cart'>Your cart is empty.</td></tr>";
                        }
                        ?>
                    </table>

                        <div class="book-btn-container">
                            <span id="selectedCount">Total: 0</span> <!-- Display count here -->
                            <input type="hidden" name="cart_ids" id="cart_ids" value=""> <!-- Hidden field to store cart ids selected -->
                            <button type="submit" class="book-btn">Book</button>
                        </div>

                    </form>
                </div>
            </main>
        </div>         
    </div>

    <script>

        // calculation to update how many boxes selected
        function updateSelectedCount() {
            //Get all checkboxes
            var checkboxes = document.querySelectorAll("input[name='selected_venues[]']");
        
            // count how many are checked
            var selectedCount = 0;
            checkboxes.forEach(function(checkbox) {
                if(checkbox.checked) {
                    selectedCount++;
                }
            });

            //update the count message
            document.getElementById('selectedCount').innerText = "Total: " + selectedCount;
        }

        function confirmBooking() {
            const checkboxes = document.querySelectorAll("input[name='selected_venues[]']:checked")

            //check if selected at least one venue to book
            if (checkboxes.length == 0) {
                alert("Please select at least one venue to book.");
                return false;
            }

            //check availability
            let bookedVenueFound = false;
            checkboxes.forEach(function(checkbox) {
                const availability = checkbox.getAttribute('data-availability');
                if(availability == 'Unavailable') {
                    bookedVenueFound = true;
                }
            });

            if(bookedVenueFound) {
                alert("One or more of your selected venues are already booked. Please remove them from your selection.");
                return false;
            }

            // Display confirmation dialog to book
            const confirmAction = confirm("Are you sure you want to book the selected venues?");
            
            if (confirmAction) {
                return true;  // Proceed with form submission
            } else {
                return false; // Prevent form submission
            }
        }

        function confirmDelete(cartId) {
            if (confirm("Are you sure you want to delete this item from your cart?")) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'delete_cart.php'; 

                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = 'cart_id';
                hiddenField.value = cartId;

                form.appendChild(hiddenField);
                document.body.appendChild(form);
                form.submit(); // Submit the form
            }
        }

        // Display success or error messages from the session
        <?php if (isset($_SESSION['success_message'])): ?>
            alert("<?php echo $_SESSION['success_message']; ?>");
            <?php unset($_SESSION['success_message']); // Clear message after displaying ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            alert("<?php echo $_SESSION['error_message']; ?>");
            <?php unset($_SESSION['error_message']); // Clear message after displaying ?>
        <?php endif; ?>

    </script>

</body>
</html>