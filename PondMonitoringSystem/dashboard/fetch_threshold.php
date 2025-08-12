<?php
include("db_conn.php");

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Fetch threshold values for the given sensor_id
  $stmt = $pdo->prepare("SELECT wt_low, wt_high, ph_low, ph_high, do_low, do_high FROM threshold WHERE id = 1");
  $stmt->execute();

  $thresholdData = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($thresholdData) {
    // Replace NULL or empty values with "--"
    foreach ($thresholdData as $key => $value) {
      if ($value === null || $value === "") {
        $thresholdData[$key] = "--";
      }
    }
    echo json_encode($thresholdData);
  } else {
    echo json_encode([
      "wt_low"  => "--",
      "wt_high" => "--",
      "ph_low"  => "--",
      "ph_high" => "--",
      "do_low"  => "--",
      "do_high" => "--"
    ]);
  }
} catch (PDOException $e) {
  echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
