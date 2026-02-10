<?php
include 'db.php';
$id = $_GET['id'];

// Borrar producto y todas sus asignaciones automáticamente (por clave foránea)
$conn->query("DELETE FROM productos WHERE id=$id");

header("Location: index.php");
?>
