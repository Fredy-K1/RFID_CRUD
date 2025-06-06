<?php
session_start();
header('Content-Type: application/json');
require_once '../db/conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
  exit;
}

$email = $conn->real_escape_string($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
  echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
  exit;
}

$sql = "SELECT id, name, password FROM admins WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows === 1) {
  $admin = $result->fetch_assoc();
  if (password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['welcome_shown'] = false; 
    echo json_encode(['success' => true]);
    exit;
  }
}

echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos.']);
