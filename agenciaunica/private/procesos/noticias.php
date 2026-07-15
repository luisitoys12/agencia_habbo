<?php
/**
 * noticias.php — CRUD de noticias/anuncios. Solo dueños (rol_id=4).
 * Cargado desde index.php?page=NOT
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) { header('Location: /login.php'); exit(); }
if ($_SESSION['rol_id'] < 3) { header('Location: /index.php'); exit(); }

require_once(__DIR__ . '/db.php');

$msg = '';

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'crear') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $tipo = $_POST['tipo'] ?? 'noticia';
    if ($titulo && $descripcion) {
        $stmt = $conn->prepare('INSERT INTO publicaciones (titulo, descripcion, tipo, id_autor, fecha) VALUES (?,?,?,?,NOW())');
        $stmt->bind_param('sssі', $titulo, $descripcion, $tipo, $_SESSION['id']);
        // fix: bind_param con i
        $id_autor = (int)$_SESSION['id'];
        $stmt->close();
        $stmt2 = $conn->prepare('INSERT INTO publicaciones (titulo, descripcion, tipo, id_autor, fecha) VALUES (?,?,?,?,NOW())');
        $stmt2->bind_param('sssi', $titulo, $descripcion, $tipo, $id_autor);
        $stmt2->execute();
        $stmt2->close();
        $msg = 'success|Publicación creada correctamente.';
    } else {
        $msg = 'error|Completa todos los campos.';
    }
}

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'eliminar') {
    $id_pub = (int)($_POST['id_pub'] ?? 0);
    if ($id_pub > 0) {
        $stmt = $conn->prepare('DELETE FROM publicaciones WHERE id = ?');
        $stmt->bind_param('i', $id_pub);
        $stmt->execute();
        $stmt->close();
        $msg = 'success|Publicación eliminada.';
    }
}

// Listar todas
$pubs = $conn->query("
    SELECT p.id, p.titulo, p.descripcion, p.tipo, p.fecha, u.usuario_registro AS autor
    FROM publicaciones p
    LEFT JOIN registro_usuario u ON p.id_autor = u.id
    ORDER BY p.fecha DESC
")->fetch_all(MYSQLI_ASSOC);
?>
<body class="bg-theme">
<div class="content-wrapper">
  <div class="container-fluid">

    <?php if ($msg): list($tipo_msg,$texto_msg)=explode('|',$msg,2); ?>
    <div class="alert alert-<?= $tipo_msg==='success'?'success':'danger' ?> mt-3"><?= htmlspecialchars($texto_msg) ?></div>
    <?php endif; ?>

    <?php if ($_SESSION['rol_id'] >= 4): ?>
    <!-- Formulario crear publicación (solo dueños) -->
    <div class="card text-white mt-3" style="background:#5e4b8a;border:2px solid #a57db5;border-radius:10px;">
      <div class="card-header"><i class="fa fa-plus"></i> Nueva publicación</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="accion" value="crear">
          <div class="row">
            <div class="col-12 col-md-6 mb-2">
              <label>Título</label>
              <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="col-12 col-md-6 mb-2">
              <label>Tipo</label>
              <select name="tipo" class="form-control">
                <option value="noticia">Noticia</option>
                <option value="actualizacion">Actualización</option>
                <option value="blog">Blog</option>
                <option value="anuncio">Anuncio</option>
              </select>
            </div>
            <div class="col-12 mb-2">
              <label>Descripción</label>
              <textarea name="descripcion" class="form-control" rows="4" required></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-light"><i class="fa fa-save"></i> Publicar</button>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <!-- Lista de publicaciones -->
    <div class="card text-white mt-3" style="background:#2c2f33;border-radius:10px;">
      <div class="card-header"><i class="fa fa-newspaper-o"></i> Publicaciones y noticias</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="color:#fff;">
          <thead style="background:#444;">
            <tr>
              <th>Tipo</th><th>Título</th><th>Autor</th><th>Fecha</th>
              <?php if ($_SESSION['rol_id'] >= 4): ?><th>Acción</th><?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pubs)): ?>
            <tr><td colspan="5" class="text-center text-muted">No hay publicaciones aún.</td></tr>
            <?php else: ?>
            <?php foreach ($pubs as $p): ?>
            <tr>
              <td><span class="badge" style="background:#a57db5;"><?= htmlspecialchars($p['tipo']) ?></span></td>
              <td><?= htmlspecialchars($p['titulo']) ?></td>
              <td><?= htmlspecialchars($p['autor'] ?? 'Sistema') ?></td>
              <td><?= $p['fecha'] ? date('d/m/Y H:i', strtotime($p['fecha'])) : '' ?></td>
              <?php if ($_SESSION['rol_id'] >= 4): ?>
              <td>
                <form method="POST" onsubmit="return confirm('¿Eliminar esta publicación?')" style="display:inline;">
                  <input type="hidden" name="accion" value="eliminar">
                  <input type="hidden" name="id_pub" value="<?= (int)$p['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                </form>
              </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>
</body>
