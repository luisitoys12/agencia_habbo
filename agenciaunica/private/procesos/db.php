<?php
/**
 * db.php — Conexion a MariaDB con reintentos automaticos.
 * Solucion: Connection refused cuando MySQL aun esta arrancando.
 */

$host    = '127.0.0.1';
$db      = 'sistema_agencia';
$user    = 'agencia_user';
$pass    = 'agencia_pass2026';
$charset = 'utf8mb4';

// Suprimir warnings de mysqli para manejarlos manualmente
mysqli_report(MYSQLI_REPORT_OFF);

$conn        = null;
$max_retries = 8;
$retry_secs  = 2;

for ($i = 1; $i <= $max_retries; $i++) {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_errno === 0) {
        $conn->set_charset($charset);
        break;
    }
    $conn->close();
    $conn = null;
    if ($i < $max_retries) sleep($retry_secs);
}

if (!$conn) {
    // Pagina de espera amigable con auto-recarga
    http_response_code(503);
    header('Retry-After: 10');
    echo '<!DOCTYPE html><html lang="es"><head>';
    echo '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Iniciando...</title>';
    echo '<style>'
        .'*{box-sizing:border-box;margin:0;padding:0}'
        .'body{background:#0e1117;color:#e2e8f0;font-family:system-ui,sans-serif;'
        .'display:flex;align-items:center;justify-content:center;min-height:100vh;}'
        .'.box{text-align:center;padding:2.5rem 2rem;background:rgba(255,255,255,.04);'
        .'border:1px solid rgba(255,200,80,.15);border-radius:1.25rem;max-width:420px;width:90%;}'
        .'.spin{width:48px;height:48px;border:4px solid rgba(255,165,0,.15);'
        .'border-top-color:#fbbf24;border-radius:50%;animation:s 1s linear infinite;margin:0 auto 1.5rem;}'
        .'@keyframes s{to{transform:rotate(360deg)}}'
        .'h2{color:#fbbf24;font-size:1.4rem;margin-bottom:.75rem;}'
        .'p{color:#94a3b8;font-size:.95rem;margin-bottom:1.5rem;line-height:1.6;}'
        .'.btn{background:#f59e0b;border:none;color:#000;padding:.7rem 1.5rem;'
        .'border-radius:.6rem;cursor:pointer;font-size:1rem;font-weight:600;}'
        .'</style>';
    echo '</head><body><div class="box">';
    echo '<div class="spin"></div>';
    echo '<h2>&#9728;&#65039; Reino Hogwarz</h2>';
    echo '<p>El servidor est&aacute; iniciando.<br>Esto toma unos segundos la primera vez.</p>';
    echo '<button class="btn" onclick="location.reload()">&#8635;&nbsp;Reintentar</button>';
    echo '</div>';
    // Auto-recarga cada 8 segundos
    echo '<script>setTimeout(()=>location.reload(),8000);</script>';
    echo '</body></html>';
    exit();
}

// Reactivar reporte de errores mysqli para el resto del codigo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
