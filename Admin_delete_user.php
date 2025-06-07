<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    // Include database connection
    $conn = require __DIR__ . "/db_connection.php";
    
    // Sanitize the input to prevent SQL injection
    $user_id = $conn->real_escape_string($_POST['user_id']);

    // Prepare delete statement
    $sql = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo 'User deleted successfully.';
    } else {
        // Deletion failed
        echo 'Error deleting user: ' . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request
    echo 'Invalid request.';
}
?>
