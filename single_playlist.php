<?php
session_start();
include 'db_connection.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulate user ID (replace with actual session/user ID)
$userID = $_SESSION["user_id"] ?? 1; // Ensure this is dynamically set based on session or user context

// Get playlist ID from query string
$playlistID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($playlistID === 0) {
    die('Invalid playlist ID');
}

// Initialize playlist variable
$playlist = null;

// Fetch playlist details from the `playlists` table
$playlistQuery = $conn->prepare("SELECT * FROM playlists WHERE playlist_id = ?");
if (!$playlistQuery) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$playlistQuery->bind_param("i", $playlistID);
$playlistQuery->execute();
$playlistResult = $playlistQuery->get_result();

if ($playlistResult->num_rows > 0) {
    $playlist = $playlistResult->fetch_assoc();
} else {
    // If not found in `playlists`, try the `playlist` table
    $playlistQuery = $conn->prepare("SELECT * FROM playlist WHERE playlist_id = ?");
    if (!$playlistQuery) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $playlistQuery->bind_param("i", $playlistID);
    $playlistQuery->execute();
    $playlistResult = $playlistQuery->get_result();

    if ($playlistResult->num_rows > 0) {
        $playlist = $playlistResult->fetch_assoc();
    }
}

$playlistQuery->close();

// Check if playlist is found
if (!$playlist) {
    die('Playlist not found');
}

// Determine if this is the Liked Songs playlist
$isLikedSongs = ($playlist['playlist_name'] === 'Liked Songs');

// Fetch songs in the playlist
if ($isLikedSongs) {
    $songsQuery = $conn->prepare("
        SELECT songs.id, songs.song_title, artist.artist_name
        FROM liked_songs 
        JOIN songs ON liked_songs.song_id = songs.id 
        JOIN artist ON songs.artist_id = artist.artist_id
        WHERE liked_songs.user_id = ?
    ");
    if (!$songsQuery) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $songsQuery->bind_param("i", $userID);
} else {
    $songsQuery = $conn->prepare("
        SELECT songs.id, songs.song_title, artist.artist_name
        FROM playlist_songs 
        JOIN songs ON playlist_songs.song_id = songs.id 
        JOIN artist ON songs.artist_id = artist.artist_id
        WHERE playlist_songs.playlist_id = ?
    ");
    if (!$songsQuery) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $songsQuery->bind_param("i", $playlistID);
}

$songsQuery->execute();
$songs = $songsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
$songsQuery->close();

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($playlist['playlist_name']); ?></title>
    <link rel="stylesheet" href="user_playlist.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            background-image: url('assets/pic/background.png');
        }

        .playlist-container {
            width: 80%;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            outline: none;
        }

        .playlist-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .playlist-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .playlist-details {
            flex-grow: 1;
        }

        .playlist-details h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600; /* Poppins weight 600 for bold text */
        }

        .playlist-details p {
            margin: 5px 0 0;
            color: #666;
        }

        .songs-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .song-item {
            display: flex;
            align-items: flex-start; /* Align items at the start */
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .song-item a {
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column; /* Stack title and artist vertically */
            width: 100%;
        }

        .song-title {
            font-size: 18px;
            font-weight: 600; /* Poppins weight 600 for bold text */
            margin-bottom: 5px; /* Space between title and artist */
        }

        .song-artist {
            color: #666;
        }

        .song-item a:hover .song-title {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="playlist-container">
        <button class="close-button" onclick="goBack()">&times;</button>
        <div class="playlist-header">
            <img src="<?php echo htmlspecialchars($playlist['playlist_image']) ?: 'assets/pic/default.png'; ?>" alt="Playlist Image" class="playlist-image">
            <div class="playlist-details">
                <h1><?php echo htmlspecialchars($playlist['playlist_name']); ?></h1>
                <p>Created on: <?php echo htmlspecialchars($playlist['created_at']); ?>, Total songs: <?php echo count($songs); ?></p>
            </div>
        </div>
        <ul class="songs-list">
            <?php if ($songs): ?>
                <?php foreach ($songs as $song): ?>
                    <li class="song-item">
                        <a href="song_page.php?id=<?php echo urlencode($song['id']); ?>">
                            <span class="song-title"><?php echo htmlspecialchars($song['song_title']); ?></span>
                            <span class="song-artist"><?php echo htmlspecialchars($song['artist_name']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No songs found in this playlist.</p>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>