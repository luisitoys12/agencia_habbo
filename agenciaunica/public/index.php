<?php
/**
 * index.php — Página de inicio pública de la agencia.
 * Visible sin login. Si hay sesión, muestra boton "Ir al panel".
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once(__DIR__ . '/../private/procesos/db.php');

// Datos públicos para la landing
$total_usuarios = 0;
$r = $conn->query('SELECT COUNT(*) AS c FROM registro_usuario');
if ($r) $total_usuarios = $r->fetch_assoc()['c'] ?? 0;

$noticias = [];
$rn = $conn->query('SELECT titulo, contenido, autor, fecha FROM publicaciones ORDER BY fecha DESC LIMIT 3');
if ($rn) $noticias = $rn->fetch_all(MYSQLI_ASSOC);

$admins = [];
$ra = $conn->query('SELECT nombre, rango, cara, accion, bebida FROM modificar_administradores LIMIT 8');
if ($ra) $admins = $ra->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Agencia Habbo — Inicio</title>
  <link rel="stylesheet" href="/private/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="/private/assets/css/font-awesome.min.css">
  <link rel="stylesheet" href="/private/assets/css/style.css">
  <style>
    :root {
      --morado:  #5e4b8a;
      --morado2: #a57db5;
      --dark:    #1e1e2e;
      --card-bg: #2c2f33;
      --card-hd: #3b3d42;
    }
    body { background: var(--dark); color: #ddd; font-family: 'Segoe UI', sans-serif; }
    .navbar-brand img { height: 42px; }
    .hero {
      background: linear-gradient(135deg, #1e1e2e 0%, #3b1f5e 50%, #1e1e2e 100%);
      padding: 80px 20px 60px;
      text-align: center;
    }
    .hero h1 { font-size: clamp(1.8rem, 5vw, 3rem); color: #fff; text-shadow: 0 0 20px var(--morado2); }
    .hero p  { color: #c8b8e0; font-size: 1.1rem; max-width: 600px; margin: 0 auto 28px; }
    .btn-morado { background: var(--morado); color:#fff; border:2px solid var(--morado2); border-radius:30px; padding:10px 28px; font-weight:600; transition:.2s; }
    .btn-morado:hover { background: var(--morado2); color:#fff; }
    .btn-outline-morado { background:transparent; color:var(--morado2); border:2px solid var(--morado2); border-radius:30px; padding:10px 28px; font-weight:600; transition:.2s; }
    .btn-outline-morado:hover { background: var(--morado2); color:#fff; }
    .section-title { color: var(--morado2); font-size:1.3rem; font-weight:700; margin-bottom:20px; }
    .card-dark { background: var(--card-bg); border:1px solid #444; border-radius:12px; }
    .card-dark .card-header { background: var(--card-hd); border-radius:12px 12px 0 0; color:#ddd; font-weight:600; }
    .noticia-card { border-left:3px solid var(--morado2); padding:14px 16px; background:#252830; border-radius:0 8px 8px 0; margin-bottom:14px; }
    .noticia-card h6 { color:#fff; margin-bottom:4px; }
    .noticia-card p  { color:#bbb; font-size:.88rem; margin-bottom:4px; }
    .noticia-card small { color:#888; }
    .stat-box { background:#252830; border:1px solid #444; border-radius:10px; padding:20px; text-align:center; }
    .stat-box .num { font-size:2rem; font-weight:700; color:var(--morado2); }
    .stat-box .lbl { color:#aaa; font-size:.85rem; }
    .staff-avatar img { border-radius:50%; border:2px solid var(--morado2); }
    .staff-card { background:#252830; border-radius:10px; padding:16px 10px; text-align:center; }
    .staff-card .nick { color:#fff; font-weight:600; font-size:.9rem; margin-top:6px; }
    .staff-card .cargo { color:var(--morado2); font-size:.78rem; }
    .social-btn { display:inline-flex; align-items:center; gap:8px; border-radius:30px; padding:10px 22px; font-weight:600; font-size:.9rem; transition:.2s; text-decoration:none; }
    footer { background:#111; color:#888; text-align:center; padding:28px 20px; margin-top:60px; font-size:.85rem; }
    footer a { color:var(--morado2); text-decoration:none; }
    .navbar { background: #14121f; border-bottom:1px solid #333; }
    .nav-link { color:#ccc !important; }
    .nav-link:hover { color:var(--morado2) !important; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/index.php">
      <img src="/private/assets/images/logo-icon.png" alt="Logo">
      <span style="color:var(--morado2);font-weight:700;font-size:1.1rem;">Agencia Habbo</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"
            style="border-color:#555;">
      <span class="navbar-toggler-icon" style="filter:invert(1);"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item"><a class="nav-link" href="#noticias">Noticias</a></li>
        <li class="nav-item"><a class="nav-link" href="#staff">Staff</a></li>
        <li class="nav-item"><a class="nav-link" href="#redes">Redes</a></li>
        <?php if (isset($_SESSION['rol_id'])): ?>
          <li class="nav-item">
            <a class="btn btn-morado ms-2" href="/panel.php">Panel</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn-outline-morado ms-2" href="/login.php" style="display:inline-block;">Entrar</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-morado ms-1" href="/register.php">Registrarse</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <h1>🎮 Bienvenido a la Agencia Habbo</h1>
  <p>La comunidad más activa de Habbo en español. Ascensos, eventos, pagas y mucho más te esperan.</p>
  <?php if (isset($_SESSION['rol_id'])): ?>
    <a href="/panel.php" class="btn btn-morado me-2"><i class="fa fa-th-large"></i> Ir al panel</a>
  <?php else: ?>
    <a href="/register.php" class="btn btn-morado me-2"><i class="fa fa-user-plus"></i> Únete ahora</a>
    <a href="/login.php" class="btn-outline-morado" style="display:inline-block;"><i class="fa fa-sign-in"></i> Iniciar sesión</a>
  <?php endif; ?>
</section>

<!-- STATS -->
<section class="container mt-5">
  <div class="row g-3">
    <div class="col-6 col-md-3">
      <div class="stat-box">
        <div class="num"><?= number_format($total_usuarios) ?></div>
        <div class="lbl"><i class="fa fa-users"></i> Usuarios</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-box">
        <div class="num"><?= count($admins) ?></div>
        <div class="lbl"><i class="fa fa-shield"></i> Staff activo</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-box">
        <div class="num"><?= count($noticias) ?>+</div>
        <div class="lbl"><i class="fa fa-newspaper-o"></i> Noticias</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-box">
        <div class="num">24/7</div>
        <div class="lbl"><i class="fa fa-clock-o"></i> Activos</div>
      </div>
    </div>
  </div>
</section>

<!-- NOTICIAS -->
<section class="container mt-5" id="noticias">
  <div class="section-title"><i class="fa fa-newspaper-o"></i> Últimas noticias</div>
  <?php if (empty($noticias)): ?>
    <p class="text-muted">No hay noticias publicadas aún.</p>
  <?php else: ?>
    <?php foreach ($noticias as $n): ?>
    <div class="noticia-card">
      <h6><?= htmlspecialchars($n['titulo']) ?></h6>
      <p><?= htmlspecialchars(substr($n['contenido'] ?? $n['descripcion'] ?? '', 0, 220)) ?><?= strlen($n['contenido'] ?? '') > 220 ? '…' : '' ?></p>
      <small><i class="fa fa-user"></i> <?= htmlspecialchars($n['autor']) ?> &nbsp;|&nbsp;
             <i class="fa fa-calendar"></i> <?= $n['fecha'] ? date('d/m/Y', strtotime($n['fecha'])) : '' ?></small>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</section>

<!-- STAFF -->
<section class="container mt-5" id="staff">
  <div class="section-title"><i class="fa fa-shield"></i> Nuestro staff</div>
  <?php if (empty($admins)): ?>
    <p class="text-muted">Sin staff configurado aún.</p>
  <?php else: ?>
  <div class="row g-3">
    <?php foreach ($admins as $ad): ?>
    <?php
      $qs = 'user='.urlencode($ad['nombre'])
           .'&direction=3&head_direction=3'
           .'&gesture='.urlencode($ad['cara'])
           .'&action='.urlencode($ad['accion'])
           .'&'.urlencode($ad['bebida'])
           .'&size=l';
    ?>
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <div class="staff-card">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?<?= $qs ?>"
             alt="<?= htmlspecialchars($ad['nombre']) ?>" width="64" height="110" loading="lazy"
             style="display:block;margin:0 auto;">
        <div class="nick"><?= htmlspecialchars($ad['nombre']) ?></div>
        <div class="cargo"><?= htmlspecialchars($ad['rango']) ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- REDES SOCIALES -->
<section class="container mt-5" id="redes">
  <div class="section-title"><i class="fa fa-share-alt"></i> Síguenos</div>
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="card-dark p-3 text-center">
        <i class="fa fa-whatsapp fa-2x text-success mb-2"></i>
        <h6 class="text-white">Canal de WhatsApp</h6>
        <p class="text-muted small">Contáctanos para publicaciones o noticias</p>
        <a href="https://whatsapp.com/channel/0029Vajlw9FDTkK9NgHlz81t" target="_blank" rel="noopener"
           class="social-btn" style="background:#25d366;color:#fff;"><i class="fa fa-whatsapp"></i> Entrar al canal</a>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card-dark p-3 text-center">
        <i class="fa fa-instagram fa-2x mb-2" style="color:#e1306c;"></i>
        <h6 class="text-white">Instagram</h6>
        <p class="text-muted small">Síguenos para actualizaciones y novedades</p>
        <a href="https://www.instagram.com/twitchagency_hbb?igsh=emdjdW9rcDJwajZ3" target="_blank" rel="noopener"
           class="social-btn" style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);color:#fff;">
          <i class="fa fa-instagram"></i> Visitar Instagram</a>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="card-dark p-3 text-center">
        <i class="fa fa-twitter fa-2x text-info mb-2"></i>
        <h6 class="text-white">Twitter / X</h6>
        <p class="text-muted small">Manténte al día con noticias en tiempo real</p>
        <a href="https://twitter.com" target="_blank" rel="noopener"
           class="social-btn" style="background:#1da1f2;color:#fff;"><i class="fa fa-twitter"></i> Visitar Twitter</a>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <p>&copy; <?= date('Y') ?> Agencia Habbo. Todos los derechos reservados.</p>
  <p>
    <a href="/login.php">Iniciar sesión</a> &nbsp;|&nbsp;
    <a href="/register.php">Registrarse</a>
    <?php if (isset($_SESSION['rol_id'])): ?>
      &nbsp;|&nbsp; <a href="/panel.php">Panel</a>
      &nbsp;|&nbsp; <a href="/logout.php">Cerrar sesión</a>
    <?php endif; ?>
  </p>
</footer>

<script src="/private/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
