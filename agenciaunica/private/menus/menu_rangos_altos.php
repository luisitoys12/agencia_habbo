<?php
$nombre_usuario = $_SESSION['usuario'] ?? $_SESSION['usuario_registro'] ?? 'usuario';
$rol = $_SESSION['rol_id'] ?? 1;
$page_actual = $_GET['page'] ?? 'HOM';

$roles = [1=>'Rango Bajo',2=>'Rango Medio',3=>'Rango Alto',4=>'Dueño'];
$rol_descripcion = $roles[$rol] ?? 'Desconocido';

// Consultar créditos y rango
$id_usuario = $_SESSION['id'] ?? 0;
$creditos = 0; $rango = 'Sin rango';
if ($id_usuario) {
    $stmt = $conn->prepare('SELECT creditos FROM dinero_digital WHERE id_usuario=? LIMIT 1');
    if ($stmt) { $stmt->bind_param('i',$id_usuario); $stmt->execute(); $r=$stmt->get_result(); if($r&&$row=$r->fetch_assoc()) $creditos=$row['creditos']; $stmt->close(); }
    $stmt2 = $conn->prepare('SELECT r.rango FROM registro_usuario u LEFT JOIN rangos r ON u.Rango_asignado=r.id_rango WHERE u.id=? LIMIT 1');
    if ($stmt2) { $stmt2->bind_param('i',$id_usuario); $stmt2->execute(); $r2=$stmt2->get_result(); if($r2&&$row2=$r2->fetch_assoc()) $rango=$row2['rango']; $stmt2->close(); }

    // Sanciones activas del usuario
    $stmt3 = $conn->prepare('SELECT COUNT(*) AS c FROM sanciones WHERE id_sancionado=? AND activa=1');
    $mis_sanciones = 0;
    if ($stmt3) { $stmt3->bind_param('i',$id_usuario); $stmt3->execute(); $r3=$stmt3->get_result(); if($r3&&$row3=$r3->fetch_assoc()) $mis_sanciones=$row3['c']; $stmt3->close(); }
}
?>

<nav class="navbar navbar-dark fixed-top" style="background-color:#2d1b32;" aria-label="Panel navbar">
  <div class="container-fluid">
    <div class="d-flex justify-content-between w-100 align-items-center">

      <!-- Perfil rápido -->
      <a class="navbar-brand d-flex align-items-center gap-2" href="#" data-bs-toggle="offcanvas" data-bs-target="#userInfoCanvas" style="text-decoration:none;">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nombre_usuario) ?>&direction=3&head_direction=3&gesture=sml&size=s"
             alt="Avatar" width="36" height="50" style="border-radius:50%;border:2px solid #d4af37;" loading="lazy">
        <span style="color:#d4af37;font-size:0.85rem;"><?= htmlspecialchars($nombre_usuario) ?><br>
          <small style="color:#a57db5;"><?= htmlspecialchars($rango) ?></small>
        </span>
      </a>

      <!-- Logo central -->
      <img src="/agenciaunica/private/assets/images/favicon.png" alt="Logo" width="40" height="40" style="border-radius:50%;border:2px solid #d4af37;box-shadow:0 0 10px #d4af37;">

      <?php if ($mis_sanciones > 0): ?>
      <span class="badge bg-danger me-2" title="Tienes sanciones activas"><i class="fa fa-ban"></i> <?= $mis_sanciones ?></span>
      <?php endif; ?>

      <!-- Hamburguesa -->
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu" aria-label="Menú">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

    <!-- ===== OFFCANVAS PERFIL ===== -->
    <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="userInfoCanvas" style="background:#2d1b32;">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">Mi Perfil</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body text-center">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nombre_usuario) ?>&direction=3&head_direction=3&gesture=sml&action=none&size=b"
             alt="Avatar" width="100" height="130" style="border-radius:50%;border:3px solid #d4af37;" loading="lazy">
        <h5 class="mt-2 text-white"><?= htmlspecialchars($nombre_usuario) ?></h5>
        <span class="badge" style="background:#a57db5;"><?= htmlspecialchars($rango) ?></span>
        <span class="badge ms-1" style="background:#4e2a57;"><?= htmlspecialchars($rol_descripcion) ?></span>
        <hr style="border-color:#a57db5;">
        <div class="virtual-money-container mt-2" style="background:#4e2a57;padding:10px 15px;border-radius:12px;">
          <h6 class="text-white mb-1"><i class="fa fa-coins"></i> Créditos</h6>
          <h3 class="text-warning mb-0">$ <?= number_format($creditos) ?></h3>
        </div>
        <?php if ($mis_sanciones > 0): ?>
        <div class="alert alert-danger mt-3 p-2" style="font-size:0.85rem;">
          <i class="fa fa-ban"></i> Tienes <strong><?= $mis_sanciones ?></strong> sanción(es) activa(s).
        </div>
        <?php endif; ?>
        <hr style="border-color:#a57db5;">
        <a href="/index.php?page=PERFIL" class="btn btn-outline-light btn-sm w-100 mb-2"><i class="fa fa-user"></i> Ver perfil completo</a>
        <a href="/logout.php" class="btn btn-danger btn-sm w-100"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
      </div>
    </div>

    <!-- ===== OFFCANVAS MENÚ ===== -->
    <div class="offcanvas offcanvas-end text-white" style="background:#2d1b32;" tabindex="-1" id="offcanvasMenu">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title">MENÚ — <?= htmlspecialchars($rol_descripcion) ?></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav flex-grow-1">

          <li class="nav-item">
            <a class="nav-link <?= $page_actual==='HOM'?'text-warning':'text-white' ?>" href="/index.php?page=HOM">
              <i class="fa fa-home me-2"></i>Dashboard
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= $page_actual==='PERFIL'?'text-warning':'text-white' ?>" href="/index.php?page=PERFIL">
              <i class="fa fa-user me-2"></i>Mi Perfil
            </a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
              <i class="fa fa-info-circle me-2"></i>Información
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="/index.php?page=RAG"><i class="fa fa-medal me-1"></i> Rangos</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown">
              <i class="fa fa-tachometer me-2"></i>Ascensos y Tiempo
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalAscenderPersona"><i class="fa fa-level-up me-1"></i> Toma de ascenso</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalTomaTime"><i class="fa fa-stopwatch me-1"></i> Toma de tiempo</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= $page_actual==='NOT'?'text-warning':'text-white' ?>" href="/index.php?page=NOT">
              <i class="fa fa-newspaper-o me-2"></i>Noticias
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= $page_actual==='DJ'?'text-warning':'text-white' ?>" href="/index.php?page=DJ">
              <i class="fa fa-music me-2"></i>DJs
            </a>
          </li>

          <li class="nav-item mt-3">
            <a href="/logout.php" class="btn btn-danger w-100">
              <i class="fa fa-sign-out me-2"></i>Cerrar sesión
            </a>
          </li>
        </ul>
      </div>
    </div>

  </div>
</nav>

<!--MODALES-->
<?php require_once('../private/modal/sistema_de_ascenso.php'); ?>
<?php require_once('../private/modal/modaltomatime.php'); ?>

<style>
  body { padding-top: 70px; }
  .navbar-nav .nav-link:hover { color: #fff !important; text-shadow: 0 0 8px #d4af37; }
  .navbar-toggler-icon { background-image: url('/agenciaunica/private/images/menu/ghost.png'); background-size: cover; }
</style>
