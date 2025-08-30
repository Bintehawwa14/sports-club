<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        // Generate a random token
        $token = bin2hex(random_bytes(16));
        $resetLink = "http://localhost/sports-club/reset_password_link.php?token=$token";

        // Save token in DB against user (you must implement this)

        // Pass email + link to mailer
        require 'mailer.php';
        sendResetEmail($email, $resetLink);
    } else {
        echo "No email provided!";
    }
}
?>
