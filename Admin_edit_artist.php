<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Check if artist ID is provided
if (!isset($_GET['id'])) {
    // Handle error, redirect or show error message
    exit("Artist ID not provided");
}

$artist_id = $_GET['id'];

// Fetch artist data
$sql = "SELECT * FROM artist WHERE artist_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $artist = $result->fetch_assoc();
} else {
    exit("Artist not found");
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $artist_name = $_POST['artist_name'];
    $artist_email = $_POST['artist_email'];
    $artist_youtube = $_POST['artist_youtube'];

    // Check if a new profile image is uploaded
    if ($_FILES['artist_photo']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['artist_photo']['name'];
        $temp_name = $_FILES['artist_photo']['tmp_name'];
        $image_path = "uploads/artist/" . $image_name;

        // Move uploaded file to desired location
        if (move_uploaded_file($temp_name, $image_path)) {
            $artist_photo = $image_path;
        } else {
            echo "Failed to move uploaded file.";
            exit();
        }
    } else {
        $artist_photo = $artist['artist_photo']; // Use existing profile image if no new one uploaded
    }

    // Update artist data in database
    $sql = "UPDATE artist SET artist_name=?, artist_email=?, artist_youtube=?, artist_photo=? WHERE artist_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $artist_name, $artist_email, $artist_youtube, $artist_photo, $artist_id);

    if ($stmt->execute()) {
        // Artist updated successfully
        header("Location: Admin_artist_list.php");
        exit();
    } else {
        // Error updating artist
        echo "Failed to update artist: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artist</title>
    <link rel="stylesheet" href="Admin_upload.css">
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
            <h1>Edit Artist</h1>
            <form id="editForm" action="Admin_edit_artist.php?id=<?php echo $artist_id; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="artist_id" value="<?php echo $artist['artist_id']; ?>">
                <div class="form-group">
                    <label for="artist_name">Artist Name *</label>
                    <input type="text" id="artist_name" name="artist_name" value="<?php echo $artist['artist_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="artist_email">Email *</label>
                    <input type="email" id="artist_email" name="artist_email" value="<?php echo $artist['artist_email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="artist_youtube">YouTube</label>
                    <input type="url" id="artist_youtube" name="artist_youtube" value="<?php echo $artist['artist_youtube']; ?>">
                </div>
                <div class="form-group">
                    <label for="artist_photo">Profile Image</label>
                    <input type="file" id="artist_photo" name="artist_photo" accept="image/*">
                </div>
                <button type="submit">Update Artist</button>
            </form>
        </main>
    </div>
</body>
</html>
