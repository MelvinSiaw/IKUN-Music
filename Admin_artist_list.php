<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

// Initialize variable to store artist data
$artists = [];

// Fetch artist data
$sql = "SELECT artist_id, artist_name, artist_email, artist_youtube, artist_photo FROM artist";
$result = $conn->query($sql);

// Check if query execution was successful
if ($result) {
    // Fetch all rows as associative array
    $artists = $result->fetch_all(MYSQLI_ASSOC);
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
    <title>Admin - Artist List</title>
    <link rel="stylesheet" href="Admin_list.css">
    <style>
    .profile-image {
        max-width: 100%; 
        max-height: 45px; 
        width: auto; 
        height: auto; 
        display: block; 
        margin-top: 10px; 
        object-fit: contain; 
        align-items: center;
    }

    .youtube-link {
            color: blue;
            text-decoration: none;
        }

        .youtube-link:hover {
            color: darkblue;
            text-decoration: underline;
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
            <h1>Artist List</h1>
            <button id="addNewBtn">Add New</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Artist Name</th>
                        <th>Email</th>
                        <th>YouTube</th>
                        <th>Photo</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="artistList">
                    <?php foreach ($artists as $artist): ?>
                        <tr>
                            <td><?php echo $artist['artist_id']; ?></td>
                            <td><?php echo $artist['artist_name']; ?></td>
                            <td><?php echo $artist['artist_email']; ?></td>
                            <td>
                                <?php if (!empty($artist['artist_youtube'])): ?>
                                    <a href="<?php echo $artist['artist_youtube']; ?>" target="_blank" class="youtube-link">YouTube Link</a>
                                <?php else: ?>
                                    No link available
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($artist['artist_photo'])): ?>
                                    <img src="<?php echo $artist['artist_photo']; ?>" alt="Artist Photo" class="profile-image">
                                <?php else: ?>
                                    No image available
                                <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <button class="edit" onclick="editArtist(<?php echo $artist['artist_id']; ?>)">✏️</button>
                                <button class="delete" onclick="deleteArtist(<?php echo $artist['artist_id']; ?>)">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($artists)): ?>
                        <tr><td colspan="6">No artists found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script>
        function editArtist(id) {
            window.location.href = `Admin_edit_artist.php?id=${id}`;
        }

        function deleteArtist(id) {
            if (confirm('Are you sure you want to delete this artist?')) {
                // Send AJAX request to delete artist
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'Admin_delete_artist.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Refresh the page after deletion
                        window.location.reload();
                    } else {
                        alert('Failed to delete artist. Please try again.');
                    }
                };
                xhr.send('artist_id=' + id);
            }
        }

        document.getElementById('addNewBtn').addEventListener('click', function() {
            window.location.href = 'Admin_upload_artist.php'; // Navigate to the upload artist page
        });
    </script>
</body>
</html>
