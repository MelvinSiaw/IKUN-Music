<?php
require 'db_connection.php'; // Add your database connection details

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    if ($password !== $password_confirmation) {
        echo 'Passwords do not match';
        exit;
    }

    // Validate token and expiry
    $query = "SELECT * FROM users WHERE reset_token='$token' AND token_expiry > NOW()";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET password_hash='$hashed_password', reset_token=NULL, token_expiry=NULL WHERE reset_token='$token'";
        if (mysqli_query($conn, $query)) {
            echo 'Password has been reset successfully';
        } else {
            echo 'Error updating password';
        }
    } else {
        echo 'Invalid or expired token';
    }
}
?>