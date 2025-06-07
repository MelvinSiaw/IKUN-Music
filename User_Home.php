<?php
session_start();

$conn = require __DIR__ . "/db_connection.php"; // Ensure database connection is established

if (isset($_SESSION["user_id"])) {
    $sql = "SELECT name, profile_image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($name, $profile_image);
    $stmt->fetch();
    $stmt->close();
}

// Handle the image path
if (!empty($profile_image)) {
    if (strpos($profile_image, 'uploads/') === 0) {
        $image_path = $profile_image;
    } elseif (strpos($profile_image, '../uploads/') === 0) {
        $image_path = substr($profile_image, 3);
    } else {
        $image_path = 'uploads/profile/' . $profile_image;
    }
} else {
    $image_path = 'assets/pic/default.jpg';
}

// Fetch trending songs
$trending_sql = "SELECT songs.id, songs.song_title, artist.artist_name FROM songs JOIN artist ON songs.artist_id = artist.artist_id ORDER BY release_date DESC LIMIT 10";
$trending_result = $conn->query($trending_sql);

// Fetch artist details along with the number of songs
$albums_sql = "SELECT a.artist_id, a.artist_name, a.artist_photo, COUNT(s.id) AS song_count
               FROM artist a
               LEFT JOIN songs s ON a.artist_id = s.artist_id
               GROUP BY a.artist_id";
$albums_result = $conn->query($albums_sql);

// Fetch songs with artist names
$songs_sql = "SELECT s.id, s.song_title, s.profile_picture_upload, a.artist_name
              FROM songs s
              JOIN artist a ON s.artist_id = a.artist_id";
$songs_result = $conn->query($songs_sql);

// Fetch playlists
$playlists_sql = "SELECT p.playlist_id, p.playlist_name, p.playlist_image, COUNT(sp.song_id) AS song_count 
                  FROM playlist p 
                  LEFT JOIN playlist_songs sp ON p.playlist_id = sp.playlist_id
                  GROUP BY p.playlist_id";
$playlists_result = $conn->query($playlists_sql);

// Check if songs_result is valid before using fetch_assoc()
if ($songs_result) {
    // Fetch songs data
    while ($song = $songs_result->fetch_assoc()) {
        // Process each song
        // Example: echo htmlspecialchars($song['song_title']);
    }
} else {
    // Handle query error or no results
    echo "Error fetching songs: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ikun Music Dashboard</title>
    <link rel="stylesheet" href="assets/css/Home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        #logout {
            color: #ffffff;
            transition: color 0.3s;
        }
        
        #logout:hover {
            color: #ff0000;
        }

        #logout:hover .fas {
            color: #ff0000;
        }  
        
        .main-content {
            padding: 20px;
            width: calc(100%-90px);
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-left: 40px;
            margin-right: 20px;
        }

        .section-header {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #ffffff; 
        }

        .albums, .songs, .playlists {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .album, .song-card, .playlist {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            width: 200px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .album img, .song-card img, .playlist img {
            width: 150px;
            height: 150px;
            border-radius: 5%;
            margin-bottom: 5px;
        }

        .album span, .song span, .playlist span {
            display: block;
            margin-bottom: 10px;
        }

        .see-details, .listen, .view-playlist {
            text-decoration: none;
            color: #6200ea;
        }

        .song-card .listen {
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            padding: 5px 75px;
            border-radius: 5px;
        }

        .song-card .listen:hover {
            background-color: #0056b3;
        }

        /* New upload section */
        .upload-banner {
            display: flex;
            align-items: center;
            background: linear-gradient(to right top, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1);
            color: white;
            padding: 10px;
            padding-left: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 240px;
            margin-right: 130px;
            margin-top: 0px;
        }

        .upload-banner img {
            width: 100px; /* Adjust image size */
            height: 100px; /* Adjust image size */
            border-radius: 10px;
            margin-right: 20px;
        }

        .upload-banner-content {
            flex-grow: 1;
        }

        .upload-banner-content h2 {
            margin: 0;
            font-size: 1.5em; /* Adjust font size */
        }

        .upload-banner-content p {
            margin: 5px 0;
            font-size: 1em; /* Adjust font size */
        }

        .upload-banner-actions {
            display: flex;
            gap: 10px;
        }

        .upload-banner-actions button {
            background-color: #ffffff;
            color: #0072ff;
            border: none;
            padding: 10px 20px; /* Adjust padding */
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .upload-banner-actions button:hover {
            background-color: #0072ff;
            color: #ffffff;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            gap: 0px;
        }

        .content-section {
            overflow-x: auto;
            padding: 10px 0;
        }

        .scroll-container {
            display: flex;
            gap: 10px;
        }

        .album, .song-card, .playlist {
            flex: 0 0 auto;
            width: 200px; /* Adjust as needed */
        }

        .album img, .song-card img, .playlist img {
            width: 100%;
            height: 200px;
        }

        .section-header {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .action-link {
            display: inline-block;
            padding: 8px 60px;
            margin-top: 5px;
            font-size: 14px;
            color: #fff;
            background-color: #007BFF; 
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .action-link:hover {
            background-color: #0056b3;
        }

        /* Drawer styles */
        .drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background-color: #333;
            color: #fff;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .drawer-content ul {
            list-style: none;
            padding: 0;
        }

        .drawer-content li {
            margin-bottom: 10px;
        }

        .drawer-content li a {
            color: #fff;
            text-decoration: none;
        }

        .drawer-content li a:hover {
            text-decoration: underline;
        }

        .drawer-content h4 {
            color: #ff0000;
            text-align: center;
            margin-top: 20px;
        }

        .drawer.open {
            transform: translateX(0);
        }

        .drawer-header {
            background-color: #444;
            padding: 15px;
            font-size: 1.2em;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            position: relative;
        }

        #drawer-title {
            margin: 0;
        }

        #close-drawer-button {
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5em;
            cursor: pointer;
        }

        .drawer-content {
            padding: 15px;
            overflow-y: auto;
        }

        /* Button to toggle drawer */
        #drawer-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #6200ea;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1001;
            font-size: 1.2em;
            transition: background-color 0.3s;
        }

        #drawer-button:hover {
            background-color: #3700b3;
        }

        #drawer-button.hide {
            display: none;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-logo">
            <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo" class="navbar-image"><span>IKUN MUSIC</span>
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
    </div>
    <div class="main-content">
        <div class="header">
        <form action="search.php" method="GET">
            <input type="text" name="query" placeholder="Search for Songs ヾ(•ω•`)o" style="padding: 10px; width: 1280px; border: 1px solid #ccc; border-radius: 4px 0 0 4px;">
            <button type="submit" style="background-color: #007BFF; color: white; border: 1px solid #007BFF; border-radius: 0 4px 4px 0; padding: 10px 20px; cursor: pointer;">Search</button>
        </form>
        </div>
        <!-- New upload section -->
        <div class="upload-banner">
            <img src="assets/pic/ikun_background.png" alt="Upload Image">
            <div class="upload-banner-content">
                <h2>Upload Your First Track</h2>
                <p>Experience our Free Music Streaming Platform</p>
                <div class="upload-banner-actions">
                    <button onclick="window.location.href='UploadForm.php'" style="font-family: Poppins, sans-serif;">Upload Track</button>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
    <!-- Albums Section -->
        <div class="content-section">
            <div class="section-header">Recommended Artist</div>
            <div class="albums scroll-container">
                <?php while ($album = $albums_result->fetch_assoc()): ?>
                    <div class="album">
                        <img src="<?php echo htmlspecialchars($album['artist_photo']); ?>" alt="Artist Image">
                        <span><?php echo htmlspecialchars($album['artist_name']); ?></span>
                        <span><?php echo htmlspecialchars($album['song_count']) . ' Songs'; ?></span>
                        <a href="artist_home.php?artist_id=<?php echo urlencode($album['artist_id']); ?>" class="action-link">See Details</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <!-- Songs Section -->
        <div class="content-section">
            <div class="section-header">Recommended Songs</div>
            <div class="songs scroll-container">
                <?php
                // Assuming $songs_result is still valid here and has been reset if needed.
                $songs_result->data_seek(0); // Reset result set pointer if needed
                while ($song = $songs_result->fetch_assoc()): ?>
                    <div class="song-card">
                        <img src="<?php echo htmlspecialchars($song['profile_picture_upload']); ?>" alt="Song Image">
                        <div class="song">
                            <span><?php echo htmlspecialchars($song['song_title']); ?></span>
                            <span><?php echo htmlspecialchars($song['artist_name']); ?></span>
                            <a href="song_page.php?id=<?php echo urlencode($song['id']); ?>" class="action-link">Listen</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <!-- Playlists Section -->
        <div class="content-section">
            <div class="section-header">Recommended Playlists</div>
            <div class="playlists scroll-container">
                <?php while ($playlist = $playlists_result->fetch_assoc()): ?>
                    <div class="playlist">
                        <img src="<?php echo htmlspecialchars($playlist['playlist_image']); ?>" alt="Playlist Image">
                        <span><?php echo htmlspecialchars($playlist['playlist_name']); ?></span>
                        <span><?php echo htmlspecialchars($playlist['song_count']) . ' Songs'; ?></span>
                        <a href="single_playlist.php?id=<?php echo urlencode($playlist['playlist_id']); ?>" class="action-link">View Playlist</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Button to toggle drawer -->
    <button id="drawer-button" onclick="toggleDrawer()">☰ Trending</button>

    <!-- Drawer for Popular and Trending -->
    <div id="trending-drawer" class="drawer">
        <div class="drawer-header">
            <span id="drawer-title">Popular and Trending</span>
            <button id="close-drawer-button" onclick="toggleDrawer()">×</button>
        </div>
        <div class="drawer-content">
            <ul>
                <?php while ($trending = $trending_result->fetch_assoc()): ?>
                    <li><a href="song_page.php?id=<?php echo $trending['id']; ?>"><?php echo htmlspecialchars($trending['song_title']) . ' - ' . htmlspecialchars($trending['artist_name']); ?></a></li>
                    <hr>
                <?php endwhile; ?>
            </ul>
            <h4 id="ads" style="color: white;">Upload your production and become the next Trending! 🥳 </h4>
        </div>
    </div>

    <script>
        function toggleDrawer() {
            const drawer = document.getElementById('trending-drawer');
            const drawerButton = document.getElementById('drawer-button');
            const closeButton = document.getElementById('close-drawer-button');

            if (drawer.classList.contains('open')) {
                drawer.classList.remove('open');
                drawerButton.classList.remove('hide');
            } else {
                drawer.classList.add('open');
                drawerButton.classList.add('hide');
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>