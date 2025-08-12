<?php

include("db_conn.php");

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Construct the SQL query with the validated station name
  $sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1"; // Using backticks to enclose the table name
  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($data);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}
