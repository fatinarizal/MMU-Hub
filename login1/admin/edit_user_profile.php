<?php
session_start();
include "../connection.php"; // Include the database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle profile update form submission for any user
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = "All fields are required.";
    } else {

        // Update the user information in the database
        $update_sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ssssi', $name, $email, $phone, $password, $user_id);

        if ($update_stmt->execute()) {
            $_SESSION['success'] = "User profile updated successfully!";
        } else {
            $error = "Error updating profile. Please try again.";
        }
    }
}

// Fetch all users from the database
$all_users_sql = "SELECT * FROM users";
$all_users_result = $conn->query($all_users_sql);

// Fetch selected user for editing
$selected_user = null;
if (isset($_GET['edit_user_id'])) {
    $edit_user_id = $_GET['edit_user_id'];
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param('i', $edit_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_user = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
    <link rel="stylesheet" href="../css/edit_user_profile.css"> <!-- Updated the CSS path -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
        <ul>
            <li><a href="admindashboard.php">Dashboard</a></li>
            <li><a href="edit_user_profile.php" class="active">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <header>
            <h1>Edit User Profile</h1>
            <nav>
                <a href="userdashboard.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>
        <br>
        
        <div class="container">
            <div class="profile-box">
                <!-- Show success or error messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
                <?php elseif (isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <!-- Edit selected user form -->
                <?php if ($selected_user): ?>
                    <form method="POST" action="edit_user_profile.php">
                        <input type="hidden" name="user_id" value="<?php echo $selected_user['id']; ?>">

                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($selected_user['name']); ?>" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selected_user['email']); ?>" required>

                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($selected_user['phone']); ?>" required>

                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($selected_user['phone']); ?>" required>

                        <button type="submit" name="update_user">Update</button>
                    </form>
                <?php else: ?>
                    <p>Select a user to edit from the table below.</p>
                <?php endif; ?>
            </div>

            <div class="all-users">
                <h2>All Users</h2>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Password</th>
                        <th>Action</th>
                    </tr>
                    <?php while ($row = $all_users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['password']); ?></td>
                            <td>
                            <form method="GET" action="edit_user_profile.php" style="display:inline;">
                                <input type="hidden" name="edit_user_id" value="<?php echo $row['id']; ?>">
                                <button type="submit">Edit</button>
                            </form> 
                                <form method="POST" action="delete_user.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
