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
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'ID inválido']);
  exit;
}

$sql = "DELETE FROM admins WHERE id = $id";

if ($conn->query($sql) === TRUE) {
  if ($conn->affected_rows > 0) {
    echo json_encode(['success' => true]);
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'Administrador no encontrado']);
  }
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Error al eliminar: ' . $conn->error]);
}

$conn->close();
?>
