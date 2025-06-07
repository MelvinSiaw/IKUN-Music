<?php
// Include database connection
include 'db_connection.php'; // Ensure correct path to db_connection.php

// Initialize variables
$song = null;
$comments = [];

function fetchSongDetails($conn, $songID) {
    $sql = "SELECT s.*, a.artist_name
            FROM Songs s
            JOIN artist a ON s.artist_id = a.artist_id
            WHERE s.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $songID);
    $stmt->execute();
    $result = $stmt->get_result();
    $song = $result->fetch_assoc();
    $stmt->close();

    return $song;
}

// Function to get profile image path
function getProfileImagePath($profile_image) {
    if (!empty($profile_image)) {
        
        if (strpos($profile_image, 'uploads/') === 0) {
            $image_path = $profile_image;
        } elseif (strpos($profile_image, 'uploads/') === 0) {
            $image_path = substr($profile_image, 3); 
        } else {
            $image_path = 'uploads/profile/' . $profile_image;
        }
    } else {
        $image_path = 'assets/pic/default.jpg'; // Default image path
    }
    return $image_path;
}

// Function to fetch comments for a song
function fetchComments($conn, $songID) {
    $comments = [];
    $commentsQuery = "SELECT c.*, u.name, u.profile_image FROM Comments c JOIN users u ON c.user_id = u.user_id WHERE song_id = $songID ORDER BY created_at DESC";
    $commentsResult = mysqli_query($conn, $commentsQuery);

    if ($commentsResult) {
        while ($row = mysqli_fetch_assoc($commentsResult)) {
            // Process the profile image path
            $row['profile_image'] = getProfileImagePath($row['profile_image']);
            $comments[] = $row;
        }
    }

    return $comments;
}

// Handle new comment submission
function addComment($conn, $songID, $userID, $commentText) {
    $commentText = mysqli_real_escape_string($conn, $commentText);
    $insertCommentQuery = "INSERT INTO Comments (song_id, user_id, comment_text) VALUES ($songID, $userID, '$commentText')";
    $result = mysqli_query($conn, $insertCommentQuery); 

    return $result;
}
