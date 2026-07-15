<?php
/**
 * index.php v3 — Hub completo del panel.
 * Mapa de secciones seguro. Sin ?page → HOM (dashboard).
 */
require_once(__DIR__ . '/../private/plantillas/header.php');
?>
<?php
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
    'SAN'    => __DIR__ . '/../private/procesos/sanciones.php',
];

if (array_key_exists($page, $secciones) && file_exists($secciones[$page])) {
    include $secciones[$page];
} else {
    include __DIR__ . '/FO.php';
}
?>
<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
