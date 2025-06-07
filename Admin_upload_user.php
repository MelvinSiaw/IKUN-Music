<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone'];
    $profile_image = ''; // Initialize profile image variable

    // Handle profile image upload
    if ($_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['profile_image']['name'];
        $temp_name = $_FILES['profile_image']['tmp_name'];
        $image_path = "uploads/profile/" . $image_name;

        // Move uploaded file to desired location
        if (move_uploaded_file($temp_name, $image_path)) {
            $profile_image = $image_path;
        } else {
            echo "Failed to move uploaded file.";
            exit();
        }
    } else {
        echo "Profile image upload failed.";
        exit();
    }

    // Insert user data into database
    $sql = "INSERT INTO users (name, email, password_hash, phone, profile_image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $email, $password, $phone, $profile_image);

    if ($stmt->execute()) {
        // User added successfully
        header("Location: Admin_user_list.php"); // Redirect to user list page
        exit();
    } else {
        // Error inserting user
        echo "Failed to add user: " . $conn->error;
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
    <title>Upload a User</title>
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
            <h1>Upload a User</h1>
            <form id="uploadForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Image *</label>
                    <input type="file" id="profile_image" name="profile_image" required accept="image/*">
                </div>
                
                <button type="submit">Add User</button>
            </form>
        </main>
    </div>
</body>
</html>
