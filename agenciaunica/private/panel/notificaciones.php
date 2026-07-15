<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php'); exit;
}

$msg = '';
$msg_type = 'success';

// ENVIAR NOTIFICACION
if (isset($_POST['accion']) && $_POST['accion'] === 'enviar') {
    $mensaje   = trim($_POST['mensaje'] ?? '');
    $destino   = $_POST['destino'] ?? 'todos';
    $id_usuario = intval($_POST['id_usuario'] ?? 0);

    if (!$mensaje) {
        $msg = '⚠️ El mensaje no puede estar vacío.';
        $msg_type = 'warning';
    } else {
        if ($destino === 'todos') {
            $usuarios = [];
            $r = $conn->query("SELECT id FROM registro_usuario");
            if ($r) while ($row = $r->fetch_assoc()) $usuarios[] = $row['id'];

            $stmt = $conn->prepare("INSERT INTO notificaciones (id_usuario, mensaje, leida) VALUES (?, ?, 0)");
            $count = 0;
            foreach ($usuarios as $uid) {
                $stmt->bind_param("is", $uid, $mensaje);
                if ($stmt->execute()) $count++;
            }
            $msg = "✅ Notificación enviada a $count usuario(s).";
        } elseif ($destino === 'uno' && $id_usuario > 0) {
            $stmt = $conn->prepare("INSERT INTO notificaciones (id_usuario, mensaje, leida) VALUES (?, ?, 0)");
            $stmt->bind_param("is", $id_usuario, $mensaje);
            if ($stmt->execute()) {
                $msg = '✅ Notificación enviada al usuario #' . $id_usuario . '.';
            } else {
                $msg = '❌ Error: ' . $conn->error;
                $msg_type = 'danger';
            }
        }
    }
}

// ELIMINAR NOTIFICACION
if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM notificaciones WHERE id = $id");
    $msg = '🗑️ Notificación eliminada.';
}

// Listar últimas 50 notificaciones
$notifs = [];
$r = $conn->query("
    SELECT n.id, n.mensaje, n.leida, n.fecha, u.usuario_registro
    FROM notificaciones n
    LEFT JOIN registro_usuario u ON n.id_usuario = u.id
    ORDER BY n.fecha DESC LIMIT 50");
if ($r) while ($row = $r->fetch_assoc()) $notifs[] = $row;

// Lista de usuarios para selector
$usuarios = [];
$r2 = $conn->query("SELECT id, usuario_registro FROM registro_usuario ORDER BY usuario_registro");
if ($r2) while ($row = $r2->fetch_assoc()) $usuarios[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones — Panel Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/private/assets/css/icons.css">
    <link rel="stylesheet" href="/private/assets/css/app-style.css">
    <link rel="stylesheet" href="/private/assets/css/neon.css">
    <style>
        .panel-nav { display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap; }
        .panel-nav a { padding:.6rem 1.2rem; border-radius:.5rem; background:rgba(255,255,255,0.07); border:1px solid rgba(255,200,80,0.2); color:#e2e8f0; text-decoration:none; font-size:.9rem; transition:.2s; }
        .panel-nav a:hover, .panel-nav a.active { background:rgba(255,165,0,0.2); border-color:rgba(255,165,0,0.5); color:#fbbf24; }
        .form-card { background:rgba(255,255,255,0.04); border:1px solid rgba(255,200,80,0.2); border-radius:1rem; padding:1.5rem; margin-bottom:2rem; }
        .form-card .form-control, .form-card select, .form-card textarea { background:rgba(255,255,255,0.07); border:1px solid rgba(255,200,80,0.2); color:#e2e8f0; }
        .form-card .form-control:focus, .form-card select:focus, .form-card textarea:focus { background:rgba(255,165,0,0.1); border-color:#fbbf24; color:#fff; box-shadow:none; }
        .form-card label { color:#94a3b8; font-size:.85rem; margin-bottom:.3rem; }
        .form-card select option { background:#0d2b45; color:#e2e8f0; }
        .table-dark-custom { background:rgba(255,255,255,0.03); border:1px solid rgba(255,200,80,0.15); border-radius:.75rem; overflow:hidden; }
        .table-dark-custom th { background:rgba(255,165,0,0.1); color:#fbbf24; font-size:.85rem; }
        .table-dark-custom td { color:#e2e8f0; font-size:.85rem; vertical-align:middle; border-color:rgba(255,255,255,0.05); }
        .badge-leida { background:rgba(52,211,153,0.15); color:#34d399; padding:.2rem .6rem; border-radius:999px; font-size:.7rem; }
        .badge-noleida { background:rgba(244,114,182,0.15); color:#f472b6; padding:.2rem .6rem; border-radius:999px; font-size:.7rem; }
        #destinoUno { display:none; }
    </style>
</head>
<body class="bg-theme bg-theme1">
<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="panel-nav">
        <a href="index.php"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php" class="active"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h4 class="mb-3">🔔 Enviar Notificaciones</h4>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- FORMULARIO -->
    <div class="form-card">
        <h5 style="color:#f472b6;margin-bottom:1.25rem;"><i class='bx bx-send'></i> Nueva notificación</h5>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="enviar">
            <div class="row g-3">
                <div class="col-md-4">
                    <label>Destinatario</label>
                    <select name="destino" class="form-control" id="destinoSelect" onchange="toggleDestino(this.value)">
                        <option value="todos">📢 Todos los usuarios</option>
                        <option value="uno">👤 Usuario específico</option>
                    </select>
                </div>
                <div class="col-md-4" id="destinoUno">
                    <label>Seleccionar usuario</label>
                    <select name="id_usuario" class="form-control">
                        <option value="">-- Elige un usuario --</option>
                        <?php foreach($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['usuario_registro']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12">
                    <label>Mensaje de la notificación *</label>
                    <textarea name="mensaje" class="form-control" rows="3"
                        placeholder="Ej: ¡Hay un evento especial esta noche a las 8PM!" required maxlength="500"></textarea>
                    <small style="color:#64748b;">Máximo 500 caracteres.</small>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class='bx bx-bell-plus'></i> Enviar notificación
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- LISTA NOTIFICACIONES -->
    <h5 style="color:#94a3b8;margin-bottom:1rem;">Últimas notificaciones enviadas</h5>
    <?php if (empty($notifs)): ?>
    <div class="alert alert-secondary">No hay notificaciones enviadas aún.</div>
    <?php else: ?>
    <div class="table-dark-custom">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($notifs as $n): ?>
            <tr>
                <td><?= $n['id'] ?></td>
                <td><small><?= htmlspecialchars($n['usuario_registro'] ?? 'N/A') ?></small></td>
                <td style="max-width:300px;"><?= htmlspecialchars($n['mensaje']) ?></td>
                <td>
                    <?php if ($n['leida']): ?>
                    <span class="badge-leida">✅ Leída</span>
                    <?php else: ?>
                    <span class="badge-noleida">🔴 No leída</span>
                    <?php endif; ?>
                </td>
                <td><small><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></small></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar?')">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class='bx bx-trash'></i></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once('../plantillas/footer.php'); ?>
<script>
function toggleDestino(val) {
    document.getElementById('destinoUno').style.display = val === 'uno' ? 'block' : 'none';
}
</script>
</body>
</html>
