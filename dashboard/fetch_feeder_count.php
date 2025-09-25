<?php
include("db_conn.php");

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch threshold values for the given sensor_id
  $stmt = $pdo->prepare("SELECT reset_count FROM feeder_count WHERE id = 1");
  $stmt->execute();

  $feederData = $stmt->fetch(PDO::FETCH_ASSOC);

  // Return as JSON
  if ($feederData) {
    echo json_encode($feederData);
  } else {
    echo json_encode(["error" => "No feeder data found"]);
  }
} catch (PDOException $e) {
  echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
