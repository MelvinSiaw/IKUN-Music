<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

$playlistId = $_GET['id'];

// Fetch playlist data
$sqlPlaylist = "SELECT * FROM playlist WHERE playlist_id = '$playlistId'";
$resultPlaylist = $conn->query($sqlPlaylist);
$playlist = $resultPlaylist->fetch_assoc();

// Fetch songs data
$sqlSongs = "SELECT id, song_title FROM songs";
$resultSongs = $conn->query($sqlSongs);
$songs = $resultSongs->fetch_all(MYSQLI_ASSOC);

// Fetch songs in the playlist
$sqlPlaylistSongs = "SELECT songs.id, songs.song_title 
                     FROM songs 
                     JOIN playlist_songs ON songs.id = playlist_songs.song_id 
                     WHERE playlist_songs.playlist_id = '$playlistId'";
$resultPlaylistSongs = $conn->query($sqlPlaylistSongs);
$playlistSongs = $resultPlaylistSongs->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Playlist</title>
    <link rel="stylesheet" href="Admin_list.css">
</head>
<body>
    <div class="container">
    <aside class="sidebar">
            <div class="navbar">
                <div class="navbar-logo">
                    <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo" class="navbar-image">
                    <span>IKUN MUSIC</span>
                </div>
                <div class="navbar-links-container">
                    <a href="Admin_dashboard.php" class="navbar-link">Dashboard</a>
                    <a href="Admin_playlist_list.php" class="navbar-link">Playlist List</a>
                    <a href="Admin_song_list.php" class="navbar-link">Song List</a>
                    <a href="Admin_edit_comment.php" class="navbar-link">Comment List</a>
                    <a href="Admin_artist_list.php" class="navbar-link">Artist List</a>
                    <a href="Admin_user_list.php" class="navbar-link">Users List</a>
                </div>
                <a href="index.php" class="logout">Logout</a>
            </div>
        </aside>
        <main class="main-content">
        <h1>Manage Playlist: <?php echo htmlspecialchars($playlist['playlist_name']); ?></h1>
        <h2>Songs in Playlist</h2>
        <ul>
        <?php foreach ($playlistSongs as $song): ?>
        <li>
            <?php echo htmlspecialchars($song['song_title']); ?>
            <form method="POST" action="Admin_delete_song_from_playlist.php" style="display: inline;">
                <input type="hidden" name="playlistId" value="<?php echo htmlspecialchars($playlistId); ?>">
                <input type="hidden" name="songId" value="<?php echo htmlspecialchars($song['id']); ?>">
                <button type="submit" onclick="return confirm('are you sure you want to remove this song from your playlist?')">Delete</button>
            </form>
        </li>
        <?php endforeach; ?>
        <?php if (empty($playlistSongs)): ?>
        <li>No songs in this playlist.</li>
        <?php endif; ?>
        </ul>

        <h2>Add Song to Playlist</h2>
        <form method="POST" action="Admin_add_song_to_playlist.php">
            <input type="hidden" name="playlistId" value="<?php echo htmlspecialchars($playlistId); ?>">
            <label for="songId">Select Song:</label>
            <select id="songId" name="songId" required>
                <?php foreach ($songs as $song): ?>
                    <option value="<?php echo htmlspecialchars($song['id']); ?>"><?php echo htmlspecialchars($song['song_title']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Add Song</button>
        </form>
        </main>
    </div>
</body>
</html>
