<?php
header('Content-Type: application/json');
require_once '../db/conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'], $data['name'], $data['email'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Faltan campos requeridos']);
  exit;
}

$id = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);

// Validación sencilla del email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['error' => 'Email no válido']);
  exit;
}

$sql = "UPDATE admins SET name='$name', email='$email'";

// Si se proporciona una nueva contraseña
if (!empty($data['password'])) {
  $password = password_hash($data['password'], PASSWORD_BCRYPT);
  $sql .= ", password='$password'";
}

$sql .= " WHERE id=$id";

if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Error al actualizar: ' . $conn->error]);
}

$conn->close();
?>
