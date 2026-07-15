<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reino Hogwarz - Agencia Habbo</title>

    <link rel="shortcut icon" href="/private/assets/images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <style>
        :root {
            --color-bg: #0a0a0f;
            --color-bg2: #12121a;
            --color-card: #1a1a2e;
            --color-primary: #7c3aed;
            --color-secondary: #a855f7;
            --color-accent: #f59e0b;
            --color-text: #e2e8f0;
            --color-muted: #94a3b8;
            --color-border: #2d2d4e;
            --gradient: linear-gradient(135deg, #7c3aed, #a855f7, #f59e0b);
            --shadow-glow: 0 0 30px rgba(124, 58, 237, 0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--color-bg);
            color: var(--color-text);
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            background: rgba(10,10,15,0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--color-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar__logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .navbar__logo-icon {
            width: 42px;
            height: 42px;
            background: var(--gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .navbar__logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar__actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .btn {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
        }

        .btn--ghost {
            background: transparent;
            border: 1px solid var(--color-border);
            color: var(--color-text);
        }

        .btn--ghost:hover {
            border-color: var(--color-primary);
            color: var(--color-secondary);
        }

        .btn--primary {
            background: var(--gradient);
            color: #fff;
            box-shadow: var(--shadow-glow);
        }

        .btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 40px rgba(124,58,237,0.6);
        }

        /* ===== HERO SWIPER ===== */
        .hero {
            min-height: 100vh;
            padding-top: 70px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-swiper {
            width: 100%;
            height: 100%;
        }

        .hero__slide {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 70px);
            padding: 3rem 2rem;
        }

        .hero__content {
            max-width: 1100px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .hero__badge {
            display: inline-block;
            background: rgba(124,58,237,0.2);
            border: 1px solid var(--color-primary);
            color: var(--color-secondary);
            padding: 0.3rem 0.9rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .hero__title {
            font-size: clamp(2.2rem, 5vw, 3.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.2rem;
        }

        .hero__title span {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero__desc {
            color: var(--color-muted);
            font-size: 1.05rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .hero__btns {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .hero__image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero__avatar-grid {
            display: grid;
            grid-template-columns: repeat(3, 80px);
            gap: 1rem;
            justify-content: center;
        }

        .hero__avatar {
            width: 80px;
            height: 110px;
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .hero__avatar:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-glow);
        }

        .hero__avatar img {
            width: 100%;
            image-rendering: pixelated;
        }

        .swiper-pagination-bullet {
            background: var(--color-primary) !important;
            opacity: 0.5;
        }

        .swiper-pagination-bullet-active {
            opacity: 1 !important;
            box-shadow: 0 0 8px var(--color-primary);
        }

        /* ===== STATS BAR ===== */
        .stats {
            background: var(--color-card);
            border-top: 1px solid var(--color-border);
            border-bottom: 1px solid var(--color-border);
            padding: 1.5rem 2rem;
        }

        .stats__inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            text-align: center;
        }

        .stats__item h3 {
            font-size: 1.8rem;
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stats__item p {
            color: var(--color-muted);
            font-size: 0.85rem;
            margin-top: 0.2rem;
        }

        /* ===== SECCIONES ===== */
        .section {
            padding: 5rem 2rem;
        }

        .section__header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section__label {
            display: inline-block;
            background: rgba(124,58,237,0.15);
            color: var(--color-secondary);
            padding: 0.3rem 1rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }

        .section__title {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 800;
        }

        .section__title span {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section__subtitle {
            color: var(--color-muted);
            margin-top: 0.5rem;
            font-size: 1rem;
        }

        /* ===== ADMINS ===== */
        .admins__grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1.5rem;
        }

        .admin__card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.5rem 1rem;
            text-align: center;
            transition: all 0.3s;
        }

        .admin__card:hover {
            border-color: var(--color-primary);
            box-shadow: var(--shadow-glow);
            transform: translateY(-4px);
        }

        .admin__avatar img {
            width: 64px;
            image-rendering: pixelated;
            margin-bottom: 0.75rem;
        }

        .admin__name {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.4rem;
        }

        .admin__badge {
            display: inline-block;
            background: rgba(245,158,11,0.15);
            border: 1px solid var(--color-accent);
            color: var(--color-accent);
            padding: 0.2rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* ===== MEMBRESIAS ===== */
        .membresias__grid {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .membresia__card {
            background: var(--color-card);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .membresia__card:hover {
            border-color: var(--color-primary);
            box-shadow: var(--shadow-glow);
            transform: translateY(-4px);
        }

        .membresia__tag {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--color-primary);
            color: #fff;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .membresia__img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            background: linear-gradient(135deg, #1a1a2e, #2d2d4e);
        }

        .membresia__body {
            padding: 1.25rem;
        }

        .membresia__name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .membresia__duration {
            color: var(--color-muted);
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .membresia__desc {
            color: var(--color-muted);
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .membresia__price {
            font-size: 1.3rem;
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--color-card);
            border-top: 1px solid var(--color-border);
            padding: 3rem 2rem;
            text-align: center;
        }

        .footer__logo {
            font-size: 1.4rem;
            font-weight: 800;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.75rem;
        }

        .footer__copy {
            color: var(--color-muted);
            font-size: 0.85rem;
        }

        /* ===== SCROLL UP ===== */
        .scroll-up {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 44px;
            height: 44px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
            text-decoration: none;
            box-shadow: var(--shadow-glow);
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            z-index: 999;
        }

        .scroll-up.show {
            opacity: 1;
        }

        .scroll-up:hover {
            transform: translateY(-4px);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero__content {
                grid-template-columns: 1fr;
                text-align: center;
            }
            .hero__btns { justify-content: center; }
            .hero__image { display: none; }
            .stats__inner { grid-template-columns: repeat(2, 1fr); }
            .navbar__logo-text { display: none; }
        }
    </style>
</head>

<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="#" class="navbar__logo">
        <div class="navbar__logo-icon">&#127981;</div>
        <span class="navbar__logo-text">Reino Hogwarz</span>
    </a>
    <div class="navbar__actions">
        <a href="login.php" class="btn btn--ghost"><i class='bx bx-log-in'></i> Login</a>
        <a href="register.php" class="btn btn--primary"><i class='bx bx-user-plus'></i> Registrarse</a>
    </div>
</nav>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">

            <!-- Slide 1 -->
            <div class="swiper-slide hero__slide">
                <div class="hero__content">
                    <div class="hero__data">
                        <span class="hero__badge">&#11088; Agencia #1 de Habbo</span>
                        <h1 class="hero__title">Bienvenido al<br><span>Reino Hogwarz</span></h1>
                        <p class="hero__desc">La agencia m&aacute;s seria y organizada de Habbo. Oportunidades reales, paga garantizada y una comunidad que te respalda.</p>
                        <div class="hero__btns">
                            <a href="register.php" class="btn btn--primary">Unirse ahora</a>
                            <a href="#admins" class="btn btn--ghost">Conocer el staff</a>
                        </div>
                    </div>
                    <div class="hero__image">
                        <div class="hero__avatar-grid">
                            <?php
                            $demo_users = ['Admin','Staff1','Staff2','Staff3','Staff4','Staff5'];
                            foreach(array_slice($demo_users,0,6) as $u): ?>
                            <div class="hero__avatar">
                                <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($u) ?>&direction=3&head_direction=3&gesture=sml&action=std&size=m" alt="<?= htmlspecialchars($u) ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide hero__slide">
                <div class="hero__content">
                    <div class="hero__data">
                        <span class="hero__badge">&#127942; Compite y escala</span>
                        <h1 class="hero__title">Sube de<br><span>Rango R&aacute;pido</span></h1>
                        <p class="hero__desc">Completa misiones, participa en torneos y demuestra tu valor. Cada acci&oacute;n cuenta para tu ascenso dentro del reino.</p>
                        <div class="hero__btns">
                            <a href="register.php" class="btn btn--primary">Empezar gratis</a>
                        </div>
                    </div>
                    <div class="hero__image">
                        <div style="font-size:8rem;text-align:center;">&#127942;</div>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide hero__slide">
                <div class="hero__content">
                    <div class="hero__data">
                        <span class="hero__badge">&#128176; La mejor paga</span>
                        <h1 class="hero__title">Mejor Paga &<br><span>Administraci&oacute;n</span></h1>
                        <p class="hero__desc">Paga semanal garantizada, administraci&oacute;n transparente y un equipo dedicado a tu crecimiento. Aqu&iacute; tu esfuerzo tiene recompensa real.</p>
                        <div class="hero__btns">
                            <a href="register.php" class="btn btn--primary">Ver membres&iacute;as</a>
                            <a href="login.php" class="btn btn--ghost">Ya tengo cuenta</a>
                        </div>
                    </div>
                    <div class="hero__image">
                        <div style="font-size:8rem;text-align:center;">&#128176;</div>
                    </div>
                </div>
            </div>

        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- ===== STATS ===== -->
<div class="stats">
    <div class="stats__inner">
        <div class="stats__item"><h3>500+</h3><p>Miembros activos</p></div>
        <div class="stats__item"><h3>#1</h3><p>Agencia de Habbo</p></div>
        <div class="stats__item"><h3>100%</h3><p>Paga garantizada</p></div>
        <div class="stats__item"><h3>24/7</h3><p>Staff disponible</p></div>
    </div>
</div>

<!-- ===== ADMINS & MEMBRESIAS ===== -->
<?php
    require_once('private/procesos/db.php');

    // Admins
    $admins = [];
    $r = $conn->query("SELECT id, nombre, rango, cara, accion, bebida FROM modificar_administradores LIMIT 12");
    if ($r && $r->num_rows > 0) {
        while ($row = $r->fetch_assoc()) $admins[] = $row;
    }

    // Membresias
    $membresias = [];
    $r2 = $conn->query("SELECT id, nombre, precio, duracion, beneficios FROM modificar_membresias ORDER BY precio DESC");
    if ($r2 && $r2->num_rows > 0) {
        while ($row = $r2->fetch_assoc()) $membresias[] = $row;
    }
?>

<!-- Admins -->
<?php if (!empty($admins)): ?>
<section class="section" id="admins" style="background: var(--color-bg2);">
    <div class="section__header">
        <span class="section__label">Staff</span>
        <h2 class="section__title">Administradores del <span>Reino</span></h2>
        <p class="section__subtitle">El equipo que hace posible la mejor agencia</p>
    </div>
    <div class="admins__grid">
        <?php foreach($admins as $a): ?>
        <div class="admin__card">
            <div class="admin__avatar">
                <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['nombre']) ?>&direction=3&head_direction=3&gesture=<?= urlencode($a['cara']) ?>&action=<?= urlencode($a['accion']) ?>,<?= urlencode($a['bebida']) ?>&size=l" alt="<?= htmlspecialchars($a['nombre']) ?>">
            </div>
            <div class="admin__name"><?= htmlspecialchars($a['nombre']) ?></div>
            <span class="admin__badge"><?= htmlspecialchars($a['rango']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Membresias -->
<?php if (!empty($membresias)): ?>
<section class="section" id="membresias">
    <div class="section__header">
        <span class="section__label">Planes</span>
        <h2 class="section__title">Nuestras <span>Members&iacute;as</span></h2>
        <p class="section__subtitle">Elige el plan que mejor se adapte a ti</p>
    </div>
    <div class="membresias__grid">
        <?php
        $imgs = [
            1 => '/private/images/images/membresias/diamante.jpg',
            2 => '/private/images/images/membresias/guarda_paga_plus.jpg',
            3 => '/private/images/images/membresias/level_up.jpg',
            4 => '/private/images/images/membresias/premium.jpg',
            5 => '/private/images/images/membresias/regla_libre.jpg',
            6 => '/private/images/images/membresias/guarda_paga.jpg',
        ];
        foreach($membresias as $m):
            $img = $imgs[$m['id']] ?? '/private/assets/images/mantenimiento.png';
        ?>
        <div class="membresia__card">
            <span class="membresia__tag">New</span>
            <img src="<?= htmlspecialchars($img) ?>" class="membresia__img" alt="<?= htmlspecialchars($m['nombre']) ?>" onerror="this.style.background='linear-gradient(135deg,#1a1a2e,#2d2d4e)';this.style.display='block';this.src='';">
            <div class="membresia__body">
                <div class="membresia__name"><?= htmlspecialchars($m['nombre']) ?></div>
                <div class="membresia__duration"><i class='bx bx-time'></i> <?= htmlspecialchars($m['duracion']) ?></div>
                <div class="membresia__desc"><?= htmlspecialchars($m['beneficios']) ?></div>
                <div class="membresia__price"><?= htmlspecialchars($m['precio']) ?> Cr&eacute;ditos</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Rangos -->
<section class="section" style="background: var(--color-bg2);">
    <div class="section__header">
        <span class="section__label">Estructura</span>
        <h2 class="section__title">Rangos del <span>Reino</span></h2>
        <p class="section__subtitle">Escala posiciones y gana m&aacute;s beneficios</p>
    </div>
    <div style="max-width:1100px;margin:0 auto;">
        <?php include 'private/procesos/rangos.php'; ?>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="footer__logo">&#127981; Reino Hogwarz</div>
    <p class="footer__copy">&copy; 2026 Reino Hogwarz &mdash; Agencia Habbo. Todos los derechos reservados.</p>
</footer>

<!-- Scroll up -->
<a href="#" class="scroll-up" id="scrollUp"><i class='bx bx-up-arrow-alt'></i></a>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const heroSwiper = new Swiper('.hero-swiper', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.swiper-pagination', clickable: true },
        effect: 'fade',
        fadeEffect: { crossFade: true },
    });

    const scrollUpBtn = document.getElementById('scrollUp');
    window.addEventListener('scroll', () => {
        scrollUpBtn.classList.toggle('show', window.scrollY > 400);
    });
</script>

</body>
</html>
