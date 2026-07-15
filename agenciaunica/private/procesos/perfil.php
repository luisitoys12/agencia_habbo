<?php
/**
 * perfil.php — Página de perfil del usuario autenticado.
 * Se carga desde index.php?page=PERFIL
 */
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) { header('Location: /login.php'); exit(); }

require_once(__DIR__ . '/db.php');

$id_usuario = $_SESSION['id'];

// Obtener datos completos del usuario
$stmt = $conn->prepare("
    SELECT u.id, u.usuario_registro, u.fecha_registro, u.ip_registro, u.rol_id,
           r.rango AS nombre_rango, r.id_rango
    FROM registro_usuario u
    LEFT JOIN rangos r ON u.Rango_asignado = r.id_rango
    WHERE u.id = ?
    LIMIT 1
");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$perfil = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Contar ascensos realizados
$stmt_asc = $conn->prepare('SELECT COUNT(*) FROM ascensos WHERE id_usuario_encargado = ?');
$stmt_asc->bind_param('i', $id_usuario);
$stmt_asc->execute();
$stmt_asc->bind_result($total_ascensos);
$stmt_asc->fetch();
$stmt_asc->close();

// Contar tiempos de paga
$stmt_tp = $conn->prepare('SELECT COUNT(*) FROM tiempos_de_paga WHERE id_usuario_encargado = ?');
$stmt_tp->bind_param('i', $id_usuario);
$stmt_tp->execute();
$stmt_tp->bind_result($total_tiempos);
$stmt_tp->fetch();
$stmt_tp->close();

// Obtener últimos 5 ascensos
$stmt_hist = $conn->prepare("
    SELECT a.fecha_ascenso, u.usuario_registro AS usuario_ascendido
    FROM ascensos a
    LEFT JOIN registro_usuario u ON a.id_usuario_ascendido = u.id
    WHERE a.id_usuario_encargado = ?
    ORDER BY a.fecha_ascenso DESC
    LIMIT 5
");
$stmt_hist->bind_param('i', $id_usuario);
$stmt_hist->execute();
$historial_ascensos = $stmt_hist->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_hist->close();

// Obtener últimos 5 tiempos registrados
$stmt_tp_hist = $conn->prepare("
    SELECT tp.fecha_tiempo, tp.descripcion
    FROM tiempos_de_paga tp
    WHERE tp.id_usuario_encargado = ?
    ORDER BY tp.fecha_tiempo DESC
    LIMIT 5
");
$stmt_tp_hist->bind_param('i', $id_usuario);
$stmt_tp_hist->execute();
$historial_tiempos = $stmt_tp_hist->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_tp_hist->close();

$nick = htmlspecialchars($perfil['usuario_registro'] ?? 'Desconocido');
$rango = htmlspecialchars($perfil['nombre_rango'] ?? 'Sin rango');
$fecha_reg = $perfil['fecha_registro'] ? date('d/m/Y', strtotime($perfil['fecha_registro'])) : 'N/D';
$total_logros = (int)$total_ascensos + (int)$total_tiempos;
?>

<body class="bg-theme">
<div class="content-wrapper">
  <div class="container-fluid">

    <div class="row mt-3">

      <!-- Tarjeta de perfil -->
      <div class="col-12 col-lg-4 mb-3">
        <div class="card text-white h-100" style="background-color:#5e4b8a;border:2px solid #a57db5;border-radius:10px;">
          <div class="card-body text-center">
            <!-- Avatar de Habbo -->
            <img
              src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nick) ?>&direction=3&head_direction=3&gesture=sml&action=&size=l"
              alt="Avatar de <?= $nick ?>"
              class="rounded-circle mb-3"
              style="width:120px;height:160px;background:#2c2f33;"
              loading="lazy">

            <h3 class="text-white mb-1"><?= $nick ?></h3>
            <span class="badge" style="background:#a57db5;font-size:0.95rem;padding:6px 14px;border-radius:20px;">
              <?= $rango ?>
            </span>

            <hr style="border-color:#a57db5;">

            <div class="row text-center mt-2">
              <div class="col-4">
                <h4 class="text-warning mb-0"><?= (int)$total_ascensos ?></h4>
                <small>Ascensos</small>
              </div>
              <div class="col-4">
                <h4 class="text-info mb-0"><?= (int)$total_tiempos ?></h4>
                <small>Tiempos</small>
              </div>
              <div class="col-4">
                <h4 class="text-success mb-0"><?= $total_logros ?></h4>
                <small>Logros</small>
              </div>
            </div>

            <hr style="border-color:#a57db5;">
            <p class="mb-1 small"><i class="fa fa-calendar"></i> Miembro desde: <strong><?= $fecha_reg ?></strong></p>
            <p class="mb-0 small"><i class="fa fa-shield"></i> Rol ID: <strong><?= (int)($perfil['rol_id'] ?? 0) ?></strong></p>
          </div>
        </div>
      </div>

      <!-- Historial y estadísticas -->
      <div class="col-12 col-lg-8">

        <!-- Últimos ascensos realizados -->
        <div class="card text-white mb-3" style="background-color:#2c2f33;border-radius:10px;">
          <div class="card-header" style="background:#3b3d42;border-radius:10px 10px 0 0;">
            <i class="fa fa-arrow-up text-warning"></i> Últimos ascensos que realizaste
          </div>
          <div class="card-body p-0">
            <?php if (!empty($historial_ascensos)): ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0" style="color:#fff;">
                <thead style="background:#444;">
                  <tr>
                    <th>Usuario ascendido</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($historial_ascensos as $a): ?>
                  <tr>
                    <td>
                      <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['usuario_ascendido'] ?? '') ?>&direction=3&head_direction=3&gesture=sml&size=s"
                           alt="" width="30" height="40" style="margin-right:8px;vertical-align:middle;" loading="lazy">
                      <?= htmlspecialchars($a['usuario_ascendido'] ?? 'N/D') ?>
                    </td>
                    <td><?= $a['fecha_ascenso'] ? date('d/m/Y H:i', strtotime($a['fecha_ascenso'])) : 'N/D' ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
              <p class="text-muted p-3 mb-0">Aún no has realizado ascensos.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Últimos tiempos registrados -->
        <div class="card text-white" style="background-color:#2c2f33;border-radius:10px;">
          <div class="card-header" style="background:#3b3d42;border-radius:10px 10px 0 0;">
            <i class="fa fa-clock-o text-info"></i> Últimos tiempos de paga registrados
          </div>
          <div class="card-body p-0">
            <?php if (!empty($historial_tiempos)): ?>
            <div class="table-responsive">
              <table class="table table-hover mb-0" style="color:#fff;">
                <thead style="background:#444;">
                  <tr>
                    <th>Descripción</th>
                    <th>Fecha</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($historial_tiempos as $t): ?>
                  <tr>
                    <td><?= htmlspecialchars($t['descripcion'] ?? 'Sin descripción') ?></td>
                    <td><?= $t['fecha_tiempo'] ? date('d/m/Y H:i', strtotime($t['fecha_tiempo'])) : 'N/D' ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
              <p class="text-muted p-3 mb-0">Aún no tienes tiempos registrados.</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>
</body>
