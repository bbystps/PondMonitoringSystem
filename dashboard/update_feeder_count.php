<?php
include("db_conn.php");

$count = $_POST['feederCount'];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Update threshold values
  $stmt = $pdo->prepare("UPDATE feeder_count SET 
        reset_count = :count
        WHERE id = 1");

  $stmt->bindParam(':count', $count);

  $feederUpdated = $stmt->execute();

  if ($feederUpdated) {
    echo json_encode(["status" => "success", "message" => "Feeder updated successfully"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to update data"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]);
}
