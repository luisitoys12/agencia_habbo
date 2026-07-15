<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php'); exit;
}

$msg = ''; $msg_type = 'success';

// CREAR / ACTUALIZAR admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $rango  = trim($_POST['rango']  ?? '');
        $cara   = trim($_POST['cara']   ?? '');
        $accion_campo = trim($_POST['accion_campo'] ?? 'none');
        $bebida = trim($_POST['bebida'] ?? 'none');

        if (!$nombre || !$rango) {
            $msg = '⚠️ Nombre y rango son obligatorios.'; $msg_type = 'warning';
        } elseif ($accion === 'crear') {
            $st = $conn->prepare('INSERT INTO modificar_administradores (nombre, rango, cara, accion, bebida) VALUES (?,?,?,?,?)');
            $st->bind_param('sssss', $nombre, $rango, $cara, $accion_campo, $bebida);
            $st->execute() ? $msg='✅ Administrador añadido.' : ($msg='❌ Error: '.$conn->error);
        } else {
            $id = intval($_POST['id']);
            $st = $conn->prepare('UPDATE modificar_administradores SET nombre=?, rango=?, cara=?, accion=?, bebida=? WHERE id=?');
            $st->bind_param('sssssi', $nombre, $rango, $cara, $accion_campo, $bebida, $id);
            $st->execute() ? $msg='✅ Administrador actualizado.' : ($msg='❌ Error: '.$conn->error);
        }
    }

    if ($accion === 'eliminar') {
        $id = intval($_POST['id']);
        $conn->query("DELETE FROM modificar_administradores WHERE id=$id");
        $msg = '🗑️ Administrador eliminado.';
    }
}

// Listar admins
$admins = [];
$r = $conn->query('SELECT id, nombre, rango, cara, accion, bebida FROM modificar_administradores ORDER BY id ASC');
if ($r) while($row = $r->fetch_assoc()) $admins[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff — Panel Admin</title>
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
        .form-card .form-select option{background:#2d1b32;}
        .staff-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;}
        .staff-item{background:rgba(255,255,255,.04);border:1px solid rgba(255,200,80,.15);border-radius:.75rem;padding:1rem;text-align:center;transition:.2s;}
        .staff-item:hover{border-color:rgba(255,200,80,.4);}
        .staff-item img{border-radius:50%;border:2px solid #d4af37;}
        .staff-item .s-name{font-size:.85rem;color:#e2e8f0;margin-top:.5rem;font-weight:600;}
        .staff-item .s-rango{font-size:.72rem;color:#a57db5;}
        .staff-actions{display:flex;gap:.4rem;justify-content:center;margin-top:.75rem;}
    </style>
</head>
<body class="bg-theme bg-theme1">
<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="panel-nav">
        <a href="index.php"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="staff.php" class="active"><i class='bx bx-group'></i> Staff</a>
        <a href="sanciones.php"><i class='bx bx-block'></i> Sanciones</a>
        <a href="usuarios.php"><i class='bx bx-user-circle'></i> Usuarios</a>
        <a href="/agenciaunica/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h4 class="mb-3">👥 Gestión de Staff</h4>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show">
        <?= $msg ?><button class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="form-card">
        <h5 style="color:#fbbf24;margin-bottom:1.25rem;"><i class='bx bx-user-plus'></i> Añadir / Editar administrador</h5>
        <form method="POST" id="staffForm">
            <input type="hidden" name="accion" value="crear" id="formAccion">
            <input type="hidden" name="id" id="formId" value="0">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Nick Habbo *</label>
                    <input type="text" name="nombre" id="fNombre" class="form-control" placeholder="NickHabbo" required>
                </div>
                <div class="col-md-3">
                    <label>Rango / Cargo *</label>
                    <input type="text" name="rango" id="fRango" class="form-control" placeholder="Ej: Dueño" required>
                </div>
                <div class="col-md-2">
                    <label>Cara (expresión)</label>
                    <input type="text" name="cara" id="fCara" class="form-control" placeholder="sml">
                </div>
                <div class="col-md-2">
                    <label>Acción pose</label>
                    <input type="text" name="accion_campo" id="fAccion" class="form-control" placeholder="none">
                </div>
                <div class="col-md-2">
                    <label>Bebida pose</label>
                    <input type="text" name="bebida" id="fBebida" class="form-control" placeholder="none">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-warning me-2"><i class='bx bx-save'></i> Guardar</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetForm()">Cancelar edición</button>
                </div>
            </div>
        </form>
    </div>

    <div class="section-title mb-3" style="color:#d4af37;font-weight:700;">Staff actual (<?= count($admins) ?>)</div>

    <?php if (empty($admins)): ?>
    <div class="alert alert-secondary">No hay administradores registrados.</div>
    <?php else: ?>
    <div class="staff-grid">
    <?php foreach ($admins as $a): ?>
        <div class="staff-item">
            <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['nombre']) ?>&direction=3&head_direction=3&gesture=<?= urlencode($a['cara']?:'sml') ?>&action=<?= urlencode($a['accion']?:'none') ?>&size=s"
                 alt="" width="50" height="70" loading="lazy">
            <div class="s-name"><?= htmlspecialchars($a['nombre']) ?></div>
            <div class="s-rango"><?= htmlspecialchars($a['rango']) ?></div>
            <div class="staff-actions">
                <button class="btn btn-sm btn-outline-warning" title="Editar"
                    onclick="editStaff(<?= $a['id'] ?>, '<?= addslashes($a['nombre']) ?>', '<?= addslashes($a['rango']) ?>', '<?= addslashes($a['cara']) ?>', '<?= addslashes($a['accion']) ?>', '<?= addslashes($a['bebida']) ?>')"><i class='bx bx-pencil'></i></button>
                <form method="POST" style="margin:0;" onsubmit="return confirm('¿Eliminar a <?= htmlspecialchars($a['nombre']) ?>?')">
                    <input type="hidden" name="accion" value="eliminar">
                    <input type="hidden" name="id" value="<?= $a['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class='bx bx-trash'></i></button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once('../plantillas/footer.php'); ?>
<script>
function editStaff(id, nombre, rango, cara, accion, bebida) {
    document.getElementById('formAccion').value = 'editar';
    document.getElementById('formId').value     = id;
    document.getElementById('fNombre').value    = nombre;
    document.getElementById('fRango').value     = rango;
    document.getElementById('fCara').value      = cara;
    document.getElementById('fAccion').value    = accion;
    document.getElementById('fBebida').value    = bebida;
    document.getElementById('staffForm').scrollIntoView({behavior:'smooth'});
}
function resetForm() {
    document.getElementById('formAccion').value = 'crear';
    document.getElementById('formId').value     = '0';
    document.getElementById('staffForm').reset();
}
</script>
</body>
</html>
