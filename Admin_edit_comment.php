<?php
session_start();

// Include database connection
$conn = require __DIR__ . "/db_connection.php"; // Adjust the path to db_connection.php as needed

// Function to fetch comments for a song
function fetchComments($conn) {
    $comments = [];
    $commentsQuery = "SELECT c.*, u.name, s.song_title 
                      FROM Comments c 
                      JOIN users u ON c.user_id = u.user_id 
                      JOIN songs s ON c.song_id = s.id 
                      ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($commentsQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }
    
    return $comments;
}

// Function to delete a comment
function deleteComment($conn, $comment_id) {
    $deleteQuery = "DELETE FROM Comments WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
}

// Check if a delete request has been made
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $comment_id = $_POST['comment_id'];
    deleteComment($conn, $comment_id);
}

$comments = fetchComments($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Comments Page</title>
    <link rel="stylesheet" href="Admin_edit_comment.css"> <!-- Adjust the path to your CSS file -->
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
            <h1>Admin Comments Page</h1>
            <table>
                <thead>
                    <tr>
                        <th>Comment ID</th>
                        <th>Song Title</th>
                        <th>Username</th>
                        <th>Comment Text</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td><?php echo $comment['id']; ?></td>
                            <td><?php echo $comment['song_title']; ?></td>
                            <td><?php echo $comment['name']; ?></td>
                            <td><?php echo $comment['comment_text']; ?></td>
                            <td><?php echo $comment['created_at']; ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="delete" class="action-btn delete-btn">Delete</button>
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <input type="text" name="comment_text" value="<?php echo htmlspecialchars($comment['comment_text']); ?>" class="edit-input">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
