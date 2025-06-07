<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $conn = require __DIR__ . "/db_connection.php";
    
    $userID = $_SESSION["user_id"]; // Make sure $userID is defined

    // Fetch user information
    $sql = "SELECT name, profile_image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($name, $profile_image);
    $stmt->fetch();
    $stmt->close();

    // Handle the image path
    if (!empty($profile_image)) {
        if (strpos($profile_image, 'uploads/') === 0) {
            $image_path = $profile_image;
        } elseif (strpos($profile_image, '../uploads/') === 0) {
            $image_path = substr($profile_image, 3); // Remove the '../' prefix
        } else {
            $image_path = 'uploads/profile/' . $profile_image;
        }
    } else {
        $image_path = 'assets/pic/default.jpg';
    }

    // Fetch user playlists
    $playlistsQuery = $conn->prepare("
        SELECT * 
        FROM playlists 
        WHERE user_id = ?
    ");
    $playlistsQuery->bind_param("i", $userID);
    $playlistsQuery->execute();
    $playlists = $playlistsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
    $playlistsQuery->close();

    // Fetch liked songs playlist ID
    $likedSongsPlaylistID = null;
    $likedSongsQuery = $conn->prepare("
        SELECT playlist_id 
        FROM playlists 
        WHERE playlist_name = 'Liked Songs' AND user_id = ?
    ");
    $likedSongsQuery->bind_param("i", $userID);
    $likedSongsQuery->execute();
    $likedSongsResult = $likedSongsQuery->get_result();
    if ($likedSongs = $likedSongsResult->fetch_assoc()) {
        $likedSongsPlaylistID = $likedSongs['playlist_id'];
    }
    $likedSongsQuery->close();

    // If "Liked Songs" playlist does not exist, create it with the default image
    if (!$likedSongsPlaylistID) {
        $imagePath = 'assets/pic/love-song.png';
        $uploadedImagePath = uploadImage($imagePath); // Upload the image to the server
        $insertPlaylistQuery = $conn->prepare("
            INSERT INTO playlists (playlist_name, user_id, created_at, playlist_image) 
            VALUES ('Liked Songs', ?, NOW(), ?)
        ");
        $insertPlaylistQuery->bind_param("is", $userID, $uploadedImagePath);
        $insertPlaylistQuery->execute();
        $likedSongsPlaylistID = $insertPlaylistQuery->insert_id; // Get the new playlist ID
        $insertPlaylistQuery->close();
    }
    
    // Fetch liked songs
    $likedSongs = [];
    if ($likedSongsPlaylistID) {
        $songsQuery = $conn->prepare("
            SELECT songs.* 
            FROM liked_songs 
            JOIN songs ON liked_songs.song_id = songs.id 
            WHERE liked_songs.user_id = ?
        ");
        $songsQuery->bind_param("i", $userID);
        $songsQuery->execute();
        $likedSongs = $songsQuery->get_result()->fetch_all(MYSQLI_ASSOC);
        $songsQuery->close();
    }

    mysqli_close($conn);
} 
else {
    // Redirect or handle case where user is not logged in
    header("Location: login.php");
    exit();
}

// Function to upload the image to the server
function uploadImage($imagePath) {
    $targetDir = 'uploads/playlist_images/'; // Change this to your desired upload directory
    $imageName = basename($imagePath);
    $uploadedImagePath = $targetDir . $imageName;

    // Ensure the target directory exists
    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die('Failed to create directories...');
        }
    }

    // Check if the source file exists
    if (!file_exists($imagePath)) {
        die('Source file does not exist: ' . $imagePath);
    }

    // Copy the file to the target directory
    if (!copy($imagePath, $uploadedImagePath)) {
        die('Failed to upload file...');
    }

    return $uploadedImagePath;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Playlists</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Poppins, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            background-image: url('assets/pic/background.png');
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #1f1f2e;
            color: #ffffff;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .navbar-logo {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
        }

        .navbar-logo img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
        }

        .navbar-logo span {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar-links-container {
            display: flex;
            flex-direction: column;
        }

        .navbar-link {
            color: #ffffff;
            text-decoration: none;
            font-size: 18px;
            padding: 15px 20px;
            transition: background-color 0.3s;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #33334d;
            border-radius: 10px;
            margin-top: auto;
        }

        .navbar-user img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            border-radius: 50%;
        }

        .profile-link {
            color: #ffffff;
        }

        .main-content {
            padding: 100px;
            width: 100%;
            background-image: url(assets/pic/background.png);
        }

        .container {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }

        h1 {
            color: black;
        }

        .playlist-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .playlist-item {
            background-color: #282828;
            border-radius: 8px;
            width: 150px;
            text-align: center;
            padding: 10px;
            transition: transform 0.2s;
        }

        .playlist-item:hover {
            transform: scale(1.05);
        }

        .playlist-item img {
            width: 100%;
            border-radius: 4px;
        }

        .playlist-item a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            display: block;
            margin-top: 5px;
        }

        .playlist-item span {
            color: #b3b3b3;
            font-size: 0.9em;
        }

        #logout {
            color: #ffffff;
            transition: color 0.3s;
        }

        #logout:hover {
            color: #ff0000;
        }

        #logout:hover .fas {
            color: #ff0000; /* Red color for the icon on hover */
        }
        
        .navbar-link:hover {
            color: #7700ff;
        }
        .navbar-link:hover i {
            color: #7700ff;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="navbar-logo">
            <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo">
            <span>IKUN MUSIC</span>
        </div>
        <div class="navbar-links-container">
        <a href="User_Home.php" class="navbar-link"><i class="fas fa-home"></i> Home</a>
            <a href="user_playlist.php" class="navbar-link"><i class="fas fa-music"></i> My Playlist</a>
            <a href="Help_and_Support.html" class="navbar-link"><i class="fas fa-question-circle"></i> Help & Support</a>
            <a href="About_Us.html" class="navbar-link"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="UploadForm.php" class="navbar-link"><i class="fas fa-space-shuttle"></i> Ikun Space</a>
            <a href="logout.php" class="navbar-link" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="navbar-user">
            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="User Image">
            <span><a href="User_Profile.php" class="profile-link"><?php echo htmlspecialchars($name); ?></a></span>
        </div>
    </aside>
    <main class="main-content">
        <div class="container">
            <h1>My Playlists</h1>
            <ul class="playlist-list">
                <?php if ($playlists): ?>
                    <?php foreach ($playlists as $playlist): ?>
                        <li class="playlist-item">
                            <img src="<?php echo htmlspecialchars($playlist['playlist_image']) ?: 'assets/pic/default_cover.jpg'; ?>" alt="<?php echo htmlspecialchars($playlist['playlist_name']); ?>">
                            <a href="single_playlist_liked_song.php?playlist_id=<?php echo htmlspecialchars($playlist['playlist_id']); ?>">
                                <?php echo htmlspecialchars($playlist['playlist_name']); ?>
                            </a>
                            <span>By <?php echo htmlspecialchars($name); ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </main>
</body>
</html>