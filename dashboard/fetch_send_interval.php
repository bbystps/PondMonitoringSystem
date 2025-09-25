<?php
include("db_conn.php");

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch threshold values for the given sensor_id
  $stmt = $pdo->prepare("SELECT time_interval FROM sending_interval WHERE id = 1");
  $stmt->execute();

  $thresholdData = $stmt->fetch(PDO::FETCH_ASSOC);

  // Return as JSON
  if ($thresholdData) {
    echo json_encode($thresholdData);
  } else {
    echo json_encode(["error" => "No threshold data found"]);
  }
} catch (PDOException $e) {
  echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
