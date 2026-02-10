<?php
include 'db.php';
$buscar = "";
$asigFiltro = "";

if (!empty($_GET['buscar'])) {
    $buscar = $conn->real_escape_string($_GET['buscar']);
    $sql = "SELECT p.* FROM productos p
            LEFT JOIN asignaciones a ON p.id = a.producto_id
            WHERE p.nombre LIKE '%$buscar%'
               OR p.marca LIKE '%$buscar%'
               OR p.modelo LIKE '%$buscar%'
               OR p.numero_serie LIKE '%$buscar%'
               OR a.asignado LIKE '%$buscar%'
            GROUP BY p.id
            ORDER BY p.id DESC";

    $asigFiltro = $buscar;
} else {
    $sql = "SELECT * FROM productos ORDER BY id DESC";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>📦 Sistema de Inventario</title>
<link href="./assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="./icons/bootstrap-icons.css">

<style>
/* Sistema de temas moderno */
:root {
  --bs-body-bg: #ffffff;
  --bs-body-color: #212529;
  --bs-primary: #0d6efd;
  --bs-secondary: #6c757d;
  --bs-success: #198754;
  --bs-danger: #dc3545;
  --bs-warning: #ffc107;
  --bs-info: #0dcaf0;
  --bs-card-bg: #ffffff;
  --bs-border-color: #dee2e6;
  --bs-table-bg: transparent;
  --bs-table-hover-bg: rgba(0,0,0,0.075);
}

[data-bs-theme="dark"] {
  --bs-body-bg: #0d1117;
  --bs-body-color: #e6edf3;
  --bs-primary: #58a6ff;
  --bs-secondary: #8b949e;
  --bs-success: #3fb950;
  --bs-danger: #f85149;
  --bs-warning: #d29922;
  --bs-info: #79c0ff;
  --bs-card-bg: #161b22;
  --bs-border-color: #30363d;
  --bs-table-bg: transparent;
  --bs-table-hover-bg: rgba(255,255,255,0.075);
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

.table {
  --bs-table-bg: var(--bs-table-bg);
  --bs-table-hover-bg: var(--bs-table-hover-bg);
}

.navbar {
  background-color: var(--bs-card-bg) !important;
  border-bottom: 1px solid var(--bs-border-color);
  backdrop-filter: blur(10px);
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

.theme-toggle:hover {
  background-color: var(--bs-table-hover-bg);
}

.status-badge {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
}

.action-buttons .btn {
  margin: 0 0.125rem;
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.search-container {
  background-color: var(--bs-card-bg);
  border-radius: 0.5rem;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 1px solid var(--bs-border-color);
}

.stats-card {
  background: linear-gradient(135deg, var(--bs-primary), var(--bs-info));
  color: white;
  border: none;
}

tr.asignado {
  background-color: rgba(var(--bs-success-rgb), 0.1);
  border-left: 3px solid var(--bs-success);
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <span class="navbar-brand mb-0 h1">
      <i class="bi bi-boxes"></i> Sistema de Inventario
    </span>
    <div class="d-flex align-items-center">
      <button class="theme-toggle me-2" onclick="toggleTheme()" title="Cambiar tema">
        <i class="bi bi-sun-fill" id="theme-icon"></i>
      </button>
      <span class="badge bg-primary">v2.0</span>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card stats-card">
        <div class="card-body text-center">
          <h3 class="mb-0"><?= $result->num_rows ?></h3>
          <small>Productos Total</small>
        </div>
      </div>
    </div>
  </div>

  <div class="search-container">
    <form class="row g-3" method="GET" id="searchForm">
      <div class="col-md-8">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="buscar" id="searchInput" class="form-control" 
                 placeholder="Buscar por nombre, marca, modelo, serie o asignado..." 
                 value="<?= $buscar ?>" autocomplete="off">
          <div id="searchResults" class="position-absolute w-100 border rounded-bottom shadow-lg" style="top: 100%; z-index: 1000; display: none; max-height: 300px; overflow-y: auto; background-color: var(--bs-card-bg);"></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-search"></i> Buscar
          </button>
          <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Reset
          </a>
        </div>
      </div>
    </form>
  </div>

  <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="agregar_producto.php" class="btn btn-success">
      <i class="bi bi-plus-circle"></i> Agregar Producto
    </a>
    <a href="reporte.php?buscar=<?= urlencode($asigFiltro) ?>" class="btn btn-warning" target="_blank">
      <i class="bi bi-file-earmark-pdf"></i> Generar PDF
    </a>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Producto</th>
              <th>Detalles</th>
              <th>Stock</th>
              <th>Asignaciones</th>
              <th>Notas</th>
              <th>Fechas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row=$result->fetch_assoc()): ?>
            <tr class="<?= ($conn->query("SELECT COUNT(*) FROM asignaciones WHERE producto_id=".$row['id'])->fetch_row()[0]>0) ? 'asignado' : '' ?>">
              <td><span class="badge bg-secondary"><?= $row['id'] ?></span></td>
              <td>
                <strong><?= $row['nombre'] ?></strong>
              </td>
              <td>
                <small class="text-muted">
                  <strong>Marca:</strong> <?= $row['marca'] ?><br>
                  <strong>Modelo:</strong> <?= $row['modelo'] ?><br>
                  <strong>Serie:</strong> <?= $row['numero_serie'] ?>
                </small>
              </td>
              <td>
                <span class="badge <?= $row['cantidad'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                  <?= $row['cantidad'] ?>
                </span>
              </td>
              <td>
                <?php
                $asigs = $conn->query("SELECT id, asignado, cantidad FROM asignaciones WHERE producto_id=".$row['id']);
                while($a = $asigs->fetch_assoc()){
                    if($asigFiltro != "" && stripos($a['asignado'], $asigFiltro) === false) continue;
                    echo "<div class='d-flex justify-content-between align-items-center mb-1'>";
                    echo "<span class='badge bg-info'>{$a['asignado']} ({$a['cantidad']})</span>";
                    echo "<a href='eliminar_asignacion.php?id={$a['id']}' onclick=\"return confirm('¿Quitar esta asignación?')\" class='btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></a>";
                    echo "</div>";
                }
                ?>
              </td>
              <td>
                <small><?= $row['notas'] ?></small>
              </td>
              <td>
                <small class="text-muted">
                  <strong>Creado:</strong> <?= $row['fecha_registro'] ?><br>
                  <strong>Modificado:</strong> <?= $row['fecha_modificacion'] ?>
                </small>
              </td>
              <td>
                <div class="action-buttons">
                  <a href="editar_producto.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="eliminar_producto.php?id=<?= $row['id'] ?>" 
                     onclick="return confirm('¿Eliminar producto?')" 
                     class="btn btn-sm btn-outline-danger" title="Eliminar">
                    <i class="bi bi-trash"></i>
                  </a>
                  <a href="asignar_producto.php?id=<?= $row['id'] ?>" 
                     class="btn btn-sm btn-outline-info" title="Asignar">
                    <i class="bi bi-person-plus"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<footer class="mt-auto py-4 border-top position-sticky bottom-0 bg-body">
  <div class="container">
    <div class="text-center">
      <p class="mb-0 text-muted">
        &copy; <?= date('Y') ?> infernocore | Algunos derechos reservados
      </p>
    </div>
  </div>
</footer>

<script src="./assets/js/bootstrap.bundle.min.js"></script>

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

function selectSearchResult(searchValue) {
  document.getElementById('searchInput').value = searchValue;
  document.getElementById('searchResults').style.display = 'none';
  document.getElementById('searchForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
  setTheme(getPreferredTheme());
  
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
    if (!localStorage.getItem('theme')) {
      setTheme(e.matches ? 'dark' : 'light');
    }
  });

  // Live search functionality
  const searchInput = document.getElementById('searchInput');
  const searchResults = document.getElementById('searchResults');
  let searchTimeout;

  searchInput.addEventListener('input', function() {
    const query = this.value.trim();
    
    clearTimeout(searchTimeout);
    
    if (query.length < 2) {
      searchResults.style.display = 'none';
      return;
    }

    searchTimeout = setTimeout(() => {
      fetch(`search_live.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
          console.log('[v0] Search results:', data); // Debug log
          if (data.length > 0) {
            let html = '';
            data.forEach(item => {
              const nombre = item.nombre && item.nombre.trim() !== '' ? item.nombre : 'Producto sin nombre';
              const marca = item.marca && item.marca.trim() !== '' ? item.marca : 'Sin marca';
              const modelo = item.modelo && item.modelo.trim() !== '' ? item.modelo : 'Sin modelo';
              const serie = item.numero_serie && item.numero_serie.trim() !== '' ? item.numero_serie : '';
              const asignaciones = item.asignaciones && item.asignaciones.trim() !== '' ? item.asignaciones : '';
              
              html += `
                <div class="p-2 border-bottom">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <div class="search-result-item mb-1" style="cursor: pointer; color: var(--bs-body-color);" onclick="selectSearchResult('${nombre.replace(/'/g, "\\'")}')">
                        <strong>${nombre}</strong> <small class="text-muted">(por nombre)</small>
                      </div>
                      ${marca !== 'Sin marca' ? `
                        <div class="search-result-item mb-1" style="cursor: pointer; color: var(--bs-body-color);" onclick="selectSearchResult('${marca.replace(/'/g, "\\'")}')">
                          <small class="text-muted">Marca: ${marca}</small>
                        </div>
                      ` : ''}
                      ${modelo !== 'Sin modelo' ? `
                        <div class="search-result-item mb-1" style="cursor: pointer; color: var(--bs-body-color);" onclick="selectSearchResult('${modelo.replace(/'/g, "\\'")}')">
                          <small class="text-muted">Modelo: ${modelo}</small>
                        </div>
                      ` : ''}
                      ${serie ? `
                        <div class="search-result-item mb-1" style="cursor: pointer; color: var(--bs-body-color);" onclick="selectSearchResult('${serie.replace(/'/g, "\\'")}')">
                          <small class="text-muted">Serie: ${serie}</small>
                        </div>
                      ` : ''}
                      ${asignaciones ? `
                        <div class="search-result-item mb-1" style="cursor: pointer; color: var(--bs-body-color);" onclick="selectSearchResult('${asignaciones.replace(/'/g, "\\'")}')">
                          <small class="text-info">Asignado a: ${asignaciones}</small>
                        </div>
                      ` : ''}
                    </div>
                    <span class="badge bg-secondary">#${item.id}</span>
                  </div>
                </div>
              `;
            });
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
          } else {
            searchResults.style.display = 'none';
          }
        })
        .catch(error => {
          console.error('Error:', error);
          searchResults.style.display = 'none';
        });
    }, 300);
  });

  // Hide results when clicking outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
      searchResults.style.display = 'none';
    }
  });
});
</script>

</body>
</html>
