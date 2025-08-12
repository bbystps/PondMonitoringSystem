<?php
include("db_conn.php");

$time_interval = $_POST['sendInterval'];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Update threshold values
  $stmt = $pdo->prepare("UPDATE sending_interval SET 
        time_interval = :time_interval
        WHERE id = 1");

  $stmt->bindParam(':time_interval', $time_interval);

  $sendIntervalUpdated = $stmt->execute();

  if ($sendIntervalUpdated) {
    echo json_encode(["status" => "success", "message" => "Send Interval updated successfully"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to update data"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]);
}
