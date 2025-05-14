<?php
require_once '../db/conn.php';
header('Content-Type: application/json');

$uuid = trim($_POST['uuid'] ?? '');
$name = trim($_POST['name'] ?? '');

if (empty($uuid) || empty($name)) {
    echo json_encode(['status' => 'error', 'message' => 'UUID y nombre son requeridos']);
    exit;
}

try {
    // Verificar existencia
    $check = $conn->prepare("SELECT id FROM users WHERE uuid = ?");
    $check->bind_param("s", $uuid);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Esta tarjeta ya está registrada',
            'uuid' => $uuid
        ]);
        exit;
    }
    
    // Insertar nuevo
    $stmt = $conn->prepare("INSERT INTO users (uuid, name) VALUES (?, ?)");
    $stmt->bind_param("ss", $uuid, $name);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registro exitoso',
            'inserted_id' => $stmt->insert_id
        ]);
    } else {
        throw new Exception("Error en inserción");
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al registrar: ' . $e->getMessage()
    ]);
}