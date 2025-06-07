<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $artist_name = $_POST['artist_name'];
    $artist_email = $_POST['artist_email'];
    $artist_youtube_link = $_POST['artist_youtube_link'];
    $artist_photo = ''; // Initialize profile image variable

    // Handle profile image upload
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
        echo "Profile image upload failed.";
        exit();
    }

    // Insert artist data into database
    $sql = "INSERT INTO artist (artist_name, artist_email, artist_youtube, artist_photo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $artist_name, $artist_email, $artist_youtube_link, $artist_photo);

    if ($stmt->execute()) {
        // Artist added successfully
        header("Location: Admin_artist_list.php"); // Redirect to artist list page
        exit();
    } else {
        // Error inserting artist
        echo "Failed to add artist: " . $conn->error;
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
    <title>Upload an Artist</title>
    <link rel="stylesheet" href="Admin_upload.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
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
            <h1>Upload an Artist</h1>
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="artist_name">Artist Name *</label>
                    <input type="text" id="artist_name" name="artist_name" required>
                </div>
                <div class="form-group">
                    <label for="artist_email">Email *</label>
                    <input type="email" id="artist_email" name="artist_email" required>
                </div>
                <div class="form-group">
                    <label for="artist_youtube_link">YouTube Channel Link</label>
                    <input type="url" id="artist_youtube_link" name="artist_youtube_link">
                </div>
                <div class="form-group">
                    <label for="artist_photo">Profile Image *</label>
                    <input type="file" id="artist_photo" name="artist_photo" required accept="image/*">
                </div>
                
                <button type="submit">Add Artist</button>
            </form>
        </main>
    </div>
</body>
</html>
