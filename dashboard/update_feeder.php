<?php
include("db_conn.php");

$time1 = $_POST['feedTime1'];
$interval1 = $_POST['feedInterval1'];
$time2 = $_POST['feedTime2'];
$interval2 = $_POST['feedInterval2'];
$time3 = $_POST['feedTime3'];
$interval3 = $_POST['feedInterval3'];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Update threshold values
  $stmt = $pdo->prepare("UPDATE feeder_time SET 
        time1 = :time1, interval1 = :interval1, 
        time2 = :time2, interval2 = :interval2, 
        time3 = :time3, interval3 = :interval3 
        WHERE id = 1");

  $stmt->bindParam(':time1', $time1);
  $stmt->bindParam(':interval1', $interval1);
  $stmt->bindParam(':time2', $time2);
  $stmt->bindParam(':interval2', $interval2);
  $stmt->bindParam(':time3', $time3);
  $stmt->bindParam(':interval3', $interval3);

  $feederUpdated = $stmt->execute();

  if ($feederUpdated) {
    echo json_encode(["status" => "success", "message" => "Feeder updated successfully"]);
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to update data"]);
  }
} catch (PDOException $e) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]);
}
