<?php
session_start();
include 'db_connection.php';
include 'email_service.php'; // Include your email service

// Function to send email
function sendEmail($to, $subject, $message) {
    // Logic to send email (make sure to improve and customize for delivery)
    EmailService::send($to, $subject, $message);
}

// Handle withdrawal requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $withdrawalAmount = $_POST['amount'];
    $userId = $_SESSION['user_id'];
    $status = 'pending'; // Default status

    // Validate withdrawal amount and user
    if (/* validation logic */) {
        // Process withdrawal
        $query = "INSERT INTO withdrawals (user_id, amount, status) VALUES ('$userId', '$withdrawalAmount', '$status')";
        if (mysqli_query($conn, $query)) {
            $withdrawalId = mysqli_insert_id($conn);
            $message = "Your withdrawal request of $$withdrawalAmount is pending approval.";
            sendEmail($_SESSION['user_email'], "Withdrawal Request Submitted", $message);
            header("Location: withdrawals.php?status=submitted");
        } else {
            header("Location: withdrawals.php?status=error");
        }
    } else {
        header("Location: withdrawals.php?status=invalid");
    }
}

// Handle admin approvals/rejections
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    $withdrawalId = $_GET['id'];
    $action = $_GET['action'];
    
    if ($action == 'approve') {
        // Approve withdrawal
        // Logic for updating the status in the database
    } elseif ($action == 'reject') {
        // Reject withdrawal
        // Logic for updating the status in the database
    }
    
    // Send notification email on approval/rejection
    sendEmail($_SESSION['user_email'], "Withdrawal Request $action", "Your withdrawal request has been $action.");
    header("Location: withdrawals.php?status=completed");
}

// Fetch existing withdrawals for display
$result = mysqli_query($conn, "SELECT * FROM withdrawals WHERE user_id = '$userId'");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdrawals</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS for the UI -->
</head>
<body>
    <h1>Withdrawal Requests</h1>
    <form method="POST" action="withdrawals.php">
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" required>
        <input type="submit" value="Request Withdrawal">
    </form>

    <table>
        <tr>
            <th>Amount</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['status'] == 'pending'): ?>
                    <a href="?action=approve&id=<?php echo $row['id']; ?>">Approve</a>
                    <a href="?action=reject&id=<?php echo $row['id']; ?>">Reject</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>