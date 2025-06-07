<?php
session_start();
include 'db_connection.php';

$response = array('success' => false, 'message' => 'Invalid request');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'like') {
        if (isset($_POST['song_id']) && isset($_SESSION['user_id'])) {
            $songID = intval($_POST['song_id']);
            $userID = intval($_SESSION['user_id']);

            // Check if already liked
            $checkLike = $conn->prepare("SELECT COUNT(*) as count FROM liked_songs WHERE user_id = ? AND song_id = ?");
            $checkLike->bind_param("ii", $userID, $songID);
            $checkLike->execute();
            $result = $checkLike->get_result();
            $liked = $result->fetch_assoc()['count'] > 0;
            $checkLike->close();

            if ($liked) {
                // Remove like
                $stmt = $conn->prepare("DELETE FROM liked_songs WHERE user_id = ? AND song_id = ?");
                $stmt->bind_param("ii", $userID, $songID);
                if ($stmt->execute()) {
                    $response = array('success' => true, 'message' => 'Song unliked');
                } else {
                    $response = array('success' => false, 'message' => 'Failed to unlike song');
                }
                $stmt->close();
            } else {
                // Add like
                $stmt = $conn->prepare("INSERT INTO liked_songs (user_id, song_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $userID, $songID);
                if ($stmt->execute()) {
                    $response = array('success' => true, 'message' => 'Song liked');
                } else {
                    $response = array('success' => false, 'message' => 'Failed to like song');
                }
                $stmt->close();
            }
        } else {
            $response = array('success' => false, 'message' => 'Song ID or user ID missing');
        }
    } elseif (isset($_POST['comment_text']) && isset($_POST['song_id']) && isset($_SESSION['user_id'])) {
        $commentText = trim($_POST['comment_text']);
        $userID = intval($_SESSION['user_id']);
        $songID = intval($_POST['song_id']); // Ensure you have song_id in the POST data

        if (!empty($commentText)) {
            $stmt = $conn->prepare("INSERT INTO comments (user_id, song_id, comment_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userID, $songID, $commentText);
            if ($stmt->execute()) {
                $response = array('success' => true, 'message' => 'Comment added');
            } else {
                $response = array('success' => false, 'message' => 'Failed to add comment');
            }
            $stmt->close();
        } else {
            $response = array('success' => false, 'message' => 'Comment cannot be empty');
        }
    } else {
        $response = array('success' => false, 'message' => 'Invalid request parameters');
    }
} else {
    $response = array('success' => false, 'message' => 'Invalid request method');
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>