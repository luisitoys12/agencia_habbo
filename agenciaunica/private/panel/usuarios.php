<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php'); exit;
}

$search = trim($_GET['q'] ?? '');
$usuarios = [];

$sql = 'SELECT u.id, u.nombre_usuario, u.correo, u.rol_id, r.rango, d.creditos
        FROM registro_usuario u
        LEFT JOIN rangos r ON u.Rango_asignado = r.id_rango
        LEFT JOIN dinero_digital d ON d.id_usuario = u.id';

if ($search) {
    $s = $conn->real_escape_string($search);
    $sql .= " WHERE u.nombre_usuario LIKE '%$s%' OR u.correo LIKE '%$s%'";
}
$sql .= ' ORDER BY u.id DESC LIMIT 80';
$r = $conn->query($sql);
if ($r) while ($row = $r->fetch_assoc()) $usuarios[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios — Panel Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/private/assets/css/icons.css">
    <link rel="stylesheet" href="/private/assets/css/app-style.css">
    <link rel="stylesheet" href="/private/assets/css/neon.css">
    <style>
        .panel-nav{display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;}
        .panel-nav a{padding:.6rem 1.2rem;border-radius:.5rem;background:rgba(255,255,255,.07);border:1px solid rgba(255,200,80,.2);color:#e2e8f0;text-decoration:none;font-size:.9rem;transition:.2s;}
        .panel-nav a:hover,.panel-nav a.active{background:rgba(255,165,0,.2);border-color:rgba(255,165,0,.5);color:#fbbf24;}
        .search-bar{background:rgba(255,255,255,.07);border:1px solid rgba(255,200,80,.2);border-radius:.75rem;padding:.6rem 1rem;display:flex;gap:.75rem;margin-bottom:1.5rem;align-items:center;}
        .search-bar input{background:transparent;border:none;color:#e2e8f0;flex:1;font-size:.9rem;outline:none;}
        .search-bar input::placeholder{color:#64748b;}
        .table-dark-custom{background:rgba(255,255,255,.03);border:1px solid rgba(255,200,80,.15);border-radius:.75rem;overflow:hidden;}
        .table-dark-custom th{background:rgba(255,165,0,.1);color:#fbbf24;font-size:.82rem;}
        .table-dark-custom td{color:#e2e8f0;font-size:.82rem;vertical-align:middle;border-color:rgba(255,255,255,.05);}
        .badge-rol-1{background:rgba(100,116,139,.2);color:#94a3b8;}
        .badge-rol-2{background:rgba(251,191,36,.15);color:#fbbf24;}
        .badge-rol-3{background:rgba(52,211,153,.15);color:#34d399;}
        .badge-rol-4{background:rgba(239,68,68,.2);color:#f87171;}
        .user-avatar{border-radius:50%;border:2px solid #d4af37;}
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
        <a href="sanciones.php"><i class='bx bx-block'></i> Sanciones</a>
        <a href="usuarios.php" class="active"><i class='bx bx-user-circle'></i> Usuarios</a>
        <a href="/agenciaunica/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h4 class="mb-3">👤 Gestión de Usuarios</h4>

    <form method="GET" class="search-bar">
        <i class='bx bx-search' style="color:#94a3b8;"></i>
        <input type="text" name="q" placeholder="Buscar por nick o correo..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-sm btn-outline-warning">Buscar</button>
        <?php if($search): ?><a href="usuarios.php" class="btn btn-sm btn-outline-secondary">Limpiar</a><?php endif; ?>
    </form>

    <div style="color:#64748b;font-size:.83rem;margin-bottom:1rem;">Mostrando <?= count($usuarios) ?> usuarios<?= $search ? " para '$search'" : ' (últimos 80)' ?></div>

    <?php if(empty($usuarios)): ?>
    <div class="alert alert-secondary">No se encontraron usuarios<?= $search ? " con '$search'" : '' ?>.</div>
    <?php else: ?>
    <div class="table-dark-custom">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Nick Habbo</th>
                    <th>Correo</th>
                    <th>Rango</th>
                    <th>Rol</th>
                    <th>Créditos</th>
                    <th>Sanciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($usuarios as $u):
                $roles = [1=>'Usuario',2=>'Mod',3=>'Admin',4=>'Dueño'];
                $rol_txt = $roles[$u['rol_id']] ?? 'Usuario';
                $rol_cls = 'badge-rol-'.($u['rol_id']??1);
                // Contar sanciones activas
                $n_sanciones = 0;
                $stS = $conn->prepare('SELECT COUNT(*) AS c FROM sanciones WHERE id_sancionado=? AND activa=1');
                if ($stS) { $stS->bind_param('i',$u['id']); $stS->execute(); $rS=$stS->get_result(); if($rS&&$rowS=$rS->fetch_assoc()) $n_sanciones=$rowS['c']; $stS->close(); }
            ?>
            <tr>
                <td>
                    <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($u['nombre_usuario']) ?>&size=s"
                         class="user-avatar" alt="" width="28" height="40" loading="lazy">
                </td>
                <td><strong><?= htmlspecialchars($u['nombre_usuario']) ?></strong></td>
                <td><small style="color:#94a3b8;"><?= htmlspecialchars($u['correo']??'—') ?></small></td>
                <td><small><?= htmlspecialchars($u['rango']??'Sin rango') ?></small></td>
                <td><span class="badge <?= $rol_cls ?>"><?= $rol_txt ?></span></td>
                <td style="color:#fbbf24;font-weight:700;"><?= number_format($u['creditos']??0) ?></td>
                <td><?= $n_sanciones > 0 ? "<span class='badge bg-danger'>$n_sanciones activa(s)</span>" : '<span style="color:#64748b;">—</span>' ?></td>
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
