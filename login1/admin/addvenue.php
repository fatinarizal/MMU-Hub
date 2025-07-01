<?php
session_start();
include "../connection.php";

// Ensure user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];

    // Insert new venue into the database
    $sql = "INSERT INTO venue (name, location) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $location);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Venue added successfully.";
        header("Location: editvenue.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to add venue.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Venue</title>
    <link rel="stylesheet" href="../css/editvenue.css"> <!-- Optional CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="main-content">
        <h1>Add New Venue</h1>
        <form action="addvenue.php" method="POST">
            <div class="form-group">
                <label for="name">Venue Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="location">Venue Location:</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Add Venue</button>
        </form>
        <a href="editvenue.php" class="btn btn-primary">Back to Venue List</a>
    </div>
</body>
</html>
