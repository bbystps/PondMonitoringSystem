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
$status = $json_data['status'];
$value = $json_data['value'];
$timestamp = $json_data['timestamp'];

// Map sensor codes to friendly names
$sensor_names = [
  'DO'   => 'Dissolved Oxygen',
  'TEMP' => 'Water Temperature',
  'pH'   => 'PH Level'
];

// Replace sensor code with friendly name if available
$display_sensor = isset($sensor_names[$sensor]) ? $sensor_names[$sensor] : $sensor;

// Detect status changes
$alert_triggered = false;
$email_subject = "Sensor Alert: $display_sensor is $status!";
$email_body  = "Dear Operator,\n\n$display_sensor has changed status.\n\n";
$email_body .= "📍 $display_sensor: $status 🚨 (Current Value: $value)\n";
$email_body .= "\n⏰ Timestamp: $timestamp \n";
$email_body .= "Best Regards,\nPOND Monitoring System";

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
