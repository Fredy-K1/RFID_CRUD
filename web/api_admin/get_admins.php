<?php
header('Content-Type: application/json');
require_once '../db/conn.php';

$sql = "SELECT id, name, email FROM admins ORDER BY id DESC";
$result = $conn->query($sql);

if (!$result) {
  http_response_code(500);
  echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
  exit;
}

$admins = [];
while ($row = $result->fetch_assoc()) {
  $admins[] = $row;
}

echo json_encode($admins);

$conn->close();
?>
