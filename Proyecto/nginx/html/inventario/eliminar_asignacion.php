<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt_get = $conn->prepare("SELECT producto_id, cantidad FROM asignaciones WHERE id = ?");
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result = $stmt_get->get_result();
    
    if ($result->num_rows > 0) {
        $asignacion = $result->fetch_assoc();
        $producto_id = $asignacion['producto_id'];
        $cantidad_asignada = $asignacion['cantidad'];
        
        // Start transaction to ensure both operations complete
        $conn->autocommit(FALSE);
        
        try {
            $stmt_update = $conn->prepare("UPDATE productos SET cantidad = cantidad + ? WHERE id = ?");
            $stmt_update->bind_param("ii", $cantidad_asignada, $producto_id);
            if (!$stmt_update->execute()) {
                throw new Exception("Error al actualizar el stock");
            }
            
            $stmt_delete = $conn->prepare("DELETE FROM asignaciones WHERE id = ?");
            $stmt_delete->bind_param("i", $id);
            if (!$stmt_delete->execute()) {
                throw new Exception("Error al eliminar la asignación");
            }
            
            // Commit transaction
            $conn->commit();
            header("Location: index.php?mensaje=Asignación eliminada y stock actualizado correctamente");
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            header("Location: index.php?error=Error: " . $e->getMessage());
        }
        
        // Restore autocommit
        $conn->autocommit(TRUE);
        
    } else {
        header("Location: index.php?error=Asignación no encontrada");
    }
} else {
    header("Location: index.php?error=ID de asignación no válido");
}

$conn->close();
?>
