<?php
header('Content-Type: application/json');

$response = array('status' => 'error', 'message' => 'Failed to add artist');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection
    include 'db_connection.php';

    // Escape user inputs for security
    $newArtistName = mysqli_real_escape_string($conn, $_POST['newArtistName']);
    $newArtistEmail = mysqli_real_escape_string($conn, $_POST['newArtistEmail']);
    $newArtistYouTube = mysqli_real_escape_string($conn, $_POST['newArtistYouTube']);
    $newArtistPhoto = null;

    // File upload handling for artist photo (optional)
    if (isset($_FILES['newArtistPhoto']) && $_FILES['newArtistPhoto']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/artist/';
        $newArtistPhoto = $uploadDir . basename($_FILES['newArtistPhoto']['name']);
        move_uploaded_file($_FILES['newArtistPhoto']['tmp_name'], $newArtistPhoto);
    }

    // Insert query
    $sql = "INSERT INTO artist (artist_name, artist_email, artist_youtube, artist_photo)
            VALUES ('$newArtistName', '$newArtistEmail', '$newArtistYouTube', '$newArtistPhoto')";
    
    if (mysqli_query($conn, $sql)) {
        $artistId = mysqli_insert_id($conn); // Get the ID of the newly inserted artist
        $response = array(
            'status' => 'success',
            'message' => 'Artist added successfully.',
            'artist_id' => $artistId,
            'artist_name' => $newArtistName
        );
    } else {
        $response['message'] = 'Error: ' . mysqli_error($conn);
    }

    // Close connection
    mysqli_close($conn);
}

echo json_encode($response);
?>