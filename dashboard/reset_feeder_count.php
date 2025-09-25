<?php
header('Content-Type: application/json');
include("db_conn.php");

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Set count = reset_count for id=1
  $pdo->exec("UPDATE feeder_count SET count = reset_count WHERE id = 1");

  // Read back the updated count
  $stmt = $pdo->query("SELECT count FROM feeder_count WHERE id = 1");
  $row  = $stmt->fetch(PDO::FETCH_ASSOC);
  $newCount = isset($row['count']) ? (int)$row['count'] : 0;

  echo json_encode([
    "status" => "success",
    "message" => "Feeder updated successfully",
    "new_count" => $newCount
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    "status" => "error",
    "message" => "Connection failed: " . $e->getMessage()
  ]);
}
