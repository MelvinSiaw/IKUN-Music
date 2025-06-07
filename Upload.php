<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'db_connection.php'; // Ensure correct path to db_connection.php

    // Escape user inputs for security
    $songTitle = mysqli_real_escape_string($conn, $_POST['songTitle']);
    $artist_id = mysqli_real_escape_string($conn, $_POST['artist_id']);
    $newArtist = mysqli_real_escape_string($conn, $_POST['newArtist']);
    $language = mysqli_real_escape_string($conn, $_POST['language']);
    $categories = mysqli_real_escape_string($conn, $_POST['categories']);
    $releaseDate = $_POST['releaseDate']; // Assuming date is in correct format from HTML form

    // Check if a new artist is being added
    if (!empty($newArtist)) {
        // Insert the new artist into the artist table
        $artist_sql = "INSERT INTO artist (artist_name) VALUES ('$newArtist')";
        if (mysqli_query($conn, $artist_sql)) {
            // Get the new artist's ID
            $artist_id = mysqli_insert_id($conn);
        } else {
            echo "Error: " . $artist_sql . "<br>" . mysqli_error($conn);
            exit();
        }
    }

    // File upload handling for MP3 file
    $mp3Upload = $_FILES['mp3Upload']['name'];
    $mp3Upload_temp = $_FILES['mp3Upload']['tmp_name'];
    $mp3Upload_dest = 'uploads/mp3/' . $mp3Upload;
    move_uploaded_file($mp3Upload_temp, $mp3Upload_dest);

    // File upload handling for profile picture (optional)
    $profilePictureUpload_dest = null;
    if ($_FILES['profilePictureUpload']['name']) {
        $profilePictureUpload = $_FILES['profilePictureUpload']['name'];
        $profilePictureUpload_temp = $_FILES['profilePictureUpload']['tmp_name'];
        $profilePictureUpload_dest = 'uploads/profile/' . $profilePictureUpload;
        move_uploaded_file($profilePictureUpload_temp, $profilePictureUpload_dest);
    }

    // File upload handling for background picture (optional)
    $backgroundPictureUpload_dest = null;
    if ($_FILES['backgroundPictureUpload']['name']) {
        $backgroundPictureUpload = $_FILES['backgroundPictureUpload']['name'];
        $backgroundPictureUpload_temp = $_FILES['backgroundPictureUpload']['tmp_name'];
        $backgroundPictureUpload_dest = 'uploads/background/' . $backgroundPictureUpload;
        move_uploaded_file($backgroundPictureUpload_temp, $backgroundPictureUpload_dest);
    }

    // Insert query
    $sql = "INSERT INTO Songs (song_title, artist_id, language, categories, release_date, mp3_upload, profile_picture_upload, background_picture_upload)
            VALUES ('$songTitle', '$artist_id', '$language', '$categories', '$releaseDate', '$mp3Upload_dest', '$profilePictureUpload_dest', '$backgroundPictureUpload_dest')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Song added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
}
?>
