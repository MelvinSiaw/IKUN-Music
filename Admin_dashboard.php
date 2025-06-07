<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

// Initialize variables to store counts
$songCount = 0;
$artistCount = 0;
$playlistCount = 0;
$commentCount = 0;
$userCount = 0;

// Fetch the count of songs
$songResult = $conn->query("SELECT COUNT(*) as count FROM songs");
if ($songResult) {
    $songCount = $songResult->fetch_assoc()['count'];
}

// Fetch the count of artists
$artistResult = $conn->query("SELECT COUNT(*) as count FROM artist");
if ($artistResult) {
    $artistCount = $artistResult->fetch_assoc()['count'];
}

// Fetch the count of playlists
$playlistResult = $conn->query("SELECT COUNT(*) as count FROM playlist"); // Adjust the table name if necessary
if ($playlistResult) {
    $playlistCount = $playlistResult->fetch_assoc()['count'];
}

// Fetch the count of comments
$commentResult = $conn->query("SELECT COUNT(*) as count FROM comments"); // Adjust the table name if necessary
if ($commentResult) {
    $commentCount = $commentResult->fetch_assoc()['count'];
}

// Fetch the count of users
$userResult = $conn->query("SELECT COUNT(*) as count FROM users"); // Adjust the table name if necessary
if ($userResult) {
    $userCount = $userResult->fetch_assoc()['count'];
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="Admin_list.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .stat {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            width: 200px;
            text-align: center;
            margin: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .stat:hover {
            transform: scale(1.05);
        }

        .stat h2 {
            margin: 10px 0;
            font-size: 2.5rem;
            color: #333;
        }

        .stat p {
            color: black;
            font-size: 1.2rem;
        }

        .stat .icon {
            font-size: 4rem;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.2;
        }

        .stat.songs {
            background-color: #3498db;
            color: #ecf0f1;
        }

        .stat.artists {
            background-color: #e74c3c;
            color: #ecf0f1;
        }

        .stat.playlists {
            background-color: #f39c12;
            color: #ecf0f1;
        }

        .stat.comments {
            background-color: #2ecc71;
            color: #ecf0f1;
        }

        .stat.users {
            background-color: #9b59b6;
            color: #ecf0f1;
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
                <a href="index.php" class="logout">Logout</a>
            </div> 
        </aside>
        <main class="main-content">
            <h1>Admin Dashboard</h1>
            <div class="stats">
                <div class="stat songs">
                    <i class="fas fa-music icon"></i>
                    <h2><?php echo $songCount; ?></h2>
                    <p>Songs</p>
                </div>
                <div class="stat artists">
                    <i class="fas fa-user icon"></i>
                    <h2><?php echo $artistCount; ?></h2>
                    <p>Artists</p>
                </div>
                <div class="stat playlists">
                    <i class="fas fa-list icon"></i>
                    <h2><?php echo $playlistCount; ?></h2>
                    <p>Playlists</p>
                </div>
                <div class="stat comments">
                    <i class="fas fa-comments icon"></i>
                    <h2><?php echo $commentCount; ?></h2>
                    <p>Comments</p>
                </div>
                <div class="stat users">
                    <i class="fas fa-users icon"></i>
                    <h2><?php echo $userCount; ?></h2>
                    <p>Users</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
