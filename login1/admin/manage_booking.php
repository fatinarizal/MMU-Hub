<?php
session_start();

include "../connection.php";

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}

// Handle booking update
if (isset($_POST['update'])) {
    $booking_id = $_POST['id'];
    $status = $_POST['status'];
    $bookingDate = $_POST['bookingDate'];
    $bookingStartTime = $_POST['bookingStartTime'];
    $bookingEndTime = $_POST['bookingEndTime'];

    $sql = "UPDATE booking SET 
                status = '$status', 
                bookingDate = '$bookingDate', 
                bookingStartTime = '$bookingStartTime', 
                bookingEndTime = '$bookingEndTime' 
            WHERE id = $booking_id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Booking updated successfully.";    
    } else {
        $_SESSION['error_message'] = "Error updating booking: " . $conn->error;
    }
}

// Handle booking deletion
if (isset($_GET['delete'])) {
    $booking_id = $_GET['delete'];

    $sql = "DELETE FROM booking WHERE id = $booking_id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Booking deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting booking: " . $conn->error;
    }
}

// Handle booking cancellation
if (isset($_POST['cancel'])) {
    $booking_id = $_POST['id'];

    $sql = "UPDATE booking SET status = 0 WHERE id = $booking_id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "Booking canceled successfully.";
    } else {
        $_SESSION['error_message'] = "Error canceling booking: " . $conn->error;
    }
}

// Handle new booking addition
if (isset($_POST['add'])) {
    $users_id = $_POST['users_id'];
    $venue_id = $_POST['venue_id'];
    $bookingDate = $_POST['bookingDate'];
    $bookingStartTime = $_POST['bookingStartTime'];
    $bookingEndTime = $_POST['bookingEndTime'];
    $status = $_POST['status'];

    $sql = "INSERT INTO booking (status, bookingDate, bookingStartTime, bookingEndTime, users_id, venue_id) 
            VALUES ('$status', '$bookingDate', '$bookingStartTime', '$bookingEndTime', '$users_id', '$venue_id')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success_message'] = "New booking added successfully.";
    } else {
        $_SESSION['error_message'] = "Error adding booking: " . $conn->error;
    }
}

// Fetch all bookings
$sql = "SELECT booking.id, booking.status, booking.bookingDate, booking.bookingStartTime, booking.bookingEndTime, 
               users.name AS user_name, venue.name AS venue_name, venue.location
        FROM booking
        INNER JOIN users ON booking.users_id = users.id
        INNER JOIN venue ON booking.venue_id = venue.id";

$result = $conn->query($sql);


//message
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']); // Clear messages after displaying

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Manage Booking</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/manage_booking.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">        
</head>

    <body>
    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- Escaping output for security -->
        </div>
        <ul>
            <li><a href="admindashboard.php">Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php" class="active">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
        <br>
    </div>

    <div class="main-content">
        <header>
            <h1>Manage Booking Slot</h1>
            <nav>
                <a href="admindashboard.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="book">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Venue Name</th>
                    <th>Location</th>
                    <th>Booking Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th></th>
                </tr>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['venue_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bookingDate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bookingStartTime']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['bookingEndTime']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status'] == 1 ? 'Booked' : 'Canceled Booking') . "</td>";
                        echo "<td>
                        <form method='GET' style='display:inline-block;'>
                                <input type='hidden' name='delete' value='" . $row['id'] . "'>
                                <input type='submit' class='btn btn-delete' value='Delete' onclick='return confirm(\"Are you sure you want to delete this booking? Booking ID = " . $row['id'] . "\")'>
                        </form></td>";

                        if ($row['status'] == 1) {
                            echo "<td>
                            <form method='POST' style='display:inline-block;'>
                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                <input type='hidden' name='status' value='0'>
                                <input type='submit' class='btn btn-cancel' name='cancel' value='Cancel' onclick='return confirm(\"Are you sure you want to cancel this booking? Booking ID = " . $row['id'] . "\")'>
                            </form>
                            <button type='button' class='btn btn-primary' onclick='editBooking(" . json_encode($row) . ")'>Update</button>
                        </td>";
                        } else {
                            echo "<td></td>"; // Empty cell when status is not 1 (Canceled)
                        }

                    echo "</tr>";
                        
                    }
                } else {
                    echo "<tr><td colspan='8' class='empty-book'>No bookings found.</td></tr>";
                }
                ?>
            </table>

            <button type="button" class="btn btn-success" onclick="openAddBookingModal()">Add New Booking</button>
        </div>

            <!-- add new booking modal -->
        <div id="addBookingModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Booking</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addBookingForm" method="POST">
                            <div class="form-group">
                                <label for="addUserId">User ID:</label>
                                <input type="number" class="form-control" name="users_id" id="addUserId" required>
                            </div>
                            <div class="form-group">
                                <label for="addVenueId">Venue ID:</label>
                                <input type="number" class="form-control" name="venue_id" id="addVenueId" required>
                            </div>
                            <div class="form-group">
                                <label for="addBookingDate">Booking Date:</label>
                                <input type="date" class="form-control" name="bookingDate" id="addBookingDate" required>
                            </div>
                            <div class="form-group">
                                <label for="addBookingStartTime">Start Time:</label>
                                <input type="time" class="form-control" name="bookingStartTime" id="addBookingStartTime" required>
                            </div>
                            <div class="form-group">
                                <label for="addBookingEndTime">End Time:</label>
                                <input type="time" class="form-control" name="bookingEndTime" id="addBookingEndTime" required>
                            </div>
                            <div class="form-group">
                                <label for="addBookingStatus">Status:</label>
                                <select class="form-control" name="status" id="addBookingStatus" required>
                                    <option value="1">Booked</option>
                                    <option value="0">Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" name="add" class="btn btn-success">Add Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Update Booking Modal -->
        <div id="updateBookingModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Booking</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateBookingForm" method="POST">
                    <input type="hidden" name="id" id="updateBookingId">
                    <div class="form-group">
                        <label for="updateBookingDate">Booking Date:</label>
                        <input type="date" class="form-control" name="bookingDate" id="updateBookingDate" required>
                    </div>
                    <div class="form-group">
                        <label for="updateBookingStartTime">Start Time:</label>
                        <input type="time" class="form-control" name="bookingStartTime" id="updateBookingStartTime" required>
                    </div>
                    <div class="form-group">
                        <label for="updateBookingEndTime">End Time:</label>
                        <input type="time" class="form-control" name="bookingEndTime" id="updateBookingEndTime" required>
                    </div>
                    <div class="form-group">
                        <label for="updateBookingStatus">Status:</label>
                        <select class="form-control" name="status" id="updateBookingStatus" required>
                        <option value="1">Booked</option>
                        <option value="0">Canceled Booking</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update Booking</button>
                    </form>
                </div>
                </div>
            </div>
        </div>

    <script>
        function editBooking(row) {
            // Open the modal
            $('#updateBookingModal').modal('show');

            // Populate the modal fields with the selected booking data
            document.getElementById('updateBookingId').value = row.id;
            document.getElementById('updateBookingDate').value = row.bookingDate;
            document.getElementById('updateBookingStartTime').value = row.bookingStartTime;
            document.getElementById('updateBookingEndTime').value = row.bookingEndTime;
            document.getElementById('updateBookingStatus').value = row.status;
        }

        function openAddBookingModal() {
        // Open the modal
        $('#addBookingModal').modal('show');
}
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>