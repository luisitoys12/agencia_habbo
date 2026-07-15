<?php
$nombre_usuario = $_SESSION['usuario'] ?? $_SESSION['usuario_registro'] ?? 'usuario';
$rol = $_SESSION['rol_id'] ?? 4;
$page_actual = $_GET['page'] ?? 'HOM';
$id_usuario = $_SESSION['id'] ?? 0;
$creditos = 0; $rango = 'Sin rango'; $mis_sanciones = 0;
if ($id_usuario) {
    $stmt = $conn->prepare('SELECT creditos FROM dinero_digital WHERE id_usuario=? LIMIT 1');
    if ($stmt) { $stmt->bind_param('i',$id_usuario); $stmt->execute(); $r=$stmt->get_result(); if($r&&$row=$r->fetch_assoc()) $creditos=$row['creditos']; $stmt->close(); }
    $stmt2 = $conn->prepare('SELECT r.rango FROM registro_usuario u LEFT JOIN rangos r ON u.Rango_asignado=r.id_rango WHERE u.id=? LIMIT 1');
    if ($stmt2) { $stmt2->bind_param('i',$id_usuario); $stmt2->execute(); $r2=$stmt2->get_result(); if($r2&&$row2=$r2->fetch_assoc()) $rango=$row2['rango']; $stmt2->close(); }
    $stmt3 = $conn->prepare('SELECT COUNT(*) AS c FROM sanciones WHERE id_sancionado=? AND activa=1');
    if ($stmt3) { $stmt3->bind_param('i',$id_usuario); $stmt3->execute(); $r3=$stmt3->get_result(); if($r3&&$row3=$r3->fetch_assoc()) $mis_sanciones=$row3['c']; $stmt3->close(); }
}
?>
<nav class="navbar navbar-dark fixed-top" style="background-color:#2d1b32;">
  <div class="container-fluid">
    <div class="d-flex justify-content-between w-100 align-items-center">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#" data-bs-toggle="offcanvas" data-bs-target="#userInfoCanvas" style="text-decoration:none;">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nombre_usuario) ?>&direction=3&head_direction=3&gesture=sml&size=s"
             alt="Avatar" width="36" height="50" style="border-radius:50%;border:2px solid #d4af37;" loading="lazy">
        <span style="color:#d4af37;font-size:0.85rem;"><?= htmlspecialchars($nombre_usuario) ?><br><small style="color:#a57db5;"><?= htmlspecialchars($rango) ?></small></span>
      </a>
      <img src="/agenciaunica/private/assets/images/favicon.png" alt="Logo" width="40" height="40" style="border-radius:50%;border:2px solid #d4af37;box-shadow:0 0 10px #d4af37;">
      <?php if ($mis_sanciones>0): ?><span class="badge bg-danger me-2"><i class="fa fa-ban"></i> <?= $mis_sanciones ?></span><?php endif; ?>
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu"><span class="navbar-toggler-icon"></span></button>
    </div>

    <!-- Offcanvas perfil -->
    <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="userInfoCanvas" style="background:#2d1b32;">
      <div class="offcanvas-header"><h5>Mi Perfil — Dueño</h5><button class="btn-close text-reset" data-bs-dismiss="offcanvas"></button></div>
      <div class="offcanvas-body text-center">
        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($nombre_usuario) ?>&direction=3&head_direction=3&gesture=sml&size=b" width="100" height="130" style="border-radius:50%;border:3px solid #d4af37;" loading="lazy">
        <h5 class="mt-2"><?= htmlspecialchars($nombre_usuario) ?></h5>
        <span class="badge" style="background:#a57db5;"><?= htmlspecialchars($rango) ?></span>
        <span class="badge ms-1 bg-danger">Dueño</span>
        <hr style="border-color:#a57db5;">
        <div style="background:#4e2a57;padding:10px;border-radius:12px;">
          <h6 class="text-white mb-1">Créditos</h6>
          <h3 class="text-warning mb-0">$ <?= number_format($creditos) ?></h3>
        </div>
        <hr style="border-color:#a57db5;">
        <a href="/index.php?page=PERFIL" class="btn btn-outline-light btn-sm w-100 mb-2"><i class="fa fa-user"></i> Ver perfil</a>
        <a href="/logout.php" class="btn btn-danger btn-sm w-100"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
      </div>
    </div>

    <!-- Offcanvas menú dueños -->
    <div class="offcanvas offcanvas-end text-white" style="background:#2d1b32;" tabindex="-1" id="offcanvasMenu">
      <div class="offcanvas-header"><h5>MENÚ — DUEÑOS</h5><button class="btn-close text-reset" data-bs-dismiss="offcanvas"></button></div>
      <div class="offcanvas-body">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link <?= $page_actual==='HOM'?'text-warning':'text-white' ?>" href="/index.php?page=HOM"><i class="fa fa-home me-2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='PERFIL'?'text-warning':'text-white' ?>" href="/index.php?page=PERFIL"><i class="fa fa-user me-2"></i>Mi Perfil</a></li>

          <li class="nav-item"><hr style="border-color:#a57db5;margin:8px 0;"></li>
          <li><small class="text-muted px-2" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Gestión</small></li>

          <li class="nav-item"><a class="nav-link <?= $page_actual==='GSU'?'text-warning':'text-white' ?>" href="/index.php?page=GSU"><i class="fa fa-users me-2"></i>Gestión Usuarios</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='RAG'?'text-warning':'text-white' ?>" href="/index.php?page=RAG"><i class="fa fa-shield me-2"></i>Rangos</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='GSP'?'text-warning':'text-white' ?>" href="/index.php?page=GSP"><i class="fa fa-money me-2"></i>Gestión Pagas</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='GVE'?'text-warning':'text-white' ?>" href="/index.php?page=GVE"><i class="fa fa-shopping-cart me-2"></i>Ventas</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='GVP'?'text-warning':'text-white' ?>" href="/index.php?page=GVP"><i class="fa fa-tag me-2"></i>Ventas Placas</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='DJ'?'text-warning':'text-white' ?>" href="/index.php?page=DJ"><i class="fa fa-music me-2"></i>DJs</a></li>

          <li class="nav-item"><hr style="border-color:#a57db5;margin:8px 0;"></li>
          <li><small class="text-muted px-2" style="font-size:0.7rem;text-transform:uppercase;letter-spacing:1px;">Administración</small></li>

          <li class="nav-item"><a class="nav-link <?= $page_actual==='NOT'?'text-warning':'text-white' ?>" href="/index.php?page=NOT"><i class="fa fa-newspaper-o me-2"></i>Noticias / Anuncios</a></li>
          <li class="nav-item"><a class="nav-link <?= $page_actual==='SAN'?'text-warning':'text-white' ?>" href="/index.php?page=SAN"><i class="fa fa-ban me-2"></i>Sanciones</a></li>

          <li class="nav-item mt-3">
            <a href="/logout.php" class="btn btn-danger w-100"><i class="fa fa-sign-out me-2"></i>Cerrar sesión</a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>
<?php require_once('../private/modal/sistema_de_ascenso.php'); ?>
<?php require_once('../private/modal/modaltomatime.php'); ?>
<style>body{padding-top:70px;} .navbar-toggler-icon{background-image:url('/agenciaunica/private/images/menu/ghost.png');background-size:cover;}</style>
