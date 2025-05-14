<?php
header('Content-Type: application/json');

require_once '../db/conn.php'; 

// Obtener los datos JSON del cuerpo de la petición
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['name'], $data['email'], $data['password'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Faltan campos requeridos']);
  exit;
}

$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_BCRYPT); // Encriptar contraseña

$sql = "INSERT INTO admins (name, email, password) VALUES ('$name', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true, 'id' => $conn->insert_id]);
} else {
  http_response_code(500);
  echo json_encode(['error' => 'Error al registrar administrador: ' . $conn->error]);
}
