<?php
session_start();

include "../connection.php";

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login if not admin
    exit();
}



// Delete feedback 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $delete_query = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $delete_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Feedback deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Error deleting feedback: " . $stmt->error;
    }

    $stmt->close();
    header("Location: handle_feedback.php");
    exit();
}


// Fetch all feedback
$query = "SELECT feedback.id, feedback.feedback, feedback.createdTime, users.name 
          FROM feedback 
          JOIN users ON feedback.users_id = users.id 
          ORDER BY feedback.createdTime DESC";
$result = $conn->query($query);

//message
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']); // Clear messages after displaying

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/handle_feedback.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">        
</head>

    <body>
        <div class="sidebar">
            <div class="user-info">
                <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p> <!-- Escaping output for security -->
            </div>
            <ul>
            <li><a href="admindashboard.php" >Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php" class="active">Handle Feedback</a></li>
        </ul>
            <br>
        </div>

        <div class="main-content">
            <header>
                <h1>Manage Feedback</h1>
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

            <div class="feedback">
                <?php if ($result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Feedback</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['feedback']; ?></td>
                                    <td><?php echo $row['createdTime']; ?></td>
                                    <td>
                                        <form method="POST" action="handle_feedback.php" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <?php echo "<tr><td colspan='8' class='empty-feedback'>No feedbacks found.</td></tr>"; ?>
                <?php endif; ?>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    </body>
</html>