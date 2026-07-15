<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: /login.php'); exit;
}

$id  = intval($_SESSION['usuario_id']);
$nick = $_SESSION['usuario'] ?? 'usuario';
$rol  = intval($_SESSION['rol_id'] ?? 1);

// Si es admin, redirigir al panel de admin
if ($rol >= 2) {
    header('Location: /agenciaunica/private/panel/index.php'); exit;
}

// --- Datos del usuario ---
$creditos = 0; $rango_nombre = 'Sin rango'; $rango_img = ''; $ascensos_pendientes = 0;

$st = $conn->prepare('SELECT creditos FROM dinero_digital WHERE id_usuario=? LIMIT 1');
if ($st) { $st->bind_param('i',$id); $st->execute(); $r=$st->get_result(); if($r&&$row=$r->fetch_assoc()) $creditos=$row['creditos']; $st->close(); }

$st2 = $conn->prepare('SELECT r.rango, r.imagen FROM registro_usuario u LEFT JOIN rangos r ON u.Rango_asignado=r.id_rango WHERE u.id=? LIMIT 1');
if ($st2) { $st2->bind_param('i',$id); $st2->execute(); $r2=$st2->get_result(); if($r2&&$row2=$r2->fetch_assoc()){ $rango_nombre=$row2['rango']??'Sin rango'; $rango_img=$row2['imagen']??''; } $st2->close(); }

// Sanciones activas
$sanciones = 0;
$st3 = $conn->prepare('SELECT COUNT(*) AS c FROM sanciones WHERE id_sancionado=? AND activa=1');
if ($st3) { $st3->bind_param('i',$id); $st3->execute(); $r3=$st3->get_result(); if($r3&&$row3=$r3->fetch_assoc()) $sanciones=$row3['c']; $st3->close(); }

// Notificaciones sin leer
$notifs_sin_leer = 0; $notifs = [];
$st4 = $conn->prepare('SELECT id, mensaje, leida, fecha FROM notificaciones WHERE id_usuario=? ORDER BY fecha DESC LIMIT 8');
if ($st4) { $st4->bind_param('i',$id); $st4->execute(); $r4=$st4->get_result(); if($r4) { while($row4=$r4->fetch_assoc()) { $notifs[]=$row4; if(!$row4['leida']) $notifs_sin_leer++; } } $st4->close(); }

// Últimas 5 actividades globales
$actividad = [];
$ra = $conn->query('SELECT a.descripcion, a.fecha, u.nombre_usuario FROM actividad_reciente a LEFT JOIN registro_usuario u ON a.id_usuario=u.id ORDER BY a.fecha DESC LIMIT 8');
if ($ra) while($row=$ra->fetch_assoc()) $actividad[] = $row;

// Marcar notificaciones como leídas
$conn->query("UPDATE notificaciones SET leida=1 WHERE id_usuario=$id AND leida=0");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel — Reino Hogwarz</title>
    <link rel="shortcut icon" href="/private/eventos/halloween/img/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/private/assets/css/icons.css">
    <link rel="stylesheet" href="/private/assets/css/app-style.css">
    <link rel="stylesheet" href="/private/assets/css/neon.css">
    <style>
        :root { --gold:#d4af37; --purple-dark:#2d1b32; --purple-mid:#4e2a57; --purple-light:#a57db5; }
        body { background:#1a0f1e; color:#e2e8f0; }
        .dash-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02));
            border: 1px solid rgba(212,175,55,0.25);
            border-radius: 1rem;
            padding: 1.5rem;
            transition: .3s;
        }
        .dash-card:hover { border-color: rgba(212,175,55,0.55); transform: translateY(-3px); box-shadow: 0 8px 30px rgba(212,175,55,0.12); }
        .dash-card h2 { font-size: 2.4rem; font-weight: 900; margin: 0; }
        .dash-card p { font-size: .82rem; opacity: .65; margin: 0; }
        .avatar-wrap { text-align: center; padding: 2rem 1rem; }
        .avatar-wrap img.avatar-big { border-radius: 50%; border: 4px solid var(--gold); box-shadow: 0 0 20px rgba(212,175,55,0.4); }
        .badge-rango { background: var(--purple-mid); color: var(--gold); font-size: .85rem; padding: .4rem 1rem; border-radius: 999px; border: 1px solid var(--gold); }
        .notif-item { display:flex; gap:.75rem; align-items:flex-start; padding:.75rem; border-bottom:1px solid rgba(255,255,255,0.06); }
        .notif-item:last-child { border-bottom: none; }
        .notif-icon { font-size:1.4rem; min-width:2rem; text-align:center; }
        .notif-msg { font-size:.85rem; color:#cbd5e1; }
        .notif-time { font-size:.72rem; color:#64748b; margin-top:.2rem; }
        .act-item { display:flex; align-items:center; gap:.75rem; padding:.6rem .5rem; border-bottom:1px solid rgba(255,255,255,0.05); font-size:.83rem; }
        .act-item img { border-radius:50%; border:2px solid var(--purple-light); }
        .act-item:last-child { border-bottom:none; }
        .sancion-banner { background: rgba(220,38,38,0.15); border:1px solid rgba(220,38,38,0.4); border-radius:.75rem; padding:1rem 1.25rem; margin-bottom:1.5rem; }
        .panel-header { background: linear-gradient(135deg, var(--purple-dark), #1a0f1e); border-bottom: 1px solid rgba(212,175,55,0.2); padding: 1rem 1.5rem; display:flex; align-items:center; gap:1rem; margin-bottom:2rem; }
        .panel-header a { color: rgba(212,175,55,0.7); text-decoration:none; font-size:.85rem; }
        .panel-header a:hover { color: var(--gold); }
        .panel-header .sep { color: rgba(255,255,255,0.2); }
        .section-title { font-size:1rem; font-weight:700; color:var(--gold); margin-bottom:1rem; display:flex; align-items:center; gap:.5rem; }
        .credits-big { font-size:2.8rem; font-weight:900; color:#fbbf24; letter-spacing:-1px; }
        .rango-badge-big { display:inline-flex; align-items:center; gap:.5rem; background:rgba(165,125,181,0.15); border:1px solid var(--purple-light); border-radius:.75rem; padding:.5rem 1rem; font-size:.9rem; color:var(--purple-light); }
    </style>
</head>
<body>
<div class="panel-header">
    <img src="/agenciaunica/private/assets/images/favicon.png" alt="Logo" width="36" height="36" style="border-radius:50%;border:2px solid var(--gold);">
    <span style="color:var(--gold);font-weight:700;">Reino Hogwarz</span>
    <span class="sep">|</span>
    <a href="/agenciaunica/index.php"><i class='bx bx-home'></i> Inicio</a>
    <span class="sep">|</span>
    <a href="/logout.php" class="ms-auto" style="color:#f87171;"><i class='bx bx-log-out'></i> Salir</a>
</div>

<div class="container-fluid px-3 px-md-4 pb-5">

    <?php if ($sanciones > 0): ?>
    <div class="sancion-banner">
        <i class='bx bx-block' style="color:#f87171;font-size:1.2rem;"></i>
        <strong style="color:#f87171;"> Tienes <?= $sanciones ?> sanción(es) activa(s).</strong>
        <span style="font-size:.85rem;color:#fca5a5;"> Contacta a un administrador para más información.</span>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna izquierda: perfil -->
        <div class="col-12 col-md-3">
            <div class="dash-card">
                <div class="avatar-wrap">
                    <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nick) ?>&direction=3&head_direction=3&gesture=sml&action=none&size=b"
                         alt="Avatar" width="100" height="130" class="avatar-big" loading="lazy">
                    <h5 class="mt-3 mb-1" style="color:#fff;"><?= htmlspecialchars($nick) ?></h5>
                    <div class="rango-badge-big mt-2">
                        <?php if($rango_img): ?>
                        <img src="<?= htmlspecialchars($rango_img) ?>" alt="" width="20" height="20" onerror="this.style.display='none'">
                        <?php endif; ?>
                        <?= htmlspecialchars($rango_nombre) ?>
                    </div>
                    <div class="mt-3">
                        <div style="font-size:.75rem;color:#94a3b8;">Créditos disponibles</div>
                        <div class="credits-big"><?= number_format($creditos) ?></div>
                    </div>
                </div>
                <hr style="border-color:rgba(255,255,255,0.1);">
                <div class="d-grid gap-2">
                    <a href="/index.php?page=PERFIL" class="btn btn-sm" style="background:rgba(212,175,55,0.15);border:1px solid var(--gold);color:var(--gold);"><i class='bx bx-user'></i> Mi perfil completo</a>
                    <a href="/logout.php" class="btn btn-sm btn-outline-danger"><i class='bx bx-log-out'></i> Cerrar sesión</a>
                </div>
            </div>
        </div>

        <!-- Columna central -->
        <div class="col-12 col-md-6">

            <!-- Bienvenida -->
            <div class="dash-card mb-4" style="background:linear-gradient(135deg,rgba(212,175,55,0.12),rgba(78,42,87,0.3));">
                <h4 style="color:var(--gold);margin-bottom:.5rem;">☀️ ¡Bienvenido de vuelta, <?= htmlspecialchars($nick) ?>!</h4>
                <p style="color:#94a3b8;font-size:.9rem;">Estás en el Reino Hogwarz. Completa misiones, sube de rango y gana créditos.</p>
                <a href="/agenciaunica/index.php" class="btn btn-sm mt-2" style="background:rgba(212,175,55,0.2);border:1px solid var(--gold);color:var(--gold);">
                    <i class='bx bx-globe'></i> Ver el sitio público
                </a>
            </div>

            <!-- Actividad reciente -->
            <div class="dash-card">
                <div class="section-title"><i class='bx bx-pulse'></i> Actividad reciente</div>
                <?php if (empty($actividad)): ?>
                <div style="text-align:center;padding:2rem;color:#64748b;">
                    <i class='bx bx-info-circle' style="font-size:2.5rem;"></i>
                    <p class="mt-2">Aún no hay actividad registrada.</p>
                </div>
                <?php else: ?>
                <?php foreach($actividad as $a): ?>
                <div class="act-item">
                    <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['nombre_usuario']??'guest') ?>&size=s"
                         alt="" width="30" height="42" loading="lazy">
                    <div>
                        <span style="color:#e2e8f0;"><?= htmlspecialchars($a['descripcion']) ?></span>
                        <div style="font-size:.72rem;color:#64748b;margin-top:.15rem;"><?= date('d/m H:i', strtotime($a['fecha'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna derecha: notificaciones -->
        <div class="col-12 col-md-3">
            <div class="dash-card">
                <div class="section-title">
                    <i class='bx bx-bell'></i> Notificaciones
                    <?php if ($notifs_sin_leer > 0): ?>
                    <span class="badge rounded-pill" style="background:#f87171;font-size:.72rem;"><?= $notifs_sin_leer ?></span>
                    <?php endif; ?>
                </div>
                <?php if (empty($notifs)): ?>
                <div style="text-align:center;padding:1.5rem;color:#64748b;font-size:.85rem;">
                    <i class='bx bx-bell-off' style="font-size:2rem;"></i>
                    <p class="mt-2">No tienes notificaciones.</p>
                </div>
                <?php else: ?>
                <?php foreach($notifs as $n): ?>
                <div class="notif-item">
                    <div class="notif-icon"><?= $n['leida'] ? '📩' : '🔔' ?></div>
                    <div>
                        <div class="notif-msg"><?= htmlspecialchars($n['mensaje']) ?></div>
                        <div class="notif-time"><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
