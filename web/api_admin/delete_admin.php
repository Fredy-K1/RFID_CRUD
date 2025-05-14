<?php
header('Content-Type: application/json');
require_once '../db/conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
  http_response_code(400);
  echo json_encode(['error' => 'ID no especificado']);
  exit;
}

$id = intval($data['id']);

$sql = "DELETE FROM admins WHERE id = $id";

if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Error al eliminar: ' . $conn->error]);
}
