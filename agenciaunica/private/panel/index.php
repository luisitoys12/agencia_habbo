<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php');
    exit;
}

// --- Stats ---
$total_noticias    = $conn->query("SELECT COUNT(*) as t FROM publicaciones")->fetch_assoc()['t'] ?? 0;
$total_notifs      = $conn->query("SELECT COUNT(*) as t FROM notificaciones")->fetch_assoc()['t'] ?? 0;
$total_usuarios    = $conn->query("SELECT COUNT(*) as t FROM registro_usuario")->fetch_assoc()['t'] ?? 0;
$notifs_no_leidas  = $conn->query("SELECT COUNT(*) as t FROM notificaciones WHERE leida = 0")->fetch_assoc()['t'] ?? 0;
$total_sanciones   = $conn->query("SELECT COUNT(*) as t FROM sanciones WHERE activa=1")->fetch_assoc()['t'] ?? 0;
$total_staff       = $conn->query("SELECT COUNT(*) as t FROM modificar_administradores")->fetch_assoc()['t'] ?? 0;

// Últimas 6 actividades globales
$actividad = [];
$ra = $conn->query('SELECT a.descripcion, a.fecha, u.nombre_usuario FROM actividad_reciente a LEFT JOIN registro_usuario u ON a.id_usuario=u.id ORDER BY a.fecha DESC LIMIT 6');
if ($ra) while($row=$ra->fetch_assoc()) $actividad[] = $row;

// Últimas 5 noticias
$ultimas_noticias = [];
$rn = $conn->query('SELECT titulo, autor, fecha FROM publicaciones ORDER BY fecha DESC LIMIT 5');
if ($rn) while($row=$rn->fetch_assoc()) $ultimas_noticias[] = $row;

// Usuarios más recientes
$ultimos_usuarios = [];
$ru = $conn->query('SELECT nombre_usuario, fecha_registro FROM registro_usuario ORDER BY id DESC LIMIT 5');
if ($ru) while($row=$ru->fetch_assoc()) $ultimos_usuarios[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin — Reino Hogwarz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/private/assets/css/icons.css">
    <link rel="stylesheet" href="/private/assets/css/app-style.css">
    <link rel="stylesheet" href="/private/assets/css/neon.css">
    <style>
        .stat-card{background:linear-gradient(135deg,rgba(255,255,255,.05),rgba(255,255,255,.02));border:1px solid rgba(255,200,80,.2);border-radius:1rem;padding:1.5rem;text-align:center;transition:.3s;cursor:pointer;text-decoration:none;display:block;}
        .stat-card:hover{border-color:rgba(255,200,80,.5);transform:translateY(-3px);box-shadow:0 8px 30px rgba(255,200,80,.1);}
        .stat-card h2{font-size:2.5rem;font-weight:900;margin:0;}
        .stat-card p{font-size:.82rem;opacity:.65;margin:0;}
        .panel-nav{display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;}
        .panel-nav a{padding:.6rem 1.2rem;border-radius:.5rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,200,80,.2);color:#e2e8f0;text-decoration:none;font-size:.9rem;transition:.2s;}
        .panel-nav a:hover,.panel-nav a.active{background:rgba(255,165,0,.2);border-color:rgba(255,165,0,.5);color:#fbbf24;}
        .side-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,200,80,.15);border-radius:.75rem;padding:1.25rem;}
        .side-card h6{color:#fbbf24;font-size:.85rem;margin-bottom:.85rem;}
        .side-list-item{display:flex;justify-content:space-between;align-items:center;padding:.5rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.82rem;}
        .side-list-item:last-child{border-bottom:none;}
        .act-item{display:flex;gap:.75rem;align-items:center;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-size:.82rem;}
        .act-item img{border-radius:50%;border:2px solid #a57db5;}
        .act-item:last-child{border-bottom:none;}
    </style>
</head>
<body class="bg-theme bg-theme1">

<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="panel-nav">
        <a href="index.php" class="active"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="staff.php"><i class='bx bx-group'></i> Staff</a>
        <a href="sanciones.php"><i class='bx bx-block'></i> Sanciones</a>
        <a href="usuarios.php"><i class='bx bx-user-circle'></i> Usuarios</a>
        <a href="/agenciaunica/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h3 class="mb-4">🏠 Panel de Administración</h3>

    <!-- Stats row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
            <a href="noticias.php" class="stat-card">
                <h2 style="color:#fbbf24;"><?= $total_noticias ?></h2>
                <p>📰 Noticias</p>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="notificaciones.php" class="stat-card">
                <h2 style="color:#f472b6;"><?= $notifs_no_leidas ?></h2>
                <p>🔔 Notifs sin leer</p>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="usuarios.php" class="stat-card">
                <h2 style="color:#34d399;"><?= $total_usuarios ?></h2>
                <p>👥 Usuarios</p>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="sanciones.php" class="stat-card">
                <h2 style="color:#f87171;"><?= $total_sanciones ?></h2>
                <p>🚫 Sanciones activas</p>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="staff.php" class="stat-card">
                <h2 style="color:#60a5fa;"><?= $total_staff ?></h2>
                <p>⭐ Staff</p>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card" style="cursor:default;">
                <h2 style="color:#a78bfa;"><?= $total_notifs ?></h2>
                <p>📨 Total notifs</p>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="row g-4">
        <!-- Actividad reciente -->
        <div class="col-12 col-md-6">
            <div class="side-card">
                <h6><i class='bx bx-pulse'></i> Actividad reciente</h6>
                <?php if (empty($actividad)): ?>
                <div style="color:#64748b;font-size:.83rem;text-align:center;padding:1.5rem;">Sin actividad registrada aún.</div>
                <?php else: ?>
                <?php foreach($actividad as $a): ?>
                <div class="act-item">
                    <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['nombre_usuario']??'guest') ?>&size=s"
                         alt="" width="28" height="40" loading="lazy">
                    <div>
                        <span style="color:#e2e8f0;"><?= htmlspecialchars($a['descripcion']) ?></span>
                        <div style="font-size:.7rem;color:#64748b;"><?= date('d/m H:i', strtotime($a['fecha'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Últimas noticias + nuevos usuarios -->
        <div class="col-12 col-md-3">
            <div class="side-card mb-3">
                <h6><i class='bx bx-news'></i> Últimas noticias</h6>
                <?php if (empty($ultimas_noticias)): ?>
                <div style="color:#64748b;font-size:.82rem;">Sin noticias.</div>
                <?php else: foreach($ultimas_noticias as $n): ?>
                <div class="side-list-item">
                    <span><?= htmlspecialchars(mb_substr($n['titulo'],0,28)).(mb_strlen($n['titulo'])>28?'...':'') ?></span>
                    <small style="color:#64748b;"><?= date('d/m', strtotime($n['fecha'])) ?></small>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="side-card">
                <h6><i class='bx bx-user-plus'></i> Nuevos usuarios</h6>
                <?php if (empty($ultimos_usuarios)): ?>
                <div style="color:#64748b;font-size:.82rem;">Sin usuarios.</div>
                <?php else: foreach($ultimos_usuarios as $u): ?>
                <div class="side-list-item">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($u['nombre_usuario']) ?>&size=s"
                             alt="" width="22" height="32" style="border-radius:50%;" loading="lazy">
                        <span><?= htmlspecialchars($u['nombre_usuario']) ?></span>
                    </div>
                    <small style="color:#64748b;"><?= $u['fecha_registro'] ? date('d/m', strtotime($u['fecha_registro'])) : '—' ?></small>
                </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once('../plantillas/footer.php'); ?>
</body>
</html>
