<?php
include 'db.php';
$id=$_GET['id'];
$row=$conn->query("SELECT * FROM productos WHERE id=$id")->fetch_assoc();
if($_SERVER['REQUEST_METHOD']=='POST'){
    $asignado=$_POST['asignado']; $cantidad=$_POST['cantidad']; $notas=$_POST['notas'];
    $sql="UPDATE productos SET asignado='$asignado', cantidad='$cantidad', notas='$notas' WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Asignación - Sistema de Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Aplicar el mismo sistema de temas */
:root {
  --bs-body-bg: #ffffff;
  --bs-body-color: #212529;
  --bs-card-bg: #ffffff;
  --bs-border-color: #dee2e6;
}

[data-bs-theme="dark"] {
  --bs-body-bg: #0d1117;
  --bs-body-color: #e6edf3;
  --bs-card-bg: #161b22;
  --bs-border-color: #30363d;
}

body {
  background-color: var(--bs-body-bg);
  color: var(--bs-body-color);
  transition: background-color 0.3s ease, color 0.3s ease;
}

.card {
  background-color: var(--bs-card-bg);
  border-color: var(--bs-border-color);
  box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
}

.theme-toggle {
  border: none;
  background: none;
  font-size: 1.2rem;
  color: var(--bs-body-color);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a href="index.php" class="navbar-brand">
      <i class="bi bi-arrow-left"></i> Volver al Inventario
    </a>
    <button class="theme-toggle" onclick="toggleTheme()" title="Cambiar tema">
      <i class="bi bi-sun-fill" id="theme-icon"></i>
    </button>
  </div>
</nav>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h4 class="mb-0"><i class="bi bi-pencil"></i> Editar Asignación</h4>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <input type="text" value="<?= htmlspecialchars($row['nombre']) ?>" class="form-control" disabled>
            </div>
            <div class="mb-3">
              <label class="form-label">Asignado a</label>
              <input type="text" name="asignado" class="form-control" value="<?= htmlspecialchars($row['asignado']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Cantidad</label>
              <input type="number" name="cantidad" class="form-control" value="<?= $row['cantidad'] ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Notas</label>
              <textarea name="notas" class="form-control" rows="3"><?= htmlspecialchars($row['notas']) ?></textarea>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Guardar Cambios
              </button>
              <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function getPreferredTheme() {
  const storedTheme = localStorage.getItem('theme');
  if (storedTheme) {
    return storedTheme;
  }
  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

function setTheme(theme) {
  document.documentElement.setAttribute('data-bs-theme', theme);
  localStorage.setItem('theme', theme);
  updateThemeIcon(theme);
}

function updateThemeIcon(theme) {
  const icon = document.getElementById('theme-icon');
  if (theme === 'dark') {
    icon.className = 'bi bi-moon-fill';
  } else {
    icon.className = 'bi bi-sun-fill';
  }
}

function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-bs-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  setTheme(newTheme);
}

document.addEventListener('DOMContentLoaded', function() {
  setTheme(getPreferredTheme());
});
</script>

</body>
</html>
