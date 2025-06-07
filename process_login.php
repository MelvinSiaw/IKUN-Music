<?php

$is_invalid = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $conn = require __DIR__ . "/db_connection.php";
    
    // Get the email and password from POST request
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Prepare and execute query to get user details using a prepared statement
    $stmt = $conn->prepare("SELECT user_id, name, profile_image, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        
        // Verify the password
        if (password_verify($password, $user["password_hash"])) {
            
            session_start();
            session_regenerate_id();
            
            // Set session variables
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['profile_image_url'] = $user['profile_image'] ? $user['profile_image'] : 'default-profile-image.jpg';
            
            // Redirect to the desired page
            header("Location: User_Home.php");
            exit;
        } else {
            $is_invalid = true;
        }
    } else {
        $is_invalid = true;
    }

    $stmt->close();
    $conn->close();
    
    // Redirect back to login page with an error message
    if ($is_invalid) {
        header("Location: index.php?login_error=1");
        exit;
    }
}
?>