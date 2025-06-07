<?php
session_start(); // Start the session

include 'db_connection.php';
include 'song.php';

$songID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$song = fetchSongDetails($conn, $songID);

// Check if user is logged in and session variables are set
if (isset($_SESSION['user_name']) && isset($_SESSION['profile_image_url'])) {
    $userName = $_SESSION['user_name'];
    $profileImageUrl = $_SESSION['profile_image_url'];
} else {
    $userName = 'Guest'; // Default value if user is not logged in
    $profileImageUrl = 'default-profile-image.jpg'; // Default profile image
}

if (!$song) {
    header("Location: error.php");
    exit;
}

$userID = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userID) {
    header("Location: login.php");
    exit;
}

// Check if song is already liked by the user
$isLikedQuery = $conn->prepare("SELECT COUNT(*) as count FROM liked_songs WHERE user_id = ? AND song_id = ?");
$isLikedQuery->bind_param("ii", $userID, $songID);
$isLikedQuery->execute();
$result = $isLikedQuery->get_result();
$likeStatus = $result->fetch_assoc()['count'] > 0;
$isLikedQuery->close();

$comments = fetchComments($conn, $songID);
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/song_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body::before {
            content: "";
            background: url('<?php echo htmlspecialchars($song['background_picture_upload']); ?>') no-repeat center center/cover;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            filter: blur(8px);
        }
        .container {
            background-color: rgba(255, 255, 255, 0.8);
            position: relative;
            padding: 20px;
        }
        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            outline: none;
        }
        .like-button {
            font-size: 24px;
            color: <?php echo $likeStatus ? '#ff0000' : '#ccc'; ?>;
            cursor: pointer;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            outline: none;
        }
        .like-button:hover {
            color: #ff0000;
        }

        .like-button.liked .fa-heart {
            color: #ff0000;
        }

        .like-button.unliked .fa-heart {
            color: #ccc;
        }
        
        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
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
        .audio-player {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 10px;
            padding: 10px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .audio-controls {
            display: flex;
            align-items: center;
        }
        .audio-controls button {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            margin: 0 10px;
            color: #333;
        }
        .audio-progress {
            flex-grow: 1;
            margin: 0 20px;
        }
        .audio-progress input {
            width: 100%;
        }
        .audio-time {
            display: flex;
            justify-content: space-between;
            width: 50px;
            font-size: 14px;
            color: #666;
        }
        .volume-control {
            display: flex;
            align-items: center;
        }
        .volume-control input {
            width: 100px;
            margin-left: 10px;
        }
        .download-button {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="close-button" onclick="goBack()">&times;</button>
        <header>
            <img src="<?php echo htmlspecialchars($song['profile_picture_upload']); ?>" alt="Song Cover">
            <h1><?php echo htmlspecialchars($song['song_title']); ?></h1>
            <p><?php echo htmlspecialchars($song['artist_name']); ?></p>
            <p><?php echo htmlspecialchars($song['categories']); ?></p>
            <form id="like-form" method="POST">
                <input type="hidden" name="song_id" value="<?php echo htmlspecialchars($songID); ?>">
                <button type="button" id="like-button" class="like-button"><i class="far fa-heart"></i></button>
            </form>
        </header>
        <main>
            <div class="audio-player">
                <div class="audio-controls">
                    <button onclick="togglePlayPause()"><i id="play-pause-icon" class="fas fa-play"></i></button>
                </div>
                <div class="audio-progress">
                    <input type="range" id="progress-bar" value="0" max="100">
                </div>
                <div class="audio-time">
                    <span id="current-time">00:00</span>
                </div>
                <div class="volume-control">
                    <i class="fas fa-volume-up"></i>
                    <input type="range" id="volume-bar" min="0" max="1" step="0.01" value="0.4">
                </div>
                <a href="<?php echo htmlspecialchars($song['mp3_upload']); ?>" download class="download-button">
                    <i class="fas fa-download"></i>
                </a>
            </div>
            <audio id="audio-player" volume="0.4">
                <source src="<?php echo htmlspecialchars($song['mp3_upload']); ?>" type="audio/mp3">
                Your browser does not support the audio element.
            </audio>
            <section class="comments">
                <h2>Comments</h2>
                <form id="comment-form">
                    <textarea id="comment-text" name="comment_text" placeholder="Write a Comment..." required></textarea>
                    <input type="hidden" name="song_id" value="<?php echo htmlspecialchars($songID); ?>">
                    <button type="submit" class="send-button">Submit</button>
                </form>
                <div id="comment-list">
                    <?php foreach ($comments as $comment) { ?>
                        <div class="comment">
                            <div class="comment-header">
                                <img src="<?php echo htmlspecialchars($comment['profile_image']); ?>" alt="Profile Picture" class="profile-image">
                                <p><strong><?php echo htmlspecialchars($comment['name']); ?>:</strong> <?php echo htmlspecialchars($comment['comment_text']); ?></p>
                            </div>
                            <small><?php echo htmlspecialchars($comment['created_at']); ?></small>
                        </div>
                    <?php } ?>
                </div>
                <?php if (isset($error)) { ?>
                    <p><?php echo $error; ?></p>
                <?php } ?>
            </section>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span class="icon"><i class="fas fa-check-circle"></i></span>
        <span class="message"></span>
        <span class="close" onclick="hideToast()">&times;</span>
    </div>

    <script>
        // Pass PHP variables to JavaScript
        const userName = <?php echo json_encode($userName); ?>;
        const profileImageUrl = <?php echo json_encode($profileImageUrl); ?>;

        // JavaScript code to use these variables
        console.log("User Name:", userName);
        console.log("Profile Image URL:", profileImageUrl);

        const likeButton = document.getElementById('like-button');
        const commentForm = document.getElementById('comment-form');
        const commentList = document.getElementById('comment-list');
        const toast = document.getElementById('toast');
        const audioPlayer = document.getElementById('audio-player');
        const playPauseIcon = document.getElementById('play-pause-icon');
        const progressBar = document.getElementById('progress-bar');
        const volumeBar = document.getElementById('volume-bar');
        const currentTime = document.getElementById('current-time');

        // Handle like button click
        likeButton.addEventListener('click', function() {
            const songID = document.querySelector('input[name="song_id"]').value;

            fetch('ajax_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'like',
                    song_id: songID
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    likeButton.classList.toggle('liked');
                } else {
                    showToast(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Handle comment form submission
        commentForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(commentForm);

            fetch('ajax_handler.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    const newComment = document.createElement('div');
                    newComment.classList.add('comment');
                    newComment.innerHTML = `
                        <div class="comment-header">
                            <img src="${profileImageUrl}" alt="Profile Picture" class="profile-image">
                            <p><strong>${userName}:</strong> ${document.getElementById('comment-text').value}</p>
                        </div>
                        <small>Just now</small>
                    `;
                    commentList.appendChild(newComment);
                    document.getElementById('comment-text').value = '';
                } else {
                    showToast(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Handle play/pause button click
        function togglePlayPause() {
            if (audioPlayer.paused) {
                audioPlayer.play();
                playPauseIcon.classList.replace('fa-play', 'fa-pause');
            } else {
                audioPlayer.pause();
                playPauseIcon.classList.replace('fa-pause', 'fa-play');
            }
        }

        // Update progress bar and time
        audioPlayer.addEventListener('timeupdate', () => {
            const value = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressBar.value = value;
            currentTime.textContent = formatTime(audioPlayer.currentTime);
        });

        progressBar.addEventListener('input', () => {
            const time = (progressBar.value / 100) * audioPlayer.duration;
            audioPlayer.currentTime = time;
        });

        volumeBar.addEventListener('input', () => {
            audioPlayer.volume = volumeBar.value;
        });

        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        function showToast(message) {
            toast.querySelector('.message').textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 4000);
        }

        function hideToast() {
            toast.classList.remove('show');
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
