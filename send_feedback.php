<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPmailer/src/Exception.php';
require 'PHPmailer/src/PHPMailer.php';
require 'PHPmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                                        // Enable verbose debug output
        $mail->isSMTP();                                             // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                        // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
        $mail->Username   = 'chanjiajun321@gmail.com';               // SMTP username
        $mail->Password   = 'ivdg inba cphd pmlp';                   // SMTP password
        $mail->SMTPSecure = 'tls';                                   // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                                     // TCP port to connect to

        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('chanjiajun321@gmail.com');                // Add a recipient

        // Content
        $mail->isHTML(false);                                        // Set email format to plain text
        $mail->Subject = 'New Feedback from Contact Form';
        $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

        $mail->send();
        echo 'Feedback sent successfully!';
    } catch (Exception $e) {
        echo "There was an error sending your feedback. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>