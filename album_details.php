<?php
session_start();

include 'db_connection.php';

$artist = isset($_GET['artist']) ? $conn->real_escape_string($_GET['artist']) : '';
$songs = [];

if ($artist) {
    $songsQuery = "SELECT * FROM Songs WHERE artist = '$artist'";
    $songsResult = $conn->query($songsQuery);

    while ($row = $songsResult->fetch_assoc()) {
        $songs[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Details</title>
    <link rel="stylesheet" href="assets/css/album_details.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($artist); ?></h1>
    <ul>
        <?php foreach ($songs as $song): ?>
            <li>
                <a href="song_page.php?id=<?php echo $song['id']; ?>"><?php echo htmlspecialchars($song['song_title']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
