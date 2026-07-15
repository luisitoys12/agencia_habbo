<?php
/**
 * panel.php — Hub del panel autenticado.
 * Requiere sesión activa. Sin sesión redirige a login.
 */
if (session_status() === PHP_SESSION_NONE) session_start();

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
    'ADMIN'  => __DIR__ . '/../private/panel/index.php',
    'INICIO' => __DIR__ . '/../private/panel/inicio.php',
];

if (array_key_exists($page, $secciones) && file_exists($secciones[$page])) {
    include $secciones[$page];
} else {
    include __DIR__ . '/FO.php';
}
?>
<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
