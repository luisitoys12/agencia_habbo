<?php
/**
 * buscar_usuario.php — Búsqueda AJAX de usuarios para autocompletar formularios.
 * Uso: GET /agenciaunica/buscar_usuario.php?q=NickParcial
 * Responde JSON: [{id, nombre_usuario, rango, creditos, avatar_url}]
 */
session_start();
require_once('private/procesos/db.php');

header('Content-Type: application/json; charset=utf-8');

// Solo usuarios logueados pueden buscar (y con rol adecuado si se desea)
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

$q = trim($_GET['q'] ?? '');

if (mb_strlen($q) < 2) {
    echo json_encode([]);
    exit;
}

$q_safe = $conn->real_escape_string($q);

$sql = "SELECT u.id, u.nombre_usuario,
               COALESCE(r.rango, 'Sin rango') AS rango,
               COALESCE(d.creditos, 0)         AS creditos
        FROM registro_usuario u
        LEFT JOIN rangos r       ON u.Rango_asignado = r.id_rango
        LEFT JOIN dinero_digital d ON d.id_usuario    = u.id
        WHERE u.nombre_usuario LIKE '%{$q_safe}%'
        ORDER BY u.nombre_usuario ASC
        LIMIT 10";

$result = $conn->query($sql);
$usuarios = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = [
            'id'            => (int) $row['id'],
            'nombre_usuario'=> $row['nombre_usuario'],
            'rango'         => $row['rango'],
            'creditos'      => (int) $row['creditos'],
            'avatar_url'    => 'https://www.habbo.es/habbo-imaging/avatarimage?user=' . urlencode($row['nombre_usuario']) . '&size=s',
        ];
    }
}

echo json_encode($usuarios);
