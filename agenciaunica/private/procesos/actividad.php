<?php
/**
 * actividad.php — Registra una acción en el feed global de actividad.
 * Uso: registrar_actividad($conn, $id_usuario, 'Ascendió a NickHabbo al rango X');
 */
function registrar_actividad($conn, $id_usuario, $descripcion) {
    $stmt = $conn->prepare(
        'INSERT INTO actividad_reciente (id_usuario, descripcion, fecha)
         VALUES (?, ?, NOW())'
    );
    if ($stmt) {
        $stmt->bind_param('is', $id_usuario, $descripcion);
        $stmt->execute();
        $stmt->close();
    }
}
