<?php
$conn = require __DIR__ . "/db_connection.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$song_details_sql = "SELECT * FROM Songs WHERE id = ?";
$stmt = $conn->prepare($song_details_sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$song_details_result = $stmt->get_result();
$song = $song_details_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($song['song_title']); ?></title>
    <link rel="stylesheet" href="assets/css/Home.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($song['song_title']); ?></h1>
    <div class="song-details">
        <img src="<?php echo htmlspecialchars($song['profile_picture_upload']); ?>" alt="Song Image">
        <span>Artist: <?php echo htmlspecialchars($song['artist']); ?></span>
        <span>Release Date: <?php echo htmlspecialchars($song['release_date']); ?></span>
        <audio controls>
            <source src="path_to_song/<?php echo htmlspecialchars($song['song_file']); ?>" type="audio/mpeg">
            Your browser does not support the audio element.
        </audio>
    </div>
</body>
</html>

<?php
$conn->close();
?>
