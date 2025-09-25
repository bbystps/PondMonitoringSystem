<?php
include("db_conn.php");

try {
  // Establish database connection
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
  $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

  // If start_date and end_date are not provided, set them to the last 24 hours range
  if (empty($start_date) || empty($end_date)) {
    $end_date = date('Y-m-d H:i:s'); // Current timestamp
    $start_date = date('Y-m-d H:i:s', strtotime('-24 hours'));
  }

  // Construct the SQL query
  $sql = "SELECT sensor, `value`, `status`, `timestamp` 
        FROM `threshold_notif` 
        WHERE `timestamp` BETWEEN :start_date AND :end_date
        ORDER BY `timestamp` ASC";

  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':start_date', $start_date);
  $stmt->bindParam(':end_date', $end_date);

  $stmt->execute();

  // Fetch data and return as JSON
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($data);
} catch (PDOException $e) {
  echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
