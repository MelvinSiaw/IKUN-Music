<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

// Initialize variable to store playlists data
$playlists = [];

// Fetch playlist data
$sql = "SELECT playlist_id, playlist_name, created_at, playlist_image FROM playlist";
$result = $conn->query($sql);

// Check if query execution was successful
if ($result) {
    // Fetch all rows as associative array
    $playlists = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Query execution failed
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Playlist List</title>
    <link rel="stylesheet" href="Admin_list.css">
    <style>
    .playlist-image {
        max-width: 100%; 
        max-height: 45px; 
        width: auto; 
        height: auto; 
        display: block; 
        margin-top: 10px; 
        object-fit: contain; 
        align-items: center;
    }
    </style>
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
                <a href="index.php" class="logout">Logout</a> <!-- Replace with your logout page -->
            </div>   
        </aside>
        <main class="main-content">
            <header>
                <input type="text" name="search" placeholder="Artist, Album, Song, etc...">
            </header>
            <h1>Playlist List</h1>
            <button id="addNewBtn">Add New</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Playlist Name</th>
                        <th>Created At</th>
                        <th>Playlist Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="playlistList">
                    <?php foreach ($playlists as $playlist): ?>
                        <tr>
                            <td><?php echo $playlist['playlist_id']; ?></td>
                            <td><?php echo $playlist['playlist_name']; ?></td>
                            <td><?php echo $playlist['created_at']; ?></td>
                            <td>
                                <?php if (!empty($playlist['playlist_image'])): ?>
                                    <?php
                                    $image_path = $playlist['playlist_image'];
                                    ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Playlist Image" class="playlist-image">
                                <?php else: ?>
                                    No image available
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <button class="manage" onclick="managePlaylist(<?php echo $playlist['playlist_id']; ?>)">📂</button>
                                <button class="delete" onclick="deletePlaylist(<?php echo $playlist['playlist_id']; ?>)">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($playlist)): ?>
                        <tr><td colspan="6">No playlists found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script>
        function editPlaylist(id) {
            window.location.href = `Admin_edit_playlist.php?id=${id}`;
        }

        function deletePlaylist(id) {
            if (confirm('Are you sure you want to delete this playlist?')) {
                // Send AJAX request to delete playlist
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'Admin_delete_playlist.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Refresh the page after deletion
                        window.location.reload();
                    } else {
                        alert('Failed to delete playlist. Please try again.');
                    }
                };
                xhr.send('playlist_id=' + id);
            }
        }

        function managePlaylist(id) {
            window.location.href = `Admin_manage_playlist.php?id=${id}`;
        }

        document.getElementById('addNewBtn').addEventListener('click', function() {
            window.location.href = 'Admin_upload_playlist.php'; // Navigate to the upload playlist page
        });
    </script>
</body>
</html>
