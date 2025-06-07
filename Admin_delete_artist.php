<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['artist_id'])) {
    // Include database connection
    $conn = require __DIR__ . "/db_connection.php";
    
    // Sanitize the input to prevent SQL injection
    $artist_id = $conn->real_escape_string($_POST['artist_id']);

    // Prepare delete statement
    $sql = "DELETE FROM artist WHERE artist_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $artist_id);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Deletion successful
        echo 'Artist deleted successfully.';
    } else {
        // Deletion failed
        echo 'Error deleting artist: ' . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request
    echo 'Invalid request.';
}
?>
