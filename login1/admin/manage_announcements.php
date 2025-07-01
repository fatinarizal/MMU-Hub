<?php
session_start();
include "../connection.php";

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


$message = "";

// Handle adding a new announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    // $adminId = $_SESSION["user_id"];
    // Set the target directory for uploads
    $target_dir = "../uploads/";

    // Maximum file size (example: 2MB)
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Initialize file variables to null
    $target_file_image = null;
    $target_file_file = null;

    // Upload image if it exists
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_file_image = $target_dir . uniqid() . '_' . basename($image);
        $imageFileType = strtolower(pathinfo($target_file_image, PATHINFO_EXTENSION));
        $allowed_image_types = ['jpg', 'png', 'jpeg', 'gif'];
        if (in_array($imageFileType, $allowed_image_types) && $_FILES['image']['size'] <= $maxFileSize) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file_image)) {
                $message = "Error uploading the image file.";
            }
        } else {
            $message = "Invalid image file type or size exceeds 2MB.";
        }
    }

    // Upload file if it exists
    if (!empty($_FILES['file']['name'])) {
        $file = $_FILES['file']['name'];
        $target_file_file = $target_dir . uniqid() . '_' . basename($file);
        $fileType = strtolower(pathinfo($target_file_file, PATHINFO_EXTENSION));
        $allowed_file_types = ['pdf', 'docx', 'doc'];
        if (in_array($fileType, $allowed_file_types) && $_FILES['file']['size'] <= $maxFileSize) {
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file_file)) {
                $message = "Error uploading the document file.";
            }
        } else {
            $message = "Invalid document file type or size exceeds 2MB.";
        }
    }

    // Insert announcement into the database if no file validation errors occurred
   // Insert announcement into the database if no file validation errors occurred
    if (!$message) {
    // Prepare and execute the insert query
    $query = "INSERT INTO announcements (title, content, image, file) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    // Check for null values in file uploads
    $target_file_image = $target_file_image ?: null; // Use null if no image uploaded
    $target_file_file = $target_file_file ?: null;   // Use null if no file uploaded

    $stmt->bind_param('ssss', $title, $content, $target_file_image, $target_file_file);

    if ($stmt->execute()) {
        $message = "New announcement added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

}

// Handle deleting an announcement
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $delete_query = "DELETE FROM announcements WHERE announcementID=?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param('i', $id);
    if ($delete_stmt->execute()) {
        $message = "Announcement deleted successfully.";
    } else {
        $message = "Error deleting announcement.";
    }
}

// Fetch all announcements
$result = mysqli_query($conn, "SELECT * FROM announcements");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Announcements</title>
    <link rel="stylesheet" href="../css/manage_announcements.css">
</head>
<body>
    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
        <ul>
            <li><a href="admindashboard.php" >Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php">Manage FAQ</a></li>
            <li><a href="manage_announcements.php" class="active">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
    </div>
    <div class = "main-content">
        <header>
            <h1>Manage Announcement</h1>
            <nav>
                <a href="userdashboard.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>
        <div class="container">
            <main>
                <?php if ($message) echo "<p class='message'>$message</p>"; ?>
                
                <h2>Add New Announcement</h2>
                <form action="" method="POST" enctype="multipart/form-data">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" placeholder="Enter title" required>

                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="5" placeholder="Enter announcement content" required></textarea>

                    <label for="image">Image (optional):</label>
                    <input type="file" id="image" name="image">

                    <label for="file">File (optional):</label>
                    <input type="file" id="file" name="file">

                    <button type="submit">Add Announcement</button>
                </form>

                <h2>Current Announcements</h2>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="current-announcement">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                        <?php if ($row['image']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars(basename($row['image'])); ?>" alt="Announcement Image" width="100">
                        <?php endif; ?>
                        <?php if ($row['file']): ?>
                            <a href="../uploads/<?php echo htmlspecialchars(basename($row['file'])); ?>" download>Download File</a>
                        <?php endif; ?>
                        <a href="?delete_id=<?php echo $row['announcementID']; ?>"><button class="delete-button">Delete</button></a>
                    </div>
                <?php endwhile; ?>
            </main>
        </div>
    </div>
</body>
</html>
