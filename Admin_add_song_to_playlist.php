<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['playlistId']) && isset($_POST['songId'])) {
    $playlistId = $_POST['playlistId'];
    $songId = $_POST['songId'];

    // Check if the song already exists in the playlist
    $sqlCheck = "SELECT * FROM playlist_songs WHERE playlist_id = '$playlistId' AND song_id = '$songId'";
    $resultCheck = $conn->query($sqlCheck);

    if ($resultCheck && $resultCheck->num_rows > 0) {
        // Song already exists in the playlist, handle accordingly (e.g., show message)
        echo "This song is already in the playlist.";
    } else {
        // Add the song to playlist_songs
        $sqlAdd = "INSERT INTO playlist_songs (playlist_id, song_id) VALUES ('$playlistId', '$songId')";
        
        if ($conn->query($sqlAdd) === TRUE) {
            header("Location: Admin_manage_playlist.php?id=$playlistId");
            exit();
        } else {
            echo "Error: " . $sqlAdd . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
