<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $conn = require __DIR__ . "/db_connection.php";
    
    $sql = "SELECT name, profile_image FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($name, $profile_image);
    $stmt->fetch();
    $stmt->close();
}

if (!empty($profile_image)) {
    if (strpos($profile_image, 'uploads/') === 0) {
        $image_path = $profile_image;
    } elseif (strpos($profile_image, '../uploads/') === 0) {
        $image_path = substr($profile_image, 3);
    } else {
        $image_path = 'uploads/profile/' . $profile_image;
    }
} else {
    $image_path = 'assets/pic/default.jpg';
}

// Fetch artist list
$artists_sql = "SELECT artist_id, artist_name FROM artist";
$artists_result = $conn->query($artists_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload a Song</title>
    <link rel="stylesheet" href="assets/css/upload_song.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .navbar-link:hover {
            color: #7700ff;
        }
        .navbar-link:hover i {
            color: #7700ff;
        }
        #logout {
            color: #ffffff;
            transition: color 0.3s;
        }
        #logout:hover {
            color: #ff0000;
        }
        #logout:hover .fas {
            color: #ff0000;
        }
        .toast {
            visibility: hidden;
            min-width: 300px;
            margin-left: -150px;
            background-color: #d4edda;
            color: #155724;
            text-align: left;
            border-radius: 5px;
            padding: 16px;
            position: fixed;
            z-index: 1001;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .toast.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 3.5s;
            animation: fadein 0.5s, fadeout 0.5s 3.5s;
        }
        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;} 
            to {bottom: 30px; opacity: 1;}
        }
        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }
        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;} 
            to {bottom: 0; opacity: 0;}
        }
        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }
        .toast .icon {
            margin-right: 10px;
            font-size: 20px;
        }
        .toast .close {
            margin-left: auto;
            cursor: pointer;
        }
        .popup-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1002;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        .popup-form h2 {
            margin-top: 0;
        }
        .popup-form label {
            display: block;
            margin-top: 10px;
        }
        .popup-form input[type="text"],
        .popup-form input[type="email"],
        .popup-form input[type="url"],
        .popup-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        .popup-form button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #7700ff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .popup-form button:hover {
            background-color: #2980b9;
        }
        .popup-form .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            background: none;
            border: none;
            cursor: pointer;
            outline: none;
            color: black;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1001;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="navbar">
                <div class="navbar-logo">
                    <img src="assets/pic/Inspirational_Quote_Instagram_Post_1.png" alt="Logo" class="navbar-image"><span>IKUN MUSIC</span>
                </div>
                <div class="navbar-links-container">
                    <a href="User_Home.php" class="navbar-link"><i class="fas fa-home"></i> Home</a>
                    <a href="user_playlist.php" class="navbar-link"><i class="fas fa-music"></i> My Playlist</a>
                    <a href="Help_and_Support.html" class="navbar-link"><i class="fas fa-question-circle"></i> Help & Support</a>
                    <a href="About_Us.html" class="navbar-link"><i class="fas fa-info-circle"></i> About Us</a>
                    <a href="UploadForm.php" class="navbar-link"><i class="fas fa-space-shuttle"></i> Ikun Space</a>
                    <a href="logout.php" class="navbar-link" id="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
                <div class="navbar-user">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="User Image">
                    <span><a href="User_Profile.php" class="profile-link"><?php echo htmlspecialchars($name); ?></a></span>
                </div>
            </div>
        </aside>
        <main class="main-content">
            <h1>Upload a Song</h1>
            <form id="uploadForm" enctype="multipart/form-data">
                <label for="songTitle">Song Title<span class="required">*</span></label>
                <input type="text" id="songTitle" name="songTitle" required>

                <label for="artist_id">Select Artist</label>
                <select id="artist_id" name="artist_id">
                    <option value="">Select an artist</option>
                    <?php while ($artist = $artists_result->fetch_assoc()): ?>
                        <option value="<?php echo $artist['artist_id']; ?>"><?php echo htmlspecialchars($artist['artist_name']); ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="button" onclick="openPopup()">Add New Artist</button>

                <label for="language">Language</label>
                <select id="language" name="language">
                    <option value="english">English</option>
                    <option value="chinese">Chinese</option>
                    <option value="korean">Korean</option>
                    <option value="japanese">Japanese</option>
                </select>

                <label for="categories">Categories<span class="required">*</span></label>
                <input type="text" id="categories" name="categories" required>

                <label for="releaseDate">Release Date</label>
                <input type="date" id="releaseDate" name="releaseDate" required>

                <label for="mp3Upload">MP3 Upload<span class="required">*</span></label>
                <input type="file" id="mp3Upload" name="mp3Upload" accept="audio/mp3" required>

                <label for="profilePictureUpload">Profile Picture Upload</label>
                <input type="file" id="profilePictureUpload" name="profilePictureUpload" accept="image/*">

                <label for="backgroundPictureUpload">Background Picture Upload</label>
                <input type="file" id="backgroundPictureUpload" name="backgroundPictureUpload" accept="image/*">

                <button type="submit" id="submitButton">Add Song</button>
            </form>
        </main>
    </div>

    <!-- Popup Form for New Artist -->
    <div class="overlay" id="overlay"></div>
    <div class="popup-form" id="popupForm">
        <button class="close-button" onclick="closePopup()">&times;</button>
        <h2>Add New Artist</h2>
        <form id="newArtistForm">
            <label for="newArtistName">Artist Name<span class="required">*</span></label>
            <input type="text" id="newArtistName" name="newArtistName" required>

            <label for="newArtistEmail">Artist Email</label>
            <input type="email" id="newArtistEmail" name="newArtistEmail">

            <label for="newArtistYouTube">Artist YouTube Link</label>
            <input type="url" id="newArtistYouTube" name="newArtistYouTube">

            <label for="newArtistPhoto">Artist Photo</label>
            <input type="file" id="newArtistPhoto" name="newArtistPhoto" accept="image/*">

            <button type="submit">Add Artist</button>
            <button type="button" onclick="closePopup()">Cancel</button>
        </form>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span class="icon"><i class="fas fa-check-circle"></i></span>
        <span class="message"></span>
        <span class="close" onclick="hideToast()">&times;</span>
    </div>

    <script>
    function showToast(message) {
        var toast = document.getElementById("toast");
        toast.querySelector(".message").innerText = message;
        toast.className = "toast show";
        setTimeout(function(){ hideToast(); }, 4000); // Show toast for 4 seconds
    }

    function hideToast() {
        var toast = document.getElementById("toast");
        toast.className = toast.className.replace("show", "");
    }

    function openPopup() {
        document.getElementById("overlay").style.display = "block";
        document.getElementById("popupForm").style.display = "block";
    }

    function closePopup() {
        document.getElementById("overlay").style.display = "none";
        document.getElementById("popupForm").style.display = "none";
    }

    document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        let submitButton = document.getElementById('submitButton');
        
        // Show loading spinner and disable button
        submitButton.disabled = true;
        submitButton.innerHTML = 'Uploading...';

        // AJAX request to Upload.php
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'Upload.php', true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                showToast(xhr.responseText);
                // Hide loading spinner and enable button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Add Song';
            }
        };
        xhr.send(formData);
    });

    document.getElementById('newArtistForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);

    // AJAX request to add new artist
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'AddArtist.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            let response = JSON.parse(xhr.responseText);

            if (response.status === 'success') {
                // Update the artist dropdown list
                let artistSelect = document.getElementById('artist_id');
                let newOption = document.createElement('option');
                newOption.value = response.artist_id;
                newOption.textContent = response.artist_name;
                artistSelect.appendChild(newOption);

                // Show success message
                showToast(response.message);

                // Close the popup
                closePopup();
            } else {
                // Show error message
                showToast(response.message);
            }
        }
    };
    xhr.send(formData);
});
    </script>
</body>
</html>
