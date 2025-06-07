<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $playlistId = $_POST['playlist_id'];
    
    // Delete from playlist_songs first to avoid foreign key constraint
    $sqlDeleteSongs = "DELETE FROM playlist_songs WHERE playlist_id = '$playlistId'";
    $conn->query($sqlDeleteSongs);
    
    $sql = "DELETE FROM playlist WHERE playlist_id = '$playlistId'";
    
    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>
