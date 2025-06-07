<?php
session_start();

// Validate and sanitize inputs
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'], $_POST['otp'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $otp = filter_var($_POST['otp'], FILTER_SANITIZE_NUMBER_INT);

    // Check if OTP matches the stored OTP in session
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp && $_SESSION['email'] == $email) {
        // Clear OTP session variables
        unset($_SESSION['otp']);
        unset($_SESSION['email']);

        // Perform further actions like user registration or login
        // For demonstration, let's redirect to a success page
        echo "OTP verified";
    } else {
        echo "Invalid OTP";
    }
} else {
    echo "Invalid request";
}
?>