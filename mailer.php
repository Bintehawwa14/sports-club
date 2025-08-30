<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendResetEmail($toEmail, $resetLink) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        $mail->Username = "thegamemaker@gmail.com"; // Your email
        $mail->Password = "tiyelnjqtvscwgjb"; // Your app password
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587; // TLS
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->setFrom('thegamemaker@gmail.com', 'the game maker');
        $mail->addAddress($toEmail);  // 👈 This is where error occurs if $toEmail is empty

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";

        $mail->send();
        echo "Reset link sent successfully to $toEmail";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

