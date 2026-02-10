<?php
include 'db.php';

header('Content-Type: application/json');
$conn->set_charset("utf8mb4");

$query = '';
if (isset($_GET['q'])) {
    $query = $conn->real_escape_string($_GET['q']);
}

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT p.id, 
               COALESCE(p.nombre, '') as nombre,
               COALESCE(p.marca, '') as marca, 
               COALESCE(p.modelo, '') as modelo,
               COALESCE(p.numero_serie, '') as numero_serie,
               GROUP_CONCAT(DISTINCT COALESCE(a.asignado, '') SEPARATOR ', ') as asignaciones
        FROM productos p
        LEFT JOIN asignaciones a ON p.id = a.producto_id
        WHERE (p.nombre IS NOT NULL AND p.nombre LIKE '%$query%')
           OR (p.marca IS NOT NULL AND p.marca LIKE '%$query%')
           OR (p.modelo IS NOT NULL AND p.modelo LIKE '%$query%')
           OR (p.numero_serie IS NOT NULL AND p.numero_serie LIKE '%$query%')
           OR (a.asignado IS NOT NULL AND a.asignado LIKE '%$query%')
        GROUP BY p.id, p.nombre, p.marca, p.modelo, p.numero_serie
        ORDER BY p.nombre ASC
        LIMIT 10";

$result = $conn->query($sql);
$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre'],
            'marca' => $row['marca'],
            'modelo' => $row['modelo'],
            'numero_serie' => $row['numero_serie'],
            'asignaciones' => $row['asignaciones']
        ];
    }
}

echo json_encode($products);
?>
