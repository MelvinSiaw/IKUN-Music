<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $conn = require __DIR__ . "/db_connection.php";
    
    $sql = "SELECT name, profile_image, email, phone FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->bind_result($name, $profile_image, $email, $phone);
    $stmt->fetch();
    $stmt->close();

    // Handle the image path
    if (!empty($profile_image)) {
        // Check if the path starts with 'uploads/' or '../uploads/'
        if (strpos($profile_image, 'uploads/') === 0) {
            $image_path = $profile_image;
        } elseif (strpos($profile_image, '../uploads/') === 0) {
            $image_path = substr($profile_image, 3); // Remove the '../' prefix
        } else {
            $image_path = 'uploads/profile/' . $profile_image;
        }
    } else {
        $image_path = 'assets/pic/default.jpg';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // File upload handling
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/profile/";
        $file_extension = pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $profile_image = $target_file;  // Store the relative path
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, profile_image = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $phone, $profile_image, $user_id);
    if ($stmt->execute()) {
        header("Location: User_Profile.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/user_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Existing styles */
        
        #editUserBtn {
            background-color: #5b34eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        #editUserBtn:hover {
            background-color: #472dbe;
        }

        .contact-info {
            font-size: 18px;
            color: black;
            margin: 10px 0;
        }

        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 500px; 
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-form input[type="text"],
        .modal-form input[type="email"],
        .modal-form input[type="file"] {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-form button {
            background-color: #5b34eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-form button:hover {
            background-color: #472dbe;
        }
        
        /* Add this to your CSS file */
        #logout {
            color: #ffffff; /* Default color */
            transition: color 0.3s; /* Smooth transition for color change */
        }

        #logout:hover {
            color: #ff0000; /* Red color on hover */
        }
        #logout:hover .fas {
            color: #ff0000; /* Red color for the icon on hover */
        }
        .navbar-link:hover {
            color: #7700ff;
        }
        .navbar-link:hover i {
            color: #7700ff;
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
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Profile Picture" class="profile-picture">
                <div class="profile-info">
                    <h1>Profile</h1>
                    <h2><?php echo htmlspecialchars($name); ?></h2>
                    <p class="contact-info"><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                    <p class="contact-info"><strong>Phone Number:</strong> <?php echo htmlspecialchars($phone); ?></p>
                    <button id="addSongBtn">Add New Song</button>
                    <button id="editUserBtn">Edit User</button>
                </div>
            </div>
        </main>
    </div>

    <!-- The Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form class="modal-form" action="User_Profile.php" method="post" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <br>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
                <br>
                <label for="email">Email: (Read-Only)</label>
                <br>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email) ?>" readonly>
                <br>
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <br>
                <label for="profile_image">Profile Picture:</label>
                <input type="file" id="profile_image" name="profile_image">
                <br>
                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("editUserModal");

        // Get the button that opens the modal
        var btn = document.getElementById("editUserBtn");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        document.getElementById('addSongBtn').addEventListener('click', function() {
            window.location.href = 'UploadForm.php';
        });
    </script>
</body>
</html>