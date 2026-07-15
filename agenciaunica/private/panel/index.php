<?php
session_start();
require_once('../procesos/db.php');

// Verificar que sea admin (rol_id >= 2)
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['rol_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php');
    exit;
}

// Stats
$total_noticias = $conn->query("SELECT COUNT(*) as t FROM publicaciones")->fetch_assoc()['t'] ?? 0;
$total_notifs   = $conn->query("SELECT COUNT(*) as t FROM notificaciones")->fetch_assoc()['t'] ?? 0;
$total_usuarios = $conn->query("SELECT COUNT(*) as t FROM registro_usuario")->fetch_assoc()['t'] ?? 0;
$notifs_no_leidas = $conn->query("SELECT COUNT(*) as t FROM notificaciones WHERE leida = 0")->fetch_assoc()['t'] ?? 0;
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
        .stat-card { background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)); border: 1px solid rgba(255,200,80,0.2); border-radius: 1rem; padding: 1.5rem; text-align: center; transition: .3s; }
        .stat-card:hover { border-color: rgba(255,200,80,0.5); transform: translateY(-3px); }
        .stat-card h2 { font-size: 2.5rem; font-weight: 900; margin: 0; }
        .stat-card p { font-size: .85rem; opacity: .7; margin: 0; }
        .panel-nav { display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .panel-nav a { padding: .6rem 1.2rem; border-radius: .5rem; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,200,80,0.2); color: #e2e8f0; text-decoration: none; font-size: .9rem; transition: .2s; }
        .panel-nav a:hover, .panel-nav a.active { background: rgba(255,165,0,0.2); border-color: rgba(255,165,0,0.5); color: #fbbf24; }
    </style>
</head>
<body class="bg-theme bg-theme1">

<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <h3 class="mb-4">🏠 Panel de Administración</h3>

    <div class="panel-nav">
        <a href="index.php" class="active"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 style="color:#fbbf24;"><?= $total_noticias ?></h2>
                <p>📰 Noticias publicadas</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 style="color:#f472b6;"><?= $notifs_no_leidas ?></h2>
                <p>🔔 Notifs sin leer</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 style="color:#34d399;"><?= $total_usuarios ?></h2>
                <p>👥 Usuarios registrados</p>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <h2 style="color:#60a5fa;"><?= $total_notifs ?></h2>
                <p>📨 Total notificaciones</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <a href="noticias.php" style="text-decoration:none;">
                <div class="stat-card" style="text-align:left;cursor:pointer;">
                    <h5 style="color:#fbbf24;"><i class='bx bx-news'></i> Gestionar Noticias</h5>
                    <p style="font-size:.9rem;margin-top:.5rem;">Crear, editar y eliminar artículos que aparecen en la sección de noticias del inicio.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="notificaciones.php" style="text-decoration:none;">
                <div class="stat-card" style="text-align:left;cursor:pointer;">
                    <h5 style="color:#f472b6;"><i class='bx bx-bell'></i> Enviar Notificaciones</h5>
                    <p style="font-size:.9rem;margin-top:.5rem;">Enviar alertas personalizadas a usuarios individuales o a toda la comunidad.</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php require_once('../plantillas/footer.php'); ?>
</body>
</html>
