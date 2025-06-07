<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Check if user ID is provided
if (!isset($_GET['id'])) {
    // Handle error, redirect or show error message
    exit("User ID not provided");
}

$user_id = $_GET['id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    exit("User not found");
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Initialize variables to store form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Check if a new profile image is uploaded
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
        $profile_image = $user['profile_image']; // Use existing profile image if no new one uploaded
    }

    // Update user data in database
    $sql = "UPDATE users SET name=?, email=?, phone=?, profile_image=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $username, $email, $phone, $profile_image, $user_id);

    if ($stmt->execute()) {
        // User updated successfully
        header("Location: Admin_user_list.php");
        exit();
    } else {
        // Error updating user
        echo "Failed to update user: " . $conn->error;
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
    <title>Edit User</title>
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
            <h1>Edit User</h1>
            <form id="editForm" action="Admin_edit_user.php?id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" value="<?php echo $user['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
                <button type="submit">Update User</button>
            </form>
        </main>
    </div>
</body>
</html>
