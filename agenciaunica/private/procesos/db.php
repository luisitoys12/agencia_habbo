<?php
/**
 * db.php - Conexion a MariaDB con reintentos automaticos
 * Resuelve: Connection refused cuando MySQL aun esta arrancando
 */

$host    = '127.0.0.1';
$db      = 'sistema_agencia';
$user    = 'agencia_user';
$pass    = 'agencia_pass2026';
$charset = 'utf8mb4';

$conn        = null;
$max_retries = 10;   // intentos maximos
$retry_delay = 1;    // segundos entre intentos

for ($i = 1; $i <= $max_retries; $i++) {
    try {
        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_errno === 0) {
            $conn->set_charset($charset);
            break; // conexion exitosa
        }

        $conn = null;
    } catch (Exception $e) {
        $conn = null;
    }

    if ($i < $max_retries) {
        sleep($retry_delay);
    }
}

if ($conn === null || $conn->connect_errno !== 0) {
    // Mostrar pagina de error amigable en lugar de fatal error
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Iniciando servidor...</title>';
    echo '<style>'
       . 'body{background:#0a1628;color:#e2e8f0;font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;}'
       . '.box{text-align:center;padding:2rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,200,80,.2);border-radius:1rem;max-width:400px;}'
       . 'h2{color:#fbbf24;margin-bottom:1rem;}'
       . 'p{color:#94a3b8;margin-bottom:1.5rem;}'
       . '.spinner{width:40px;height:40px;border:4px solid rgba(255,165,0,.2);border-top-color:#fbbf24;border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 1rem;}'
       . '@keyframes spin{to{transform:rotate(360deg)}}'
       . 'button{background:linear-gradient(135deg,#f59e0b,#ef4444);border:none;color:#fff;padding:.75rem 1.5rem;border-radius:.5rem;cursor:pointer;font-size:1rem;}'
       . '</style>';
    echo '</head><body><div class="box">';
    echo '<div class="spinner"></div>';
    echo '<h2>&#9728;&#65039; Reino Hogwarz</h2>';
    echo '<p>El servidor est&aacute; iniciando. Esto toma unos segundos la primera vez.</p>';
    echo '<button onclick="location.reload()">&#8635; Reintentar</button>';
    echo '</div><script>setTimeout(()=>location.reload(),5000);</script>';
    echo '</body></html>';
    exit();
}
?>
