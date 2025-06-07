<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php";

// Initialize variable to store users data
$users = [];

// Fetch user data
$sql = "SELECT user_id, name, email, phone, profile_image FROM users";
$result = $conn->query($sql);

// Check if query execution was successful
if ($result) {
    // Fetch all rows as associative array
    $users = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Query execution failed
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User List</title>
    <link rel="stylesheet" href="Admin_list.css">
    <style>
    .profile-image {
    max-width: 100%; 
    max-height: 45px; 
    width: auto; 
    height: auto; 
    display: block; 
    margin-top: 10px; 
    object-fit: contain; 
    align-items: center;
    object-fit: contain; 
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
                <a href="index.php" class="logout">Logout</a> <!-- Replace with your logout page -->
            </div>   
        </aside>
        <main class="main-content">
            <header>
                <input type="text" name="search" placeholder="Artist, Album, Song, etc...">
            </header>
            <h1>User List</h1>
            <button id="addNewBtn">Add New</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Profile Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userList">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo $user['name']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phone']; ?></td>
                            <td>
                                    <?php if (!empty($user['profile_image'])): ?>
                                        <?php
                                        $image_path = $user['profile_image'];
                                        ?>
                                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Image" class="profile-image">
                                    <?php else: ?>
                                        No image available
                                    <?php endif; ?>
                            </td>
                            <td class="action-buttons">
                                <button class="edit" onclick="editUser(<?php echo $user['user_id']; ?>)">✏️</button>
                                <button class="delete" onclick="deleteUser(<?php echo $user['user_id']; ?>)">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6">No users found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>
    <script>
        function editUser(id) {
            window.location.href = `Admin_edit_user.php?id=${id}`;
        }

        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                // Send AJAX request to delete user
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'Admin_delete_user.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Refresh the page after deletion
                        window.location.reload();
                    } else {
                        alert('Failed to delete user. Please try again.');
                    }
                };
                xhr.send('user_id=' + id);
            }
        
    }

        document.getElementById('addNewBtn').addEventListener('click', function() {
            window.location.href = 'Admin_upload_user.php'; // Navigate to the upload user page
        });
    </script>
</body>
</html>
