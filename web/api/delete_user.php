<?php
require_once '../db/conn.php';
header('Content-Type: application/json'); // Añadir cabecera JSON

$id = $_POST['id'] ?? 0;
if ($id == 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit;
}

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario eliminado correctamente',
            'deleted_id' => $id // Para referencia en frontend
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al eliminar']);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error en base de datos: ' . $e->getMessage()
    ]);
}