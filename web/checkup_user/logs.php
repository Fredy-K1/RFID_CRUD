<?php require_once '../db/conn.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Accesos</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body class="bg-light">
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">📋 Registro de Entradas y Salidas</h2>
      <a href="../index.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-circle"></i> Volver
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-bordered table-hover table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Dirección</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody id="log-body">
          <!-- Contenido inicial se carga una vez -->
          <?php
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
        </tbody>
      </table>
    </div>
  </div>

  <!-- Script para actualizar tabla automáticamente -->
  <script>
    function actualizarLogs() {
      fetch('fetch_logs.php')
        .then(response => response.text())
        .then(data => {
          document.getElementById('log-body').innerHTML = data;
        })
        .catch(error => console.error('Error al cargar logs:', error));
    }

    // Cargar logs cada 3 segundos
    setInterval(actualizarLogs, 3000);
    actualizarLogs(); // Llamada inicial
  </script>
</body>
</html>
