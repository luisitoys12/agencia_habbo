<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php'); exit;
}

$msg = ''; $msg_type = 'success';
$admin_nick = $_SESSION['usuario'] ?? 'Admin';
$admin_id   = intval($_SESSION['usuario_id']);

// SANCIONAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'sancionar') {
    $nick      = trim($_POST['nick_sancionado'] ?? '');
    $motivo    = trim($_POST['motivo'] ?? '');
    $tipo      = trim($_POST['tipo'] ?? 'Advertencia');
    $duracion  = trim($_POST['duracion'] ?? 'Indefinida');

    if (!$nick || !$motivo) {
        $msg = '⚠️ Nick y motivo son obligatorios.'; $msg_type = 'warning';
    } else {
        // Buscar id del sancionado
        $stU = $conn->prepare('SELECT id FROM registro_usuario WHERE nombre_usuario=? LIMIT 1');
        $stU->bind_param('s', $nick); $stU->execute(); $rU = $stU->get_result();
        if ($rU && $rowU = $rU->fetch_assoc()) {
            $id_sancionado = $rowU['id'];
            $stI = $conn->prepare('INSERT INTO sanciones (id_sancionado, nick_sancionado, id_admin, nick_admin, motivo, tipo, duracion, activa) VALUES (?,?,?,?,?,?,?,1)');
            $stI->bind_param('isisssss', $id_sancionado, $nick, $admin_id, $admin_nick, $motivo, $tipo, $duracion);
            $stI->execute() ? $msg="✅ Sanción aplicada a $nick." : ($msg='❌ Error: '.$conn->error);
        } else {
            $msg = "❌ No se encontró el usuario '$nick' en el registro."; $msg_type = 'danger';
        }
    }
}

// LEVANTAR SANCIÓN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'levantar') {
    $id = intval($_POST['id']);
    $conn->query("UPDATE sanciones SET activa=0 WHERE id=$id");
    $msg = '✅ Sanción levantada.';
}

// Listar sanciones
$sanciones = [];
$rs = $conn->query('SELECT id, nick_sancionado, nick_admin, motivo, tipo, duracion, activa, fecha FROM sanciones ORDER BY activa DESC, fecha DESC LIMIT 100');
if ($rs) while ($row = $rs->fetch_assoc()) $sanciones[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanciones — Panel Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/private/assets/css/icons.css">
    <link rel="stylesheet" href="/private/assets/css/app-style.css">
    <link rel="stylesheet" href="/private/assets/css/neon.css">
    <style>
        .panel-nav{display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;}
        .panel-nav a{padding:.6rem 1.2rem;border-radius:.5rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,200,80,.2);color:#e2e8f0;text-decoration:none;font-size:.9rem;transition:.2s;}
        .panel-nav a:hover,.panel-nav a.active{background:rgba(255,165,0,.2);border-color:rgba(255,165,0,.5);color:#fbbf24;}
        .form-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,200,80,.2);border-radius:1rem;padding:1.5rem;margin-bottom:2rem;}
        .form-card .form-control,.form-card .form-select{background:rgba(255,255,255,.07);border:1px solid rgba(255,200,80,.2);color:#e2e8f0;}
        .form-card .form-control:focus,.form-card .form-select:focus{background:rgba(255,165,0,.1);border-color:#fbbf24;color:#fff;box-shadow:none;}
        .form-card label{color:#94a3b8;font-size:.85rem;margin-bottom:.3rem;}
        .form-card .form-select option{background:#2d1b32;color:#e2e8f0;}
        .badge-activa{background:rgba(239,68,68,.2);color:#f87171;border:1px solid rgba(239,68,68,.4);border-radius:.5rem;padding:.2rem .6rem;font-size:.75rem;}
        .badge-inactiva{background:rgba(100,116,139,.15);color:#94a3b8;border:1px solid rgba(100,116,139,.3);border-radius:.5rem;padding:.2rem .6rem;font-size:.75rem;}
        .table-dark-custom{background:rgba(255,255,255,.03);border:1px solid rgba(255,200,80,.15);border-radius:.75rem;overflow:hidden;}
        .table-dark-custom th{background:rgba(255,165,0,.1);color:#fbbf24;font-size:.82rem;}
        .table-dark-custom td{color:#e2e8f0;font-size:.82rem;vertical-align:middle;border-color:rgba(255,255,255,.05);}
    </style>
</head>
<body class="bg-theme bg-theme1">
<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="panel-nav">
        <a href="index.php"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="staff.php"><i class='bx bx-group'></i> Staff</a>
        <a href="sanciones.php" class="active"><i class='bx bx-block'></i> Sanciones</a>
        <a href="usuarios.php"><i class='bx bx-user-circle'></i> Usuarios</a>
        <a href="/agenciaunica/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h4 class="mb-3">🚫 Gestión de Sanciones</h4>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show">
        <?= $msg ?><button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="form-card">
        <h5 style="color:#f87171;margin-bottom:1.25rem;"><i class='bx bx-user-x'></i> Aplicar nueva sanción</h5>
        <form method="POST">
            <input type="hidden" name="accion" value="sancionar">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Nick Habbo del usuario *</label>
                    <input type="text" name="nick_sancionado" class="form-control" placeholder="NickDelUsuario" required>
                </div>
                <div class="col-md-2">
                    <label>Tipo de sanción</label>
                    <select name="tipo" class="form-select">
                        <option value="Advertencia">⚠️ Advertencia</option>
                        <option value="Suspensión temporal">⏸️ Suspensión temporal</option>
                        <option value="Suspensión definitiva">🚫 Suspensión definitiva</option>
                        <option value="Bajada de rango">⬇️ Bajada de rango</option>
                        <option value="Expulsión">❌ Expulsión</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Duración</label>
                    <input type="text" name="duracion" class="form-control" placeholder="Ej: 7 días / Indefinida">
                </div>
                <div class="col-md-5">
                    <label>Motivo *</label>
                    <input type="text" name="motivo" class="form-control" placeholder="Describe el motivo de la sanción..." required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-danger"><i class='bx bx-block'></i> Aplicar sanción</button>
                </div>
            </div>
        </form>
    </div>

    <h5 style="color:#94a3b8;margin-bottom:1rem;">Historial de sanciones (<?= count($sanciones) ?>)</h5>
    <?php if (empty($sanciones)): ?>
    <div class="alert alert-secondary">No hay sanciones registradas.</div>
    <?php else: ?>
    <div class="table-dark-custom">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Duración</th>
                    <th>Motivo</th>
                    <th>Admin</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($sanciones as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><strong><?= htmlspecialchars($s['nick_sancionado']) ?></strong></td>
                <td><?= htmlspecialchars($s['tipo']) ?></td>
                <td><small><?= htmlspecialchars($s['duracion']) ?></small></td>
                <td><small><?= htmlspecialchars(mb_substr($s['motivo'],0,60)) ?><?= mb_strlen($s['motivo'])>60?'...':'' ?></small></td>
                <td><small style="color:#a57db5;"><?= htmlspecialchars($s['nick_admin']) ?></small></td>
                <td><small><?= date('d/m/Y', strtotime($s['fecha'])) ?></small></td>
                <td><?= $s['activa'] ? '<span class="badge-activa">Activa</span>' : '<span class="badge-inactiva">Levantada</span>' ?></td>
                <td>
                    <?php if ($s['activa']): ?>
                    <form method="POST" style="margin:0;" onsubmit="return confirm('¿Levantar esta sanción?')">
                        <input type="hidden" name="accion" value="levantar">
                        <input type="hidden" name="id" value="<?= $s['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-success"><i class='bx bx-check'></i></button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once('../plantillas/footer.php'); ?>
</body>
</html>
