<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $song_title = $_POST['song_title'];
    $artist = $_POST['artist'];
    $language = $_POST['language'];
    $categories = $_POST['categories'];
    $release_date = $_POST['release_date'];
    $mp3_upload = '';
    $profile_picture_upload = '';
    $background_picture_upload = '';

    // Handle MP3 upload
    if ($_FILES['mp3_upload']['error'] === UPLOAD_ERR_OK) {
        $mp3_name = $_FILES['mp3_upload']['name'];
        $temp_name = $_FILES['mp3_upload']['tmp_name'];
        $mp3_path = "uploads/mp3/" . $mp3_name;

        if (move_uploaded_file($temp_name, $mp3_path)) {
            $mp3_upload = $mp3_path;
        } else {
            echo "Failed to move uploaded MP3 file.";
            exit();
        }
    } else {
        echo "MP3 upload failed.";
        exit();
    }

    // Handle profile picture upload
    if ($_FILES['profile_picture_upload']['error'] === UPLOAD_ERR_OK) {
        $profile_picture_name = $_FILES['profile_picture_upload']['name'];
        $temp_name = $_FILES['profile_picture_upload']['tmp_name'];
        $profile_picture_path = "uploads/profile/" . $profile_picture_name;

        if (move_uploaded_file($temp_name, $profile_picture_path)) {
            $profile_picture_upload = $profile_picture_path;
        } else {
            echo "Failed to move uploaded profile picture.";
            exit();
        }
    } else {
        echo "Profile picture upload failed.";
        exit();
    }

    // Handle background picture upload
    if ($_FILES['background_picture_upload']['error'] === UPLOAD_ERR_OK) {
        $background_picture_name = $_FILES['background_picture_upload']['name'];
        $temp_name = $_FILES['background_picture_upload']['tmp_name'];
        $background_picture_path = "uploads/background/" . $background_picture_name;

        if (move_uploaded_file($temp_name, $background_picture_path)) {
            $background_picture_upload = $background_picture_path;
        } else {
            echo "Failed to move uploaded background picture.";
            exit();
        }
    } else {
        echo "Background picture upload failed.";
        exit();
    }

    // Check if the artist already exists
    $artist_sql = "SELECT artist_id FROM artist WHERE artist_name = ?";
    $artist_stmt = $conn->prepare($artist_sql);
    $artist_stmt->bind_param("s", $artist);
    $artist_stmt->execute();
    $artist_stmt->store_result();

    if ($artist_stmt->num_rows == 0) {
        // Artist does not exist, insert new artist
        $insert_artist_sql = "INSERT INTO artist (artist_name, artist_email) VALUES (?, '')"; // Add other fields as needed
        $insert_artist_stmt = $conn->prepare($insert_artist_sql);
        $insert_artist_stmt->bind_param("s", $artist);
        $insert_artist_stmt->execute();
        $artist_id = $insert_artist_stmt->insert_id;
        $insert_artist_stmt->close();
    } else {
        // Artist exists, get the artist_id
        $artist_stmt->bind_result($artist_id);
        $artist_stmt->fetch();
    }

    $artist_stmt->close();

    // Insert song data into database
    $song_sql = "INSERT INTO songs (song_title, artist_id, language, categories, release_date, mp3_upload, profile_picture_upload, background_picture_upload) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $song_stmt = $conn->prepare($song_sql);
    $song_stmt->bind_param("sissssss", $song_title, $artist_id, $language, $categories, $release_date, $mp3_upload, $profile_picture_upload, $background_picture_upload);

    if ($song_stmt->execute()) {
        // Song added successfully
        header("Location: Admin_song_list.php"); // Redirect to song list page
        exit();
    } else {
        // Error inserting song
        echo "Failed to add song: " . $conn->error;
    }

    // Close statement and connection
    $song_stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload a Song</title>
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
            <h1>Upload a Song</h1>
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="song_title">Song Title *</label>
                    <input type="text" id="song_title" name="song_title" required>
                </div>
                <div class="form-group">
                    <label for="artist">Artist *</label>
                    <input type="text" id="artist" name="artist" required>
                </div>
                <div class="form-group">
                    <label for="language">Language</label>
                    <select id="language" name="language">
                        <option value="english">English</option>
                        <option value="chinese">Chinese</option>
                        <option value="korean">Korean</option>
                        <option value="japanese">Japanese</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="categories">Categories</label>
                    <input type="text" id="categories" name="categories">
                </div>
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date">
                </div>
                <div class="form-group">
                    <label for="mp3_upload">MP3 Upload *</label>
                    <input type="file" id="mp3_upload" name="mp3_upload" required accept="audio/*">
                </div>
                <div class="form-group">
                    <label for="profile_picture_upload">Profile Picture Upload *</label>
                    <input type="file" id="profile_picture_upload" name="profile_picture_upload" required accept="image/*">
                </div>
                <div class="form-group">
                    <label for="background_picture_upload">Background Picture Upload *</label>
                    <input type="file" id="background_picture_upload" name="background_picture_upload" required accept="image/*">
                </div>
                <button type="submit">Add Song</button>
            </form>
        </main>
    </div>
</body>
</html>
