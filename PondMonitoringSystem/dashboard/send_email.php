<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Read JSON input from Python
$data = file_get_contents("php://stdin");
$json_data = json_decode($data, true);

// Extract sensor data
$sensor = $json_data['sensor'];
$value = $json_data['value'];
$status = $json_data['status'];
$timestamp = $json_data['timestamp'];

// Detect status changes
$alert_triggered = false;
$email_subject = "Sensor Alert: $sensor is $status!";
$email_body = "Dear Operator,\n\n$sensor has changed status.\n\n";
$email_body .= "📍 $sensor: $status 🚨 (Current Value: $value)\n";

$email_body .= "\n⏰ Timestamp: $timestamp \n";
$email_body .= "Best Regards,\nDAM Monitoring System";

sendEmail($email_subject, $email_body);

/**
 * Function to send an email using PHPMailer
 */
function sendEmail($subject, $body)
{
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'innovcentralph@gmail.com';
    $mail->Password = 'emymneyjnzpyizsh';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('innovcentralph@gmail.com');
    $mail->addAddress("bsteps12345@gmail.com");

    $mail->isHTML(false); // Send as plain text

    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
}
