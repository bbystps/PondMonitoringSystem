<?php
include("db_conn.php");

$wt_low = $_POST['waterLow'];
$wt_high = $_POST['waterHigh'];
$ph_low = $_POST['phLow'];
$ph_high = $_POST['phHigh'];
$do_low = $_POST['doLow'];
$do_high = $_POST['doHigh'];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Update threshold values
  $stmt = $pdo->prepare("UPDATE threshold SET 
        wt_low = :wt_low, wt_high = :wt_high, 
        ph_low = :ph_low, ph_high = :ph_high, 
        do_low = :do_low, do_high = :do_high 
        WHERE id = 1");

  $stmt->bindParam(':wt_low', $wt_low);
  $stmt->bindParam(':wt_high', $wt_high);
  $stmt->bindParam(':ph_low', $ph_low);
  $stmt->bindParam(':ph_high', $ph_high);
  $stmt->bindParam(':do_low', $do_low);
  $stmt->bindParam(':do_high', $do_high);

  $thresholdUpdated = $stmt->execute();

  if ($thresholdUpdated) {
    echo json_encode(["status" => "success", "message" => "Threshold updated successfully"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to update data"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]);
}
