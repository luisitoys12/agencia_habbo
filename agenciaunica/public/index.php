<?php
/**
 * index.php — Punto de entrada principal.
 * Sin sesion -> redirige a login.
 * Con sesion -> carga el hub del panel.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Guard: si no hay sesion activa, ir a login
if (!isset($_SESSION['rol_id'])) {
    header('Location: /login.php');
    exit();
}

require_once(__DIR__ . '/../private/plantillas/header.php');

$page = $_GET['page'] ?? 'HOM';

$secciones = [
    'HOM'    => __DIR__ . '/FO.php',
    'RAG'    => __DIR__ . '/RAG.php',
    'GSU'    => __DIR__ . '/GSU.php',
    'GSP'    => __DIR__ . '/GSP.php',
    'GVE'    => __DIR__ . '/GVE.php',
    'GVP'    => __DIR__ . '/GVP.php',
    'DJ'     => __DIR__ . '/dj.php',
    'PERFIL' => __DIR__ . '/../private/procesos/perfil.php',
    'NOT'    => __DIR__ . '/../private/procesos/noticias.php',
    'SAN'    => __DIR__ . '/../private/panel/sanciones.php',
    'STAFF'  => __DIR__ . '/../private/panel/staff.php',
    'USR'    => __DIR__ . '/../private/panel/usuarios.php',
];

if (array_key_exists($page, $secciones) && file_exists($secciones[$page])) {
    include $secciones[$page];
} else {
    include __DIR__ . '/FO.php';
}
?>
<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
