<?php
require_once '../db/conn.php';
header('Content-Type: application/json');

try {
    $result = $conn->query("SELECT id, uuid, name, registered_at FROM users ORDER BY id DESC");
    $users = [];
    
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'id' => (int)$row['id'],
            'uuid' => htmlspecialchars($row['uuid']),
            'name' => htmlspecialchars($row['name']),
            'registered_at' => $row['registered_at']
        ];
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $users,
        'count' => count($users)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al cargar usuarios: ' . $e->getMessage()
    ]);
}