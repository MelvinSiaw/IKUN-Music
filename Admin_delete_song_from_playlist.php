<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playlistId']) && isset($_POST['songId'])) {
    $playlistId = $_POST['playlistId'];
    $songId = $_POST['songId'];
    
    // Delete the song from playlist_songs
    $sql = "DELETE FROM playlist_songs WHERE playlist_id = '$playlistId' AND song_id = '$songId'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: Admin_manage_playlist.php?id=$playlistId");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
