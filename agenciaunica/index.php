<?php
// session_start() siempre primero
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once('./private/procesos/db.php');

// Obtener últimas 3 noticias
$noticias = [];
$res = mysqli_query($conn, "SELECT titulo, descripcion, DATE_FORMAT(fecha_publicacion,'%d/%m/%Y') as fecha FROM noticias ORDER BY fecha_publicacion DESC LIMIT 3");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $noticias[] = $row;
    }
}

// Obtener staff
$staff = [];
$res2 = mysqli_query($conn, "SELECT usuario, habbo FROM modificar_administradores ORDER BY id ASC LIMIT 6");
if ($res2) {
    while ($row = mysqli_fetch_assoc($res2)) {
        $staff[] = $row;
    }
}

// Total usuarios
$total_usuarios = 0;
$res3 = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios");
if ($res3) {
    $total_usuarios = mysqli_fetch_assoc($res3)['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>AGENCIA HABBO: TWITCH</title>

  <!-- Favicon -->
  <link rel="icon" href="/private/assets/images/favicon.png" type="image/x-icon">

  <!-- Bootstrap -->
  <link href="/private/assets/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Icons -->
  <link href="/private/assets/css/icons.css" rel="stylesheet" type="text/css" />

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* =============================================
       TEMA VERANO NEON — basado en neon_login_registre.css
       y app-style.css originales
       ============================================= */

    /* Animaciones neon — paleta verano (cyan, naranja, amarillo, verde, magenta) */
    @keyframes neonGlow {
      0%   { color: #00ff99; box-shadow: 0 0 25px #00ff99; }
      20%  { color: #00e5ff; box-shadow: 0 0 25px #00e5ff; }
      40%  { color: #ffeb3b; box-shadow: 0 0 25px #ffeb3b; }
      60%  { color: #ff6d00; box-shadow: 0 0 25px #ff6d00; }
      80%  { color: #ff4081; box-shadow: 0 0 25px #ff4081; }
      100% { color: #00ff99; box-shadow: 0 0 25px #00ff99; }
    }

    @keyframes bgColorChange {
      0%   { background-color: #00ff99; }
      20%  { background-color: #00e5ff; }
      40%  { background-color: #ffeb3b; }
      60%  { background-color: #ff6d00; }
      80%  { background-color: #ff4081; }
      100% { background-color: #00ff99; }
    }

    @keyframes borderNeon {
      0%   { border-color: #00ff99; box-shadow: 0 0 10px #00ff99, 0 0 20px #00ff99; }
      20%  { border-color: #00e5ff; box-shadow: 0 0 10px #00e5ff, 0 0 20px #00e5ff; }
      40%  { border-color: #ffeb3b; box-shadow: 0 0 10px #ffeb3b, 0 0 20px #ffeb3b; }
      60%  { border-color: #ff6d00; box-shadow: 0 0 10px #ff6d00, 0 0 20px #ff6d00; }
      80%  { border-color: #ff4081; box-shadow: 0 0 10px #ff4081, 0 0 20px #ff4081; }
      100% { border-color: #00ff99; box-shadow: 0 0 10px #00ff99, 0 0 20px #00ff99; }
    }

    @keyframes floatUp {
      0%, 100% { transform: translateY(0px); }
      50%       { transform: translateY(-8px); }
    }

    @keyframes sunRay {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* BASE — igual que app-style.css */
    html, body {
      height: 100%;
      font-family: 'Roboto', sans-serif;
      font-size: 15px;
      letter-spacing: 0.5px;
    }

    body {
      background-color: #0f0c29;
      background-image: linear-gradient(315deg, #0f0c29 0%, #1a0a2e 40%, #0d1a2e 74%);
      color: rgba(255,255,255,.90);
      min-height: 100vh;
    }

    /* NAVBAR */
    .navbar-landing {
      background-color: rgba(0,0,0,.4);
      border-bottom: 1px solid rgba(0,255,153,.25);
      box-shadow: 0 2px 20px rgba(0,255,153,.1);
      padding: 0 20px;
      height: 65px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 999;
      backdrop-filter: blur(10px);
    }

    .navbar-brand-landing {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .navbar-brand-landing img {
      width: 40px;
      height: 40px;
      animation: borderNeon 5s infinite;
      border-radius: 50%;
      border: 2px solid #00ff99;
    }

    .navbar-brand-landing span {
      font-size: 17px;
      font-weight: 700;
      text-transform: uppercase;
      animation: neonGlow 5s infinite;
      letter-spacing: 2px;
    }

    .navbar-links {
      display: flex;
      align-items: center;
      gap: 12px;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .navbar-links a {
      color: rgba(255,255,255,.8);
      text-decoration: none;
      font-size: 14px;
      padding: 6px 14px;
      border-radius: 20px;
      transition: all 0.3s ease;
      border: 1px solid transparent;
    }

    .navbar-links a:hover {
      border-color: #00ff99;
      color: #00ff99;
      text-shadow: 0 0 8px #00ff99;
    }

    .btn-nav-login {
      background-color: transparent;
      border: 1px solid #00ff99 !important;
      color: #00ff99 !important;
      padding: 6px 18px;
      border-radius: 20px;
      font-size: 14px;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-nav-login:hover {
      background-color: #00ff99;
      color: #0f0c29 !important;
      box-shadow: 0 0 15px #00ff99;
    }

    .btn-nav-register {
      color: #fff !important;
      padding: 6px 18px;
      border-radius: 20px;
      font-size: 14px;
      text-decoration: none;
      border: none !important;
      animation: bgColorChange 5s infinite;
      transition: all 0.3s ease;
      box-shadow: 0 0 10px rgba(0,255,153,.4);
    }

    .btn-nav-register:hover {
      box-shadow: 0 0 20px rgba(0,255,153,.8);
      transform: scale(1.05);
      color: #000 !important;
    }

    /* HERO */
    .hero-section {
      min-height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 60px 20px;
      position: relative;
      overflow: hidden;
    }

    /* Fondo verano: olas de gradiente animado */
    .hero-section::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(ellipse at 20% 50%, rgba(0,255,153,.08) 0%, transparent 50%),
                  radial-gradient(ellipse at 80% 20%, rgba(0,229,255,.08) 0%, transparent 50%),
                  radial-gradient(ellipse at 50% 80%, rgba(255,235,59,.06) 0%, transparent 50%);
      animation: sunRay 20s linear infinite;
      pointer-events: none;
    }

    .hero-content {
      position: relative;
      z-index: 1;
      max-width: 700px;
    }

    .hero-logo {
      width: 110px;
      height: 110px;
      margin: 0 auto 25px;
      animation: floatUp 3s ease-in-out infinite, borderNeon 5s infinite;
      border-radius: 50%;
      border: 3px solid #00ff99;
      display: block;
      padding: 5px;
    }

    .hero-title {
      font-size: clamp(1.8rem, 5vw, 3rem);
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 3px;
      animation: neonGlow 5s infinite;
      text-shadow: 0 0 10px currentColor, 0 0 20px currentColor, 0 0 30px currentColor;
      margin-bottom: 15px;
    }

    .hero-subtitle {
      font-size: 1rem;
      color: rgba(255,255,255,.75);
      margin-bottom: 35px;
      line-height: 1.7;
    }

    .hero-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-hero-primary {
      padding: 12px 35px;
      border-radius: 30px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      color: #0f0c29;
      animation: bgColorChange 5s infinite;
      box-shadow: 0 0 20px rgba(0,255,153,.5);
      border: none;
      transition: all 0.3s ease;
      letter-spacing: 1px;
    }

    .btn-hero-primary:hover {
      transform: scale(1.05);
      box-shadow: 0 0 35px rgba(0,255,153,.8);
      color: #000;
    }

    .btn-hero-secondary {
      padding: 12px 35px;
      border-radius: 30px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      color: #00ff99;
      background: transparent;
      border: 2px solid #00ff99;
      box-shadow: 0 0 10px rgba(0,255,153,.3);
      transition: all 0.3s ease;
      letter-spacing: 1px;
      animation: borderNeon 5s infinite;
    }

    .btn-hero-secondary:hover {
      background: rgba(0,255,153,.15);
      box-shadow: 0 0 25px rgba(0,255,153,.6);
      color: #00ff99;
    }

    /* STATS */
    .stats-section {
      padding: 50px 20px;
      background: rgba(0,0,0,.25);
      border-top: 1px solid rgba(0,255,153,.15);
      border-bottom: 1px solid rgba(0,255,153,.15);
    }

    .stat-card {
      background-color: rgba(0,0,0,.3);
      border: 1px solid;
      border-radius: 15px;
      padding: 30px 20px;
      text-align: center;
      animation: borderNeon 5s infinite;
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 900;
      animation: neonGlow 5s infinite;
      text-shadow: 0 0 15px currentColor;
      display: block;
    }

    .stat-label {
      font-size: 13px;
      color: rgba(255,255,255,.65);
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-top: 5px;
    }

    /* NOTICIAS */
    .section-title {
      font-size: 1.6rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 2px;
      animation: neonGlow 5s infinite;
      text-shadow: 0 0 10px currentColor;
      text-align: center;
      margin-bottom: 10px;
    }

    .section-subtitle {
      text-align: center;
      color: rgba(255,255,255,.55);
      font-size: 14px;
      margin-bottom: 40px;
    }

    .news-card {
      background-color: rgba(0,0,0,.35);
      border: 1px solid rgba(0,255,153,.25);
      border-radius: 15px;
      padding: 25px;
      height: 100%;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .news-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      animation: bgColorChange 5s infinite;
    }

    .news-card:hover {
      transform: translateY(-5px);
      border-color: rgba(0,255,153,.5);
      box-shadow: 0 10px 30px rgba(0,255,153,.15);
    }

    .news-date {
      font-size: 11px;
      color: rgba(255,255,255,.45);
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 10px;
    }

    .news-title {
      font-size: 15px;
      font-weight: 700;
      color: #00ff99;
      margin-bottom: 12px;
      text-shadow: 0 0 8px rgba(0,255,153,.4);
    }

    .news-desc {
      font-size: 13px;
      color: rgba(255,255,255,.65);
      line-height: 1.6;
    }

    /* STAFF */
    .staff-card {
      background-color: rgba(0,0,0,.3);
      border: 1px solid;
      border-radius: 15px;
      padding: 20px 15px;
      text-align: center;
      animation: borderNeon 5s infinite;
      transition: all 0.3s ease;
    }

    .staff-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 30px rgba(0,255,153,.2);
    }

    .staff-avatar {
      width: 64px;
      height: 64px;
      border-radius: 50%;
      border: 2px solid #00ff99;
      animation: borderNeon 5s infinite;
      margin: 0 auto 10px;
      display: block;
      object-fit: cover;
      background: rgba(0,0,0,.3);
    }

    .staff-name {
      font-size: 13px;
      font-weight: 700;
      color: #00ff99;
      text-shadow: 0 0 6px rgba(0,255,153,.5);
    }

    .staff-habbo {
      font-size: 11px;
      color: rgba(255,255,255,.5);
      margin-top: 3px;
    }

    /* REDES */
    .social-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 12px 28px;
      border-radius: 30px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      border: 2px solid;
      letter-spacing: 0.5px;
    }

    .social-btn-wa  { color: #25d366; border-color: #25d366; }
    .social-btn-ig  { color: #e1306c; border-color: #e1306c; }
    .social-btn-tw  { color: #1da1f2; border-color: #1da1f2; }
    .social-btn-dc  { color: #7289da; border-color: #7289da; }

    .social-btn-wa:hover  { background: #25d366; color: #000; box-shadow: 0 0 20px rgba(37,211,102,.5); }
    .social-btn-ig:hover  { background: #e1306c; color: #fff; box-shadow: 0 0 20px rgba(225,48,108,.5); }
    .social-btn-tw:hover  { background: #1da1f2; color: #fff; box-shadow: 0 0 20px rgba(29,161,242,.5); }
    .social-btn-dc:hover  { background: #7289da; color: #fff; box-shadow: 0 0 20px rgba(114,137,218,.5); }

    /* FOOTER */
    .footer-landing {
      background: rgba(0,0,0,.5);
      border-top: 1px solid rgba(0,255,153,.2);
      padding: 30px 20px;
      text-align: center;
    }

    .footer-links a {
      color: rgba(255,255,255,.55);
      text-decoration: none;
      font-size: 13px;
      margin: 0 12px;
      transition: color 0.3s;
    }

    .footer-links a:hover {
      color: #00ff99;
      text-shadow: 0 0 8px #00ff99;
    }

    .footer-copy {
      color: rgba(255,255,255,.3);
      font-size: 12px;
      margin-top: 15px;
    }

    /* DECORACION VERANO — burbujas flotantes */
    .bubble {
      position: fixed;
      border-radius: 50%;
      opacity: 0.07;
      pointer-events: none;
      animation: floatUp 6s ease-in-out infinite;
    }
    .bubble-1 { width: 300px; height: 300px; background: radial-gradient(circle, #00ff99, transparent); top: 10%; left: -80px; animation-delay: 0s; }
    .bubble-2 { width: 200px; height: 200px; background: radial-gradient(circle, #00e5ff, transparent); top: 50%; right: -50px; animation-delay: 2s; }
    .bubble-3 { width: 150px; height: 150px; background: radial-gradient(circle, #ffeb3b, transparent); bottom: 10%; left: 30%; animation-delay: 4s; }

    /* Responsive navbar */
    @media (max-width: 768px) {
      .navbar-landing { flex-wrap: wrap; height: auto; padding: 12px 15px; gap: 10px; }
      .navbar-links   { gap: 6px; flex-wrap: wrap; }
      .navbar-links a { font-size: 12px; padding: 4px 10px; }
    }
  </style>
</head>
<body>

<!-- Burbujas decorativas verano -->
<div class="bubble bubble-1"></div>
<div class="bubble bubble-2"></div>
<div class="bubble bubble-3"></div>

<!-- ===== NAVBAR ===== -->
<nav class="navbar-landing">
  <a href="/index.php" class="navbar-brand-landing">
    <img src="/private/assets/images/logo-icon.png" alt="Logo">
    <span>Agencia Habbo</span>
  </a>

  <ul class="navbar-links">
    <li><a href="#noticias"><i class="fa fa-newspaper mr-1"></i> Noticias</a></li>
    <li><a href="#staff"><i class="fa fa-users mr-1"></i> Staff</a></li>
    <li><a href="#redes"><i class="fa fa-share-alt mr-1"></i> Redes</a></li>
    <?php if (isset($_SESSION['usuario'])): ?>
      <li><a href="/panel.php" class="btn-nav-login"><i class="fa fa-th-large mr-1"></i> Panel</a></li>
      <li><a href="/private/procesos/cerrar_sesion.php" class="btn-nav-register"><i class="fa fa-sign-out-alt mr-1"></i> Salir</a></li>
    <?php else: ?>
      <li><a href="/login.php" class="btn-nav-login"><i class="fa fa-sign-in-alt mr-1"></i> Entrar</a></li>
      <li><a href="/register.php" class="btn-nav-register"><i class="fa fa-user-plus mr-1"></i> Registro</a></li>
    <?php endif; ?>
  </ul>
</nav>

<!-- ===== HERO ===== -->
<section class="hero-section">
  <div class="hero-content">
    <img src="/private/assets/images/logo-icon.png" alt="Logo Agencia Habbo" class="hero-logo">
    <h1 class="hero-title">☀️ Agencia Habbo Twitch</h1>
    <p class="hero-subtitle">
      La agencia líder de Habbo. Únete a nuestro equipo de staff,<br>
      crece con nosotros y sé parte de algo único este verano. 🌊
    </p>
    <div class="hero-buttons">
      <?php if (isset($_SESSION['usuario'])): ?>
        <a href="/panel.php" class="btn-hero-primary"><i class="fa fa-th-large mr-2"></i> Ir al Panel</a>
      <?php else: ?>
        <a href="/register.php" class="btn-hero-primary"><i class="fa fa-user-plus mr-2"></i> Únete Ahora</a>
        <a href="/login.php" class="btn-hero-secondary"><i class="fa fa-sign-in-alt mr-2"></i> Iniciar Sesión</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats-section">
  <div class="container">
    <div class="row text-center justify-content-center g-4">
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <span class="stat-number"><?php echo number_format($total_usuarios); ?></span>
          <span class="stat-label">Usuarios Registrados</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <span class="stat-number"><?php echo count($staff); ?>+</span>
          <span class="stat-label">Staff Activo</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <span class="stat-number"><?php echo count($noticias); ?></span>
          <span class="stat-label">Últimas Noticias</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-card">
          <span class="stat-number">24/7</span>
          <span class="stat-label">Siempre Activos</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===== NOTICIAS ===== -->
<section id="noticias" style="padding: 70px 20px;">
  <div class="container">
    <h2 class="section-title">📰 Últimas Noticias</h2>
    <p class="section-subtitle">Lo más reciente de la agencia</p>

    <?php if (!empty($noticias)): ?>
    <div class="row g-4">
      <?php foreach ($noticias as $noticia): ?>
      <div class="col-md-4">
        <div class="news-card">
          <div class="news-date"><i class="fa fa-calendar-alt mr-1"></i><?php echo htmlspecialchars($noticia['fecha']); ?></div>
          <div class="news-title"><?php echo htmlspecialchars($noticia['titulo']); ?></div>
          <p class="news-desc"><?php echo nl2br(htmlspecialchars(substr($noticia['descripcion'], 0, 150))); ?>...</p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center" style="padding:40px; color:rgba(255,255,255,.4);">
      <i class="fa fa-newspaper fa-3x mb-3" style="color:rgba(0,255,153,.3);"></i>
      <p>No hay noticias por el momento. ¡Vuelve pronto!</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===== STAFF ===== -->
<section id="staff" style="padding: 70px 20px; background: rgba(0,0,0,.2);">
  <div class="container">
    <h2 class="section-title">🌟 Nuestro Staff</h2>
    <p class="section-subtitle">El equipo que hace posible la agencia</p>

    <?php if (!empty($staff)): ?>
    <div class="row g-4 justify-content-center">
      <?php foreach ($staff as $miembro): ?>
      <div class="col-6 col-md-4 col-lg-2">
        <div class="staff-card">
          <img
            src="https://www.habbo.es/habbo-imaging/avatarimage?figure=<?php echo urlencode($miembro['habbo'] ?? 'hd-180-1.ch-215-62.lg-275-62'); ?>&action=std&gesture=sml&direction=2&head_direction=2&size=b"
            alt="<?php echo htmlspecialchars($miembro['usuario']); ?>"
            class="staff-avatar"
            onerror="this.src='https://www.habbo.es/habbo-imaging/avatarimage?figure=hd-180-1.ch-215-62.lg-275-62&action=std&gesture=sml&direction=2&head_direction=2&size=b'"
          >
          <div class="staff-name"><?php echo htmlspecialchars($miembro['usuario']); ?></div>
          <div class="staff-habbo">🎮 <?php echo htmlspecialchars($miembro['habbo'] ?? ''); ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center" style="padding:40px; color:rgba(255,255,255,.4);">
      <i class="fa fa-users fa-3x mb-3" style="color:rgba(0,255,153,.3);"></i>
      <p>El staff se presentará próximamente.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ===== REDES SOCIALES ===== -->
<section id="redes" style="padding: 70px 20px;">
  <div class="container">
    <h2 class="section-title">🌐 Síguenos</h2>
    <p class="section-subtitle">Conéctate con la comunidad</p>
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="#" class="social-btn social-btn-wa" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-whatsapp"></i> WhatsApp
      </a>
      <a href="#" class="social-btn social-btn-ig" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-instagram"></i> Instagram
      </a>
      <a href="#" class="social-btn social-btn-tw" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-twitter"></i> Twitter/X
      </a>
      <a href="#" class="social-btn social-btn-dc" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-discord"></i> Discord
      </a>
    </div>
  </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer-landing">
  <div class="footer-links">
    <?php if (isset($_SESSION['usuario'])): ?>
      <a href="/panel.php"><i class="fa fa-th-large"></i> Panel</a>
      <a href="/private/procesos/cerrar_sesion.php"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
    <?php else: ?>
      <a href="/login.php"><i class="fa fa-sign-in-alt"></i> Iniciar Sesión</a>
      <a href="/register.php"><i class="fa fa-user-plus"></i> Registrarse</a>
    <?php endif; ?>
    <a href="#noticias"><i class="fa fa-newspaper"></i> Noticias</a>
    <a href="#staff"><i class="fa fa-users"></i> Staff</a>
    <a href="#redes"><i class="fa fa-share-alt"></i> Redes</a>
  </div>
  <p class="footer-copy">☀️ &copy; <?php echo date('Y'); ?> Agencia Habbo Twitch — Todos los derechos reservados</p>
</footer>

</body>
</html>
