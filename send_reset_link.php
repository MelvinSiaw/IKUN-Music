<?php
// Include PHPMailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'db_connection.php'; // Add your database connection details

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generate a random token

    // Store the token in the database against the user's email
    $query = "UPDATE users SET reset_token='$token', token_expiry=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $reset_link = "http://localhost/FYP/reset_password.php?token=$token";
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'chanjiajun321@gmail.com'; // SMTP username
            $mail->Password   = 'ivdg inba cphd pmlp'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('chanjiajun321@gmail.com', 'IKUN MUSIC');
            $mail->addAddress($email);

            //Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = '
                Dear Mr/Ms,
                <br><br>
                Click <a href="' . $reset_link . '">here</a> to reset your password.
                <br><br>
                Thank you for choosing Ikun Music. 
                <br><br>
                This reset link is valid for a limited time only. Please change your password as soon as possible to complete your password reset.
                <br><br>
                <img src="cid:ikunmusicgif">
                <br><br>
                Cheers,
                <br>
                The Ikun Music Team
            ';

            // Attach inline image
            $mail->AddEmbeddedImage('assets/gif/jinitaimei.gif', 'ikunmusicgif', 'ikunmusicgif.gif');  

            $mail->send();
            echo 'Password reset link has been sent to your email';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'Email not found';
    }
}
?>
