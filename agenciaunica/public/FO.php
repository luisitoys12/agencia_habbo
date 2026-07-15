<?php
/**
 * FO.php v3 — Dashboard con KPIs reales, feed de actividad y top trabajadores.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . '/../private/procesos/db.php');

// === KPIs reales ===
$kpi = [];

// Total usuarios registrados
$r = $conn->query('SELECT COUNT(*) AS c FROM registro_usuario'); $kpi['usuarios'] = $r->fetch_assoc()['c'] ?? 0;

// Ascensos del mes actual
$r = $conn->query("SELECT COUNT(*) AS c FROM ascensos WHERE MONTH(fecha_ascenso)=MONTH(NOW()) AND YEAR(fecha_ascenso)=YEAR(NOW())"); $kpi['ascensos_mes'] = $r->fetch_assoc()['c'] ?? 0;

// Tiempos registrados este mes
$r = $conn->query("SELECT COUNT(*) AS c FROM tiempos_de_paga WHERE MONTH(fecha_tiempo)=MONTH(NOW()) AND YEAR(fecha_tiempo)=YEAR(NOW())"); $kpi['tiempos_mes'] = $r->fetch_assoc()['c'] ?? 0;

// Sanciones activas
$san = $conn->query('SELECT COUNT(*) AS c FROM sanciones WHERE activa=1');
$kpi['sanciones'] = $san ? ($san->fetch_assoc()['c'] ?? 0) : 0;

// === Feed actividad reciente (últimas 10) ===
$feed_query = $conn->query("
    SELECT ar.descripcion, ar.fecha, u.usuario_registro
    FROM actividad_reciente ar
    LEFT JOIN registro_usuario u ON ar.id_usuario = u.id
    ORDER BY ar.fecha DESC
    LIMIT 10
");
$feed = $feed_query ? $feed_query->fetch_all(MYSQLI_ASSOC) : [];

// === Top 3 trabajadores del mes ===
$top_query = $conn->query("
    SELECT u.id, u.usuario_registro,
           r.rango AS rango_asignado,
           COALESCE(a.total_ascensos,0) AS total_ascensos,
           COALESCE(t.total_tiempos,0) AS total_tiempos,
           COALESCE(a.total_ascensos,0)+COALESCE(t.total_tiempos,0) AS total_logros
    FROM registro_usuario u
    LEFT JOIN (SELECT id_usuario_encargado, COUNT(*) AS total_ascensos FROM ascensos GROUP BY id_usuario_encargado) a ON u.id=a.id_usuario_encargado
    LEFT JOIN (SELECT id_usuario_encargado, COUNT(*) AS total_tiempos FROM tiempos_de_paga GROUP BY id_usuario_encargado) t ON u.id=t.id_usuario_encargado
    LEFT JOIN rangos r ON u.Rango_asignado=r.id_rango
    ORDER BY total_logros DESC
    LIMIT 3
");
$usuarios_top = $top_query ? $top_query->fetch_all(MYSQLI_ASSOC) : [];

// === Última noticia, actualización y blog ===
$tipos_pub = ['noticia'=>null,'actualizacion'=>null,'blog'=>null];
foreach (array_keys($tipos_pub) as $tp) {
    $stmt_p = $conn->prepare('SELECT titulo,descripcion,fecha FROM publicaciones WHERE tipo=? ORDER BY fecha DESC LIMIT 1');
    if ($stmt_p) {
        $stmt_p->bind_param('s', $tp);
        $stmt_p->execute();
        $tipos_pub[$tp] = $stmt_p->get_result()->fetch_assoc();
        $stmt_p->close();
    }
}
$noticia      = $tipos_pub['noticia']      ?? ['titulo'=>'Sin noticias','descripcion'=>'','fecha'=>''];
$actualizacion= $tipos_pub['actualizacion']?? ['titulo'=>'Sin actualizaciones','descripcion'=>'','fecha'=>''];
$blog         = $tipos_pub['blog']         ?? ['titulo'=>'Sin posts','descripcion'=>'','fecha'=>''];
?>

<body class="bg-theme">
<div class="content-wrapper">
  <div class="container-fluid">

    <!-- ===== KPIs reales ===== -->
    <div class="card mt-3" style="background:#5e4b8a;border:2px solid #a57db5;border-radius:10px;">
      <div class="card-body">
        <div class="row text-center">
          <div class="col-6 col-lg-3 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h4 class="text-white mb-0"><?= number_format($kpi['usuarios']) ?> <span class="float-right"><i class="fa fa-users"></i></span></h4>
              <div class="progress my-2" style="height:3px;"><div class="progress-bar bg-info" style="width:100%;"></div></div>
              <p class="mb-0 text-white small">Usuarios registrados</p>
            </div>
          </div>
          <div class="col-6 col-lg-3 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h4 class="text-white mb-0"><?= number_format($kpi['ascensos_mes']) ?> <span class="float-right"><i class="fa fa-arrow-up"></i></span></h4>
              <div class="progress my-2" style="height:3px;"><div class="progress-bar bg-warning" style="width:70%;"></div></div>
              <p class="mb-0 text-white small">Ascensos este mes</p>
            </div>
          </div>
          <div class="col-6 col-lg-3 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h4 class="text-white mb-0"><?= number_format($kpi['tiempos_mes']) ?> <span class="float-right"><i class="fa fa-clock-o"></i></span></h4>
              <div class="progress my-2" style="height:3px;"><div class="progress-bar bg-success" style="width:60%;"></div></div>
              <p class="mb-0 text-white small">Tiempos registrados</p>
            </div>
          </div>
          <div class="col-6 col-lg-3 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h4 class="text-<?= $kpi['sanciones']>0?'danger':'success' ?> mb-0"><?= (int)$kpi['sanciones'] ?> <span class="float-right"><i class="fa fa-ban"></i></span></h4>
              <div class="progress my-2" style="height:3px;"><div class="progress-bar bg-danger" style="width:<?= min(100,$kpi['sanciones']*10) ?>%;"></div></div>
              <p class="mb-0 text-white small">Sanciones activas</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Fila: Feed actividad + Top trabajadores ===== -->
    <div class="row mt-3">

      <!-- Feed de actividad reciente -->
      <div class="col-12 col-lg-8 mb-3">
        <div class="card text-white h-100" style="background:#2c2f33;border-radius:10px;">
          <div class="card-header" style="background:#3b3d42;border-radius:10px 10px 0 0;">
            <i class="fa fa-bell text-warning"></i> Actividad reciente
          </div>
          <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
            <?php if (empty($feed)): ?>
              <p class="text-muted p-3 mb-0">No hay actividad registrada aún.</p>
            <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($feed as $f): ?>
              <li class="list-group-item" style="background:#2c2f33;border-color:#444;color:#ddd;">
                <small class="text-muted" style="float:right;"><?= $f['fecha'] ? date('d/m H:i', strtotime($f['fecha'])) : '' ?></small>
                <strong style="color:#a57db5;"><?= htmlspecialchars($f['usuario_registro'] ?? 'Sistema') ?></strong>
                — <?= htmlspecialchars($f['descripcion']) ?>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Top 3 trabajadores del mes -->
      <div class="col-12 col-lg-4 mb-3">
        <div class="card text-white h-100" style="background:#2c2f33;border-radius:10px;">
          <div class="card-header" style="background:#3b3d42;border-radius:10px 10px 0 0;">
            <i class="fa fa-trophy text-warning"></i> Top trabajadores del mes
          </div>
          <div class="card-body">
            <div class="row text-center mb-2">
              <?php $trofeos=['text-warning','text-secondary','text-warning']; ?>
              <?php foreach ($usuarios_top as $idx=>$u): ?>
              <div class="col-4 text-white">
                <i class="fa fa-trophy fa-2x <?= $trofeos[$idx] ?>"></i>
                <p class="mt-1 mb-0 small"><?= ($idx+1) ?>º</p>
                <p class="small"><?= htmlspecialchars($u['usuario_registro']) ?></p>
                <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($u['usuario_registro']) ?>&direction=3&head_direction=3&gesture=sml&size=s"
                     alt="" width="50" height="70" loading="lazy" style="border-radius:50%;">
              </div>
              <?php endforeach; ?>
            </div>
            <div class="table-responsive">
              <table class="table table-sm mb-0" style="color:#ccc;font-size:0.8rem;">
                <thead><tr style="color:#a57db5;"><th>Usuario</th><th>Asc.</th><th>Tie.</th></tr></thead>
                <tbody>
                  <?php foreach ($usuarios_top as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['usuario_registro']) ?></td>
                    <td><?= $u['total_ascensos'] ?></td>
                    <td><?= $u['total_tiempos'] ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Últimas publicaciones ===== -->
    <div class="card mt-3" style="background:#2c2f33;border-radius:10px;">
      <div class="card-header text-white" style="background:#3b3d42;border-radius:10px 10px 0 0;">
        <i class="fa fa-newspaper-o text-info"></i> Últimas publicaciones
      </div>
      <div class="card-body">
        <div class="row">
          <?php foreach ([['Última Noticia','noticia',$noticia],['Actualización','actualizacion',$actualizacion],['Blog','blog',$blog]] as [$label,$_tipo,$pub]): ?>
          <div class="col-12 col-lg-4 mb-3">
            <div class="border-dark p-3 bg-dark rounded h-100">
              <h6 class="text-white mb-2"><i class="fa fa-circle" style="color:#a57db5;"></i> <?= $label ?></h6>
              <p class="text-white mb-1"><strong><?= htmlspecialchars($pub['titulo']) ?></strong></p>
              <p class="text-white small" style="max-height:80px;overflow:hidden;"><?= htmlspecialchars(substr($pub['descripcion'],0,200)) ?><?= strlen($pub['descripcion'])>200?'…':'' ?></p>
              <p class="text-muted small mb-0"><?= $pub['fecha'] ? date('d/m/Y', strtotime($pub['fecha'])) : '' ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- ===== Canales sociales ===== -->
    <div class="card mt-3" style="background:#2c2f33;border-radius:10px;">
      <div class="card-body">
        <div class="row text-center">
          <div class="col-12 col-lg-4 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h6 class="text-white">Canal de WhatsApp</h6>
              <p class="text-white small">Contáctanos para publicaciones o noticias</p>
              <a href="https://whatsapp.com/channel/0029Vajlw9FDTkK9NgHlz81t" target="_blank" rel="noopener" class="btn btn-success btn-sm mt-1">Entrar al canal</a>
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h6 class="text-white">Instagram</h6>
              <p class="text-white small">Síguenos para actualizaciones y novedades</p>
              <a href="https://www.instagram.com/twitchagency_hbb?igsh=emdjdW9rcDJwajZ3" target="_blank" rel="noopener" class="btn btn-info btn-sm mt-1">Visitar Instagram</a>
            </div>
          </div>
          <div class="col-12 col-lg-4 mb-3">
            <div class="border-dark p-3 bg-dark rounded">
              <h6 class="text-white">Twitter / X</h6>
              <p class="text-white small">Mantente al día con noticias en tiempo real</p>
              <a href="https://twitter.com" target="_blank" rel="noopener" class="btn btn-primary btn-sm mt-1">Visitar Twitter</a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
