<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Include database connection
    $conn = require __DIR__ . "/db_connection.php";
    
    // Sanitize the input to prevent SQL injection
    $id = $conn->real_escape_string($_POST['id']);

    // Prepare delete statement
    $sql = "DELETE FROM songs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo 'Song deleted successfully.';
    } else {
        // Deletion failed
        echo 'Error deleting song: ' . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request
    echo 'Invalid request.';
}
?>
