<?php
session_start();
if (isset($_SESSION['admin_id'])) {
  header('Location: ../index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div class="card p-4 shadow" style="width: 350px;">
    <h3 class="mb-4 text-center"><i class="bi bi-person-lock"></i> Iniciar sesión</h3>
    <form id="loginForm">
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required autofocus />
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required />
      </div>
      <div id="loginMsg" class="mb-3 text-danger small text-center"></div>
      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-box-arrow-in-right"></i> Entrar
      </button>
    </form>
  </div>

  <script src="../assets/js/login.js"></script>
</body>
</html>
