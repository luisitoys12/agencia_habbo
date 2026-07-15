<?php
/**
 * sanciones.php — Gestión de sanciones/advertencias. Rol >= 3.
 * Cargado desde index.php?page=SAN
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) { header('Location: /login.php'); exit(); }
if ($_SESSION['rol_id'] < 3) { header('Location: /index.php'); exit(); }

require_once(__DIR__ . '/db.php');

$msg = '';

// CREATE sanción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'sancionar') {
    $id_sancionado = (int)($_POST['id_sancionado'] ?? 0);
    $tipo = $_POST['tipo_sancion'] ?? 'advertencia';
    $motivo = trim($_POST['motivo'] ?? '');
    $id_autor = (int)$_SESSION['id'];
    if ($id_sancionado > 0 && $motivo) {
        $stmt = $conn->prepare(
            'INSERT INTO sanciones (id_sancionado, id_autor, tipo_sancion, motivo, fecha)
             VALUES (?,?,?,?,NOW())'
        );
        $stmt->bind_param('iiss', $id_sancionado, $id_autor, $tipo, $motivo);
        $stmt->execute();
        $stmt->close();
        $msg = 'success|Sanción registrada correctamente.';
    } else {
        $msg = 'error|Completa todos los campos.';
    }
}

// LEVANTAR sanción
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'levantar') {
    $id_san = (int)($_POST['id_san'] ?? 0);
    if ($id_san > 0) {
        $stmt = $conn->prepare('UPDATE sanciones SET activa = 0 WHERE id = ?');
        $stmt->bind_param('i', $id_san);
        $stmt->execute();
        $stmt->close();
        $msg = 'success|Sanción levantada.';
    }
}

// Listar usuarios (para el formulario)
$usuarios = $conn->query('SELECT id, usuario_registro FROM registro_usuario ORDER BY usuario_registro ASC')->fetch_all(MYSQLI_ASSOC);

// Listar sanciones activas
$sanciones = $conn->query("
    SELECT s.id, s.tipo_sancion, s.motivo, s.fecha, s.activa,
           u_san.usuario_registro AS sancionado,
           u_aut.usuario_registro AS autor
    FROM sanciones s
    LEFT JOIN registro_usuario u_san ON s.id_sancionado = u_san.id
    LEFT JOIN registro_usuario u_aut ON s.id_autor = u_aut.id
    ORDER BY s.fecha DESC
    LIMIT 50
")->fetch_all(MYSQLI_ASSOC);
?>
<body class="bg-theme">
<div class="content-wrapper">
  <div class="container-fluid">

    <?php if ($msg): list($tm,$tx)=explode('|',$msg,2); ?>
    <div class="alert alert-<?= $tm==='success'?'success':'danger' ?> mt-3"><?= htmlspecialchars($tx) ?></div>
    <?php endif; ?>

    <!-- Formulario nueva sanción -->
    <div class="card text-white mt-3" style="background:#5e4b8a;border:2px solid #a57db5;border-radius:10px;">
      <div class="card-header"><i class="fa fa-ban"></i> Registrar sanción</div>
      <div class="card-body">
        <form method="POST">
          <input type="hidden" name="accion" value="sancionar">
          <div class="row">
            <div class="col-12 col-md-4 mb-2">
              <label>Usuario a sancionar</label>
              <select name="id_sancionado" class="form-control" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($usuarios as $u): ?>
                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['usuario_registro']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-4 mb-2">
              <label>Tipo de sanción</label>
              <select name="tipo_sancion" class="form-control">
                <option value="advertencia">⚠️ Advertencia</option>
                <option value="suspension_temporal">⏸️ Suspensión temporal</option>
                <option value="suspension_permanente">🚫 Suspensión permanente</option>
                <option value="degradacion">⬇️ Degradación de rango</option>
              </select>
            </div>
            <div class="col-12 col-md-4 mb-2">
              <label>Motivo</label>
              <input type="text" name="motivo" class="form-control" placeholder="Describe el motivo" required>
            </div>
          </div>
          <button type="submit" class="btn btn-warning"><i class="fa fa-gavel"></i> Aplicar sanción</button>
        </form>
      </div>
    </div>

    <!-- Historial de sanciones -->
    <div class="card text-white mt-3" style="background:#2c2f33;border-radius:10px;">
      <div class="card-header"><i class="fa fa-list"></i> Historial de sanciones</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="color:#fff;">
          <thead style="background:#444;">
            <tr>
              <th>Sancionado</th><th>Tipo</th><th>Motivo</th><th>Autor</th><th>Fecha</th><th>Estado</th><th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($sanciones)): ?>
            <tr><td colspan="7" class="text-center text-muted">No hay sanciones registradas.</td></tr>
            <?php else: ?>
            <?php foreach ($sanciones as $s): ?>
            <tr>
              <td><?= htmlspecialchars($s['sancionado'] ?? 'N/D') ?></td>
              <td><?= htmlspecialchars($s['tipo_sancion']) ?></td>
              <td><?= htmlspecialchars($s['motivo']) ?></td>
              <td><?= htmlspecialchars($s['autor'] ?? 'N/D') ?></td>
              <td><?= $s['fecha'] ? date('d/m/Y H:i', strtotime($s['fecha'])) : '' ?></td>
              <td>
                <span class="badge" style="background:<?= $s['activa'] ? '#dc3545' : '#6c757d' ?>">
                  <?= $s['activa'] ? 'Activa' : 'Levantada' ?>
                </span>
              </td>
              <td>
                <?php if ($s['activa'] && $_SESSION['rol_id'] >= 3): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="accion" value="levantar">
                  <input type="hidden" name="id_san" value="<?= (int)$s['id'] ?>">
                  <button type="submit" class="btn btn-sm btn-success" title="Levantar sanción">
                    <i class="fa fa-check"></i>
                  </button>
                </form>
                <?php else: ?>—<?php endif; ?>
              </td>
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
