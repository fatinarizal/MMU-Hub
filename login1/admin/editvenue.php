<?php
session_start();
include "../connection.php";

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}

// Fetch all venues from the database
$sql = "SELECT * FROM venue";
$result = $conn->query($sql);

// Fetch success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']); // Clear messages after displaying

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venue Management</title>
    <link rel="stylesheet" href="../css/editvenue.css"> <!-- Optional external CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap for styling -->
</head>
<body>

    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> 
        </div>
        <ul>
            <li><a href="admindashboard.php">Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php" class="active">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Venue List</h1>
            <nav>
                <a href="userdashboard.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>
        <div class="container">
        <!-- Success Message -->
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Table for displaying venue list -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($venue = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($venue['id']); ?></td>
                            <td><?php echo htmlspecialchars($venue['name']); ?></td>
                            <td><?php echo htmlspecialchars($venue['location']); ?></td>
                            <td>
                                <a href="deletevenue.php?id=<?php echo $venue['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this venue?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No venues found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Button to add a new venue -->
        <a href="addvenue.php" class="btn-add-venue">Add New Venue</a>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
