<?php
session_start();

include 'db_connection.php';

$artist_id = isset($_GET['artist_id']) ? $_GET['artist_id'] : '';

if ($artist_id) {
    // Fetch artist details
    $artistQuery = "SELECT * FROM artist WHERE artist_id = ?";
    $stmt = $conn->prepare($artistQuery);
    $stmt->bind_param("i", $artist_id);
    $stmt->execute();
    $artistResult = $stmt->get_result();

    if ($artistResult->num_rows > 0) {
        $artist = $artistResult->fetch_assoc();
        
        // Fetch songs by artist
        $songsQuery = "SELECT * FROM songs WHERE artist_id = ?";
        $stmt = $conn->prepare($songsQuery);
        $stmt->bind_param("i", $artist_id);
        $stmt->execute();
        $songsResult = $stmt->get_result();
    } else {
        echo "Artist not found.";
    }
} else {
    echo "Invalid artist ID.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($artist['artist_name'] ?? 'Artist Name'); ?> - Artist Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/artist_page.css">
    <style>
        body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
        background-image: url('assets/pic/background.png');
        }

        .container {
        width: 100%;
        background-color: #fff;
        margin-top: 250px;
        padding: 30px;
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

        .artist-header {
            display: flex;
            align-items: center;
            margin-bottom: 50px;
        }

        .artist-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-right: 20px;
        }

        .artist-details {
            flex-grow: 1;
        }

        .artist-details h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .artist-details p {
            margin: 5px 0;
            color: #666;
        }

        .artist-details .artist-social a {
            color: #007bff;
            text-decoration: none;
        }

        .artist-details .artist-social a:hover {
            text-decoration: underline;
        }

        .song-section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .song-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .song-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .song-item a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .song-item a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="close-button" onclick="goBack()">&times;</button>
        <header class="artist-header">
            <img class="artist-photo" src="<?php echo htmlspecialchars($artist['artist_photo'] ?? ''); ?>" alt="Artist Image">
            <div class="artist-details">
                <h1 class="artist-name"><?php echo htmlspecialchars($artist['artist_name'] ?? 'Artist Name'); ?></h1>
                <p class="artist-email"><?php echo htmlspecialchars($artist['artist_email'] ?? ''); ?></p>
                <p class="artist-social">
                    <a href="<?php echo htmlspecialchars($artist['artist_youtube'] ?? '#'); ?>" target="_blank">YouTube</a>
                </p>
            </div>
        </header>
        <section class="song-section">
            <h2 class="section-title">Songs by <?php echo htmlspecialchars($artist['artist_name'] ?? 'Artist Name'); ?></h2>
            <ul class="song-list">
                <?php while ($song = $songsResult->fetch_assoc()): ?>
                    <li class="song-item">
                        <a href="song_page.php?id=<?php echo $song['id']; ?>" class="song-link">
                            <?php echo htmlspecialchars($song['song_title']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>