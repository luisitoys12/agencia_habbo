<?php
/**
 * index.php — Hub principal del panel de agencia.
 * Carga el header (que verifica sesión), luego sirve la sección pedida.
 */
require_once(__DIR__ . '/../private/plantillas/header.php');
?>

<?php
// Sección activa (por defecto: HOME)
$page = $_GET['page'] ?? 'HOM';

// Mapa de secciones permitidas
$secciones = [
    'HOM' => __DIR__ . '/FO.php',
    'RAG' => __DIR__ . '/RAG.php',
    'GSU' => __DIR__ . '/GSU.php',
    'GSP' => __DIR__ . '/GSP.php',
    'GVE' => __DIR__ . '/GVE.php',
    'GVP' => __DIR__ . '/GVP.php',
    'PERFIL' => __DIR__ . '/../private/procesos/perfil.php',
];

if (array_key_exists($page, $secciones) && file_exists($secciones[$page])) {
    include $secciones[$page];
} else {
    // Fallback: mostrar home si la página no existe
    include __DIR__ . '/FO.php';
}
?>

<?php require_once(__DIR__ . '/../private/plantillas/footer.php'); ?>
