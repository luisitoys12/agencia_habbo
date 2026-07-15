<?php
session_start();
require_once('../procesos/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol_id'] < 2) {
    header('Location: /login.php'); exit;
}

$msg = '';
$msg_type = 'success';

// CREAR
if (isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $titulo   = trim($_POST['titulo'] ?? '');
    $contenido = trim($_POST['contenido'] ?? '');
    $imagen   = trim($_POST['imagen'] ?? '');
    $autor    = $_SESSION['usuario'] ?? 'Admin';

    if ($titulo && $contenido) {
        $stmt = $conn->prepare("INSERT INTO publicaciones (titulo, contenido, autor, imagen) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $titulo, $contenido, $autor, $imagen);
        if ($stmt->execute()) {
            $msg = '✅ Noticia publicada correctamente.';
        } else {
            $msg = '❌ Error al publicar: ' . $conn->error;
            $msg_type = 'danger';
        }
    } else {
        $msg = '⚠️ Título y contenido son obligatorios.';
        $msg_type = 'warning';
    }
}

// ELIMINAR
if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $id = intval($_POST['id']);
    $conn->query("DELETE FROM publicaciones WHERE id = $id");
    $msg = '🗑️ Noticia eliminada.';
}

// Listar noticias
$noticias = [];
$r = $conn->query("SELECT id, titulo, autor, fecha FROM publicaciones ORDER BY fecha DESC");
if ($r) while ($row = $r->fetch_assoc()) $noticias[] = $row;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias — Panel Admin</title>
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
        .form-card .form-control, .form-card textarea { background:rgba(255,255,255,0.07); border:1px solid rgba(255,200,80,0.2); color:#e2e8f0; }
        .form-card .form-control:focus, .form-card textarea:focus { background:rgba(255,165,0,0.1); border-color:#fbbf24; color:#fff; box-shadow:none; }
        .form-card label { color:#94a3b8; font-size:.85rem; margin-bottom:.3rem; }
        .table-dark-custom { background:rgba(255,255,255,0.03); border:1px solid rgba(255,200,80,0.15); border-radius:.75rem; overflow:hidden; }
        .table-dark-custom th { background:rgba(255,165,0,0.1); color:#fbbf24; font-size:.85rem; }
        .table-dark-custom td { color:#e2e8f0; font-size:.85rem; vertical-align:middle; border-color:rgba(255,255,255,0.05); }
        .char-count { font-size:.75rem; color:#94a3b8; text-align:right; }
    </style>
</head>
<body class="bg-theme bg-theme1">
<?php require_once('../plantillas/header.php'); ?>

<div class="container-fluid mt-4">
    <div class="panel-nav">
        <a href="index.php"><i class='bx bx-home'></i> Inicio</a>
        <a href="noticias.php" class="active"><i class='bx bx-news'></i> Noticias</a>
        <a href="notificaciones.php"><i class='bx bx-bell'></i> Notificaciones</a>
        <a href="/index.php" target="_blank"><i class='bx bx-link-external'></i> Ver sitio</a>
    </div>

    <h4 class="mb-3">📰 Gestionar Noticias</h4>

    <?php if ($msg): ?>
    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show" role="alert">
        <?= $msg ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- FORMULARIO NUEVA NOTICIA -->
    <div class="form-card">
        <h5 style="color:#fbbf24;margin-bottom:1.25rem;"><i class='bx bx-plus-circle'></i> Publicar nueva noticia</h5>
        <form method="POST" action="">
            <input type="hidden" name="accion" value="crear">
            <div class="row g-3">
                <div class="col-12">
                    <label>Título del artículo *</label>
                    <input type="text" name="titulo" class="form-control" placeholder="Ej: ¡Evento de verano este sábado!" maxlength="255" required>
                </div>
                <div class="col-12">
                    <label>Contenido *</label>
                    <textarea name="contenido" id="contenidoTA" class="form-control" rows="6"
                        placeholder="Escribe el cuerpo de la noticia aquí..." oninput="updateCount(this)"></textarea>
                    <div class="char-count" id="charCount">0 caracteres</div>
                </div>
                <div class="col-12">
                    <label>URL de imagen (opcional)</label>
                    <input type="url" name="imagen" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
                    <small style="color:#64748b;">Deja vacío si no quieres imagen en la tarjeta.</small>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class='bx bx-send'></i> Publicar noticia
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- LISTA DE NOTICIAS -->
    <h5 style="color:#94a3b8;margin-bottom:1rem;">Noticias publicadas (<?= count($noticias) ?>)</h5>
    <?php if (empty($noticias)): ?>
    <div class="alert alert-secondary">No hay noticias publicadas aún.</div>
    <?php else: ?>
    <div class="table-dark-custom">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($noticias as $n): ?>
            <tr>
                <td><?= $n['id'] ?></td>
                <td><?= htmlspecialchars($n['titulo']) ?></td>
                <td><small><?= htmlspecialchars($n['autor']) ?></small></td>
                <td><small><?= date('d/m/Y H:i', strtotime($n['fecha'])) ?></small></td>
                <td>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar esta noticia?')">
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
function updateCount(el) {
    document.getElementById('charCount').textContent = el.value.length + ' caracteres';
}
</script>
</body>
</html>
