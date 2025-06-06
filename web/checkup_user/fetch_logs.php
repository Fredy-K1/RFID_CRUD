<?php
require_once '../db/conn.php';

$sql = "SELECT ul.id, u.name, ul.direction, ul.timestamp 
        FROM user_logs ul
        JOIN users u ON ul.user_id = u.id
        ORDER BY ul.timestamp DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()):
?>
<tr>
  <td><?= $row['id'] ?></td>
  <td><?= htmlspecialchars($row['name']) ?></td>
  <td>
    <?= $row['direction'] === 'entrada'
        ? '<span class="text-success"><i class="bi bi-box-arrow-in-right"></i> Entrada</span>'
        : '<span class="text-danger"><i class="bi bi-box-arrow-left"></i> Salida</span>' ?>
  </td>
  <td><?= $row['timestamp'] ?></td>
</tr>
<?php endwhile; ?>
