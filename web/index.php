<?php
require_once 'auth_admin/sessions.php';
checkAdminSession();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Gestión de Usuarios RFID</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/darkmode.css" rel="stylesheet">
  <link href="assets/css/animations.css" rel="stylesheet"> 
</head>
<body class="bg-light text-dark">

  <div class="container py-5">

    <!-- Mensaje de bienvenida -->
    <?php if (isset($_SESSION['admin_name']) && empty($_SESSION['welcome_shown'])): ?>
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" id="welcomeAlert">
        <i class="bi bi-hand-thumbs-up me-2"></i>
        ¡Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong>! Has iniciado sesión como administrador.
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Cerrar"></button>
      </div>
      <?php $_SESSION['welcome_shown'] = true; ?>
    <?php endif; ?>


    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2><i class="bi bi-person-badge"></i> Gestión de Usuarios RFID</h2>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adminModal">
          <i class="bi bi-person-gear"></i> Agregar Administrador
        </button>
        <a href="checkup_user/logs.php" class="btn btn-outline-info">
          <i class="bi bi-clock-history"></i> Registro de Accesos
        </a>
        <button class="btn btn-outline-dark" onclick="toggleDarkMode()">
          <i class="bi bi-moon-fill"></i> Modo Oscuro/Claro
        </button>
        <a href="auth_admin/logout.php" class="btn btn-outline-danger">
          <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
      </div>
    </div>


    <!-- Alertas -->
    <div id="alertContainer"></div>

    <!-- Formulario de Registro -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">
        <i class="bi bi-person-plus-fill"></i> Registrar Usuario
      </div>
      <div class="card-body">
        <form id="registerForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Nombre completo</label>
              <input type="text" class="form-control" id="name" name="name" required />
            </div>
            <div class="col-md-6">
              <label for="uuid" class="form-label">UUID (desde ESP32)</label>
              <input type="text" class="form-control" id="uuidInput" name="uuid" readonly placeholder="Esperando lectura..." />
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-save"></i> Registrar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabla de Usuarios Registrados -->
    <div class="card shadow-sm">
      <div class="card-header bg-dark text-white">
        <i class="bi bi-people-fill"></i> Usuarios Registrados
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered align-middle" id="usersTable">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>UUID</th>
                <th>Nombre</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="usersBody"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Administrador -->
  <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content shadow">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="adminModalLabel"><i class="bi bi-person-gear"></i> Gestión de Administradores</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Formulario de administrador -->
          <form id="adminForm">
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label for="adminName" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="adminName" required>
              </div>
              <div class="col-md-4">
                <label for="adminEmail" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="adminEmail" required>
              </div>
              <div class="col-md-4">
                <label for="adminPassword" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="adminPassword">
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-check-circle"></i> Guardar Administrador
                </button>
              </div>
            </div>
          </form>

          <!-- Tabla de administradores -->
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Email</th>
                  <th>Contraseña</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody id="adminTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar administrador -->
  <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content shadow">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title" id="editAdminLabel"><i class="bi bi-pencil-square"></i> Editar Administrador</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <!-- Formulario de edición de administrador -->
          <form id="editAdminForm">
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <label for="editAdminName" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="editAdminName" required>
              </div>
              <div class="col-md-4">
                <label for="editAdminEmail" class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="editAdminEmail" required>
              </div>
              <div class="col-md-4">
                <label for="editAdminPassword" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="editAdminPassword">
              </div>
              <div class="col-12 text-end">
                <button type="submit" class="btn btn-warning">
                  <i class="bi bi-pencil-square"></i> Guardar Cambios
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

    <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/main.js?v=1.0.1"></script>
  <script src="assets/js/admin.js?v=1.0.1"></script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const alertBox = document.querySelector('#welcomeAlert');
      if (alertBox) {
        setTimeout(() => {
          alertBox.classList.add('fade-out');
          setTimeout(() => alertBox.remove(), 1000);
        }, 5000); // Desaparece tras 5 segundos
      }
    });
  </script>

  </body>

  </html>