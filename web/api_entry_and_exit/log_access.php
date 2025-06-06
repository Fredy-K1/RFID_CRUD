<?php
require_once '../db/conn.php';
header('Content-Type: application/json');

$uuid = trim($_POST['uuid'] ?? '');

if (empty($uuid)) {
    echo json_encode(['status' => 'error', 'message' => 'UUID requerido']);
    exit;
}

try {
    // Buscar usuario
    $stmt = $conn->prepare("SELECT id FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Tarjeta no registrada']);
        exit;
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Buscar último registro
    $logStmt = $conn->prepare("SELECT direction FROM user_logs WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1");
    $logStmt->bind_param("i", $userId);
    $logStmt->execute();
    $logResult = $logStmt->get_result();

    $newDirection = "entrada";
    if ($logResult->num_rows > 0) {
        $lastDirection = $logResult->fetch_assoc()['direction'];
        $newDirection = $lastDirection === 'entrada' ? 'salida' : 'entrada';
    }

    // Insertar nuevo log
    $insertStmt = $conn->prepare("INSERT INTO user_logs (user_id, direction) VALUES (?, ?)");
    $insertStmt->bind_param("is", $userId, $newDirection);
    $insertStmt->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Registro guardado como ' . $newDirection,
        'direction' => $newDirection
    ]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
