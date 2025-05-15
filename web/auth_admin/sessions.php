<?php
session_start();

function checkAdminSession() {
  if (!isset($_SESSION['admin_id'])) {
    header("Location: auth_admin/login.php");
    exit;
  }
}
