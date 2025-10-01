<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'innovcentralph@gmail.com'; // your Gmail
    $mail->Password = 'emymneyjnzpyizsh';        // your App Password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('innovcentralph@gmail.com', 'Test Sender');
    $mail->addAddress('bsteps12345@gmail.com', 'Test Receiver');

    $mail->isHTML(false); // plain text
    $mail->Subject = 'Test Email via Gmail SMTP';
    $mail->Body    = "Hello,\n\nThis is a test email sent using PHPMailer and Gmail SMTP.\n\nBest Regards,\nTest Script";

    $mail->send();
    echo "✅ Test email has been sent successfully!";
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
