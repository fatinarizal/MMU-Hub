<?php
session_start();
include "../connection.php";

// Check if the user is logged in and is an admin
if (!isset($_SESSION["username"]) || $_SESSION["role"] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle FAQ answering
if (isset($_POST["answer_faq"])) {
    $faq_id = $_POST["faq_id"];
    $answer = $_POST["answer"];
    $answered_by = $_SESSION["user_id"];

    if (!empty($answer)) {
        // Prepare the SQL statement to update the FAQ with the answer
        $stmt = $conn->prepare("UPDATE faqs SET answer = ?, answered_by = ?, answered_at = NOW(), status = 'answered' WHERE id = ?");
        $stmt->bind_param("sii", $answer, $answered_by, $faq_id);

        if ($stmt->execute()) {
            echo "<p>FAQ answered successfully.</p>";
        } else {
            echo "<p>Failed to answer FAQ: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p>Please enter an answer.</p>";
    }
}

// Fetch all FAQs with user details (submitted_by and answered_by if available)
$sql = "SELECT f.*, u.username AS submitted_by_name, a.username AS answered_by_name
        FROM faqs f
        JOIN users u ON f.submitted_by = u.id
        LEFT JOIN users a ON f.answered_by = a.id
        ORDER BY f.submitted_at DESC";
$faqs_res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage FAQ</title>
    <link rel="stylesheet" href="../css/manageFAQ.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

    <!-- Sidebar Section -->
    <div class="sidebar">
        <div class="user-info">
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
        <ul>
            <li><a href="admindashboard.php">Dashboard</a></li>
            <li><a href="edit_user_profile.php">Edit User Profile</a></li>
            <li><a href="manageFAQ.php" class="active">Manage FAQ</a></li>
            <li><a href="manage_announcements.php">Manage Announcement</a></li>
            <li><a href="manage_booking.php">Manage Booking</a></li>
            <li><a href="editvenue.php">Edit Venue</a></li>
            <li><a href="handle_feedback.php">Handle Feedback</a></li>
        </ul>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <header>
            <h1>Manage FAQs</h1>
            <nav>
                <a href="admindashboard.php">Home</a> | <a href="../logout.php">Log Out</a>
            </nav>
        </header>

        <div class="faq-container">
            <table class="faq-table">
                <thead>
                    <tr>
                        <th>Num.</th>
                        <th>Sender Name</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Date Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($faqs_res) > 0) {
                            $num = 1;
                            while ($faq = mysqli_fetch_assoc($faqs_res)) {
                                echo "<tr>";
                                echo "<td>" . $num++ . "</td>";
                                echo "<td>" . htmlspecialchars($faq['submitted_by_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($faq['question']) . "</td>";
                                echo "<td>";
                                
                                // Check if the FAQ has been answered
                                if ($faq['status'] == 'answered') {
                                    echo htmlspecialchars($faq['answer']);
                                } else {
                                    echo "<form method='POST' action=''>
                                            <textarea name='answer' required></textarea>
                                            <input type='hidden' name='faq_id' value='" . htmlspecialchars($faq['id']) . "'>
                                            <button type='submit' name='answer_faq'>Submit Answer</button>
                                        </form>";
                                }
                                echo "</td>";
                                echo "<td>" . htmlspecialchars($faq['submitted_at']) . "</td>";
                                echo "<td>";

                                // Display 'Pending' button only if the FAQ is pending
                                if ($faq['status'] == 'pending') {
                                    echo "<form method='POST' action=''>
                                            <input type='hidden' name='faq_id' value='" . htmlspecialchars($faq['id']) . "'>Pending</form>";
                                } else {
                                    echo "Answered";
                                }

                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No FAQs found.</td></tr>";
                        }
                    ?>
                    </tbody>

            </table>
        </div>

        <div class="back-dashboard">
            <a href="admindashboard.php"><button class="btn">Back to Dashboard</button></a>
        </div>
    </div>
</body>
</html>
