<?php
require_once '../db/conn.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;
$name = trim($_POST['name'] ?? '');

if ($id == 0 || $name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $name, $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario actualizado',
            'updated' => ['id' => $id, 'name' => $name]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar']);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en base de datos: ' . $e->getMessage()
    ]);
}