<?php
require_once("../db/conn.php");

$uuid = $_GET['uuid'] ?? '';
$direction = $_GET['direction'] ?? ''; // entrada o salida

if ($uuid && in_array($direction, ['entrada', 'salida'])) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE uuid = ?");
    $stmt->bind_param("s", $uuid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_id = $row['id'];

        $insert = $conn->prepare("INSERT INTO user_logs (user_id, direction) VALUES (?, ?)");
        $insert->bind_param("is", $user_id, $direction);
        $insert->execute();

        echo "Registro guardado";
    } else {
        echo "Usuario no encontrado";
    }
} else {
    echo "Parámetros inválidos";
}
?>
