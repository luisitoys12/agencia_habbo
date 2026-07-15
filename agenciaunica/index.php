<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/private/eventos/halloween/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/eventos/halloween/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/private/eventos/verano/css/styles.css">
    <title>Reino Hogwarz ☀️</title>
</head>
<body>

<?php
require_once('private/procesos/db.php');

// Admins
$admins = [];
$r = $conn->query("SELECT nombre, rango, cara, accion, bebida FROM modificar_administradores LIMIT 12");
if ($r && $r->num_rows > 0) while($row = $r->fetch_assoc()) $admins[] = $row;

// Membresias
$membresias = [];
$r2 = $conn->query("SELECT id, nombre, precio, duracion, beneficios FROM modificar_membresias ORDER BY precio DESC");
if ($r2 && $r2->num_rows > 0) while($row = $r2->fetch_assoc()) $membresias[] = $row;

// Noticias
$noticias = [];
$r3 = $conn->query("SELECT id, titulo, contenido, autor, imagen, fecha FROM publicaciones ORDER BY fecha DESC LIMIT 6");
if ($r3 && $r3->num_rows > 0) while($row = $r3->fetch_assoc()) $noticias[] = $row;

// Notificaciones para usuario logueado
$notificaciones = [];
$notif_count = 0;
if (isset($_SESSION['usuario_id'])) {
    $uid = intval($_SESSION['usuario_id']);
    $rn = $conn->query("SELECT id, mensaje, leida, fecha FROM notificaciones WHERE id_usuario = $uid ORDER BY fecha DESC LIMIT 5");
    if ($rn && $rn->num_rows > 0) {
        while($row = $rn->fetch_assoc()) {
            $notificaciones[] = $row;
            if (!$row['leida']) $notif_count++;
        }
    }
}
?>

<!-- ======= HEADER ======= -->
<header class="header" id="header">
    <nav class="nav container">
        <a href="#" class="nav__logo">
            <img src="/private/eventos/halloween/img/reino.png" style="width:180px;" class="nav__logo-img">
        </a>

        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li><a href="#home" class="nav__link active-link">Inicio</a></li>
                <li><a href="#radio" class="nav__link">Radio</a></li>
                <li><a href="#noticias" class="nav__link">Noticias</a></li>
                <li><a href="#membresias" class="nav__link">Membresías</a></li>
                <li><a href="#staff" class="nav__link">Staff</a></li>
            </ul>
            <img src="/private/eventos/halloween/img/nav-img.png" alt="" class="nav__img">
            <div class="nav__close" id="nav-close"><i class='bx bx-x'></i></div>
        </div>

        <div style="display:flex;align-items:center;gap:.75rem;">
            <!-- Notificaciones -->
            <div style="position:relative;">
                <div class="nav__notif" id="notifBtn" onclick="toggleNotif()">
                    <i class='bx bx-bell'></i>
                    <?php if($notif_count > 0): ?>
                    <span class="nav__notif-count"><?= $notif_count ?></span>
                    <?php endif; ?>
                </div>
                <div class="notif__dropdown" id="notifDropdown">
                    <div class="notif__title">🔔 Notificaciones</div>
                    <?php if(empty($notificaciones)): ?>
                    <div class="notif__item">
                        <span class="notif__icon">☀️</span>
                        <div class="notif__text">¡Bienvenido al Reino Hogwarz! No hay notificaciones nuevas.
                            <div class="notif__time">Ahora</div>
                        </div>
                    </div>
                    <?php else: foreach($notificaciones as $n): ?>
                    <div class="notif__item">
                        <span class="notif__icon"><?= $n['leida'] ? '📩' : '🔔' ?></span>
                        <div class="notif__text"><?= htmlspecialchars($n['mensaje']) ?>
                            <div class="notif__time"><?= date('d/m H:i', strtotime($n['fecha'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="private/panel/inicio.php" class="button button--ghost" style="padding:.5rem 1rem;font-size:.85rem;">Panel</a>
            <?php else: ?>
                <a href="login.php" class="button button--ghost" style="padding:.5rem 1rem;font-size:.85rem;">Login</a>
                <a href="register.php" class="button" style="padding:.5rem 1rem;font-size:.85rem;">Registrarse</a>
            <?php endif; ?>
            <div class="nav__toggle" id="nav-toggle"><i class='bx bx-grid-alt'></i></div>
        </div>
    </nav>
</header>

<main class="main">

<!-- ======= HOME SWIPER ======= -->
<section class="home container" id="home">
    <div class="swiper home-swiper">
        <div class="swiper-wrapper">
            <section class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home1-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">☀️ #1 Bienvenido al Reino Hogwarz</h3>
                        <h1 class="home__title">¡VERANO <br>MÁGICO <br>2026!</h1>
                        <p class="home__description">La agencia más épica de Habbo está de vuelta con todo para el verano. Misiones, torneos, paga garantizada y la mejor comunidad.</p>
                        <div class="home__buttons">
                            <a href="register.php" class="button button--flex"><i class='bx bx-star'></i> Unirme</a>
                            <a href="#radio" class="button button--ghost button--flex"><i class='bx bx-radio'></i> Radio</a>
                        </div>
                    </div>
                </div>
            </section>
            <section class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home2-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">🏆 #2 Compite y escala</h3>
                        <h1 class="home__title">SUBE DE <br>RANGO <br>RÁPIDO</h1>
                        <p class="home__description">Completa misiones de verano, gana torneos de playa y demuestra quién manda en el reino. Cada punto cuenta.</p>
                        <div class="home__buttons">
                            <a href="register.php" class="button button--flex"><i class='bx bx-trophy'></i> Empezar</a>
                        </div>
                    </div>
                </div>
            </section>
            <section class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home3-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">💰 #3 La mejor paga</h3>
                        <h1 class="home__title">MEJOR <br>PAGA & <br>ADMIN</h1>
                        <p class="home__description">Paga semanal garantizada, administración transparente y un equipo que trabaja para ti. Aquí tu esfuerzo tiene recompensa real.</p>
                        <div class="home__buttons">
                            <a href="#membresias" class="button button--flex"><i class='bx bx-diamond'></i> Membresías</a>
                            <a href="login.php" class="button button--ghost">Mi cuenta</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- ======= RADIO HABBO ======= -->
<section class="section" id="radio">
    <h2 class="section__title">📻 Radio Habbo</h2>
    <div class="container">
        <div class="radio__container">
            <div class="radio__icon-wrap" id="radioDisc">🎵</div>
            <div class="radio__info">
                <div class="radio__station">Radio Reino Hogwarz</div>
                <div class="radio__now">En vivo ahora:</div>
                <div class="radio__track" id="radioTrack">Cargando estación...</div>
            </div>
            <div class="radio__controls">
                <button class="radio__play" id="radioPlayBtn" onclick="toggleRadio()">
                    <i class='bx bx-play' id="radioIcon"></i>
                </button>
                <div class="radio__vol">
                    <i class='bx bx-volume-full' style="font-size:.9rem;"></i>
                    <input type="range" id="radioVol" min="0" max="1" step="0.1" value="0.7" oninput="setVolume(this.value)">
                </div>
            </div>
        </div>

        <!-- Audio oculto -->
        <audio id="radioAudio" preload="none">
            <!-- Reemplaza este src con la URL de tu stream de radio -->
            <source src="https://stream.kusmedios.lat/radio" type="audio/mpeg">
        </audio>
    </div>
</section>

<!-- ======= NOTICIAS ======= -->
<?php if(!empty($noticias)): ?>
<section class="section" id="noticias">
    <h2 class="section__title">📰 Noticias de la Agencia</h2>
    <div class="container">
        <div class="news__grid">
        <?php foreach($noticias as $noticia):
            $tagLabels = ['🔥 Urgente','⭐ Destacado','🎯 Evento','💬 Comunidad','📢 Anuncio'];
            $tag = $tagLabels[array_rand($tagLabels)];
            $excerpt = mb_substr(strip_tags($noticia['contenido']), 0, 100) . '...';
            $fecha = date('d M Y', strtotime($noticia['fecha']));
        ?>
        <div class="news__card">
            <?php if(!empty($noticia['imagen'])): ?>
            <img src="<?= htmlspecialchars($noticia['imagen']) ?>" class="news__img" alt="" onerror="this.style.display='none'">
            <?php else: ?>
            <div class="news__img" style="display:flex;align-items:center;justify-content:center;font-size:3rem;">📰</div>
            <?php endif; ?>
            <div class="news__body">
                <span class="news__tag"><?= $tag ?></span>
                <div class="news__title"><?= htmlspecialchars($noticia['titulo']) ?></div>
                <p class="news__excerpt"><?= htmlspecialchars($excerpt) ?></p>
                <div class="news__meta">
                    <div class="news__author">
                        <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($noticia['autor']) ?>&size=s" alt="">
                        <?= htmlspecialchars($noticia['autor']) ?>
                    </div>
                    <span><?= $fecha ?></span>
                </div>
                <!-- Reacciones -->
                <div class="reactions" data-id="<?= $noticia['id'] ?>">
                    <button class="reaction__btn" onclick="reaccionar(this,'❤️')" title="Me encanta">❤️ <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'🔥')" title="Fuego">🔥 <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'😂')" title="Divertido">😂 <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'😮')" title="Sorprendido">😮 <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'👏')" title="Aplausos">👏 <span>0</span></button>
                </div>
                <!-- Comentarios toggle -->
                <div style="margin-top:.75rem;">
                    <button class="reaction__btn" onclick="toggleComments('c<?= $noticia['id'] ?>')" style="font-size:.78rem;">
                        <i class='bx bx-comment'></i> <span>Comentar</span>
                    </button>
                </div>
                <div id="c<?= $noticia['id'] ?>" style="display:none;margin-top:1rem;">
                    <div class="comments__section">
                        <div class="comment__form">
                            <div class="comment__avatar">😎</div>
                            <div class="comment__input-wrap">
                                <textarea class="comment__input" placeholder="Escribe un comentario..." rows="2"></textarea>
                                <button class="comment__submit" onclick="addComment(this, <?= $noticia['id'] ?>)">Enviar</button>
                            </div>
                        </div>
                        <div class="comment__list" id="cl<?= $noticia['id'] ?>"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======= MEMBRESIAS ======= -->
<?php if(!empty($membresias)): ?>
<section class="section" id="membresias">
    <h2 class="section__title">💎 Membresías de Verano</h2>
    <div class="container">
        <div class="trick__container grid">
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
            $img = $imgs[$m['id']] ?? '';
        ?>
        <div class="trick__content">
            <?php if($img): ?>
            <img src="<?= htmlspecialchars($img) ?>" class="trick__img" alt="" onerror="this.style.display='none'">
            <?php else: ?>
            <div style="font-size:3rem;margin-bottom:.75rem;">🌟</div>
            <?php endif; ?>
            <span class="trick__subtitle"><?= htmlspecialchars($m['duracion']) ?></span>
            <span class="trick__title"><?= htmlspecialchars($m['nombre']) ?></span>
            <span class="trick__price"><?= htmlspecialchars($m['precio']) ?> Créditos</span>
            <?php if(!empty($m['beneficios'])): ?>
            <p style="font-size:.75rem;color:var(--text-color-light);padding:.5rem .75rem 0;line-height:1.4;"><?= htmlspecialchars($m['beneficios']) ?></p>
            <?php endif; ?>
            <a href="login.php" class="trick__button"><i class='bx bx-cart trick__icon'></i></a>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======= STAFF ======= -->
<?php include "private/procesos/administradores.php"; ?>

<!-- ======= RANGOS ======= -->
<?php include "private/procesos/rangos.php"; ?>

</main>

<!-- ======= FOOTER ======= -->
<footer class="footer section">
    <div class="container footer__container grid">
        <div>
            <a href="#" class="footer__logo">☀️ Reino Hogwarz</a>
            <p class="footer__description">La mejor agencia de Habbo. Paga garantizada, staff dedicado y la comunidad más activa.</p>
            <div class="footer__social">
                <a href="#" class="footer__social-link"><i class='bx bxl-discord'></i></a>
                <a href="#" class="footer__social-link"><i class='bx bxl-twitter'></i></a>
                <a href="#" class="footer__social-link"><i class='bx bx-radio'></i></a>
            </div>
        </div>
        <div>
            <h3 class="footer__title">Navegación</h3>
            <ul class="footer__links">
                <li><a href="#home" class="footer__link">Inicio</a></li>
                <li><a href="#radio" class="footer__link">Radio</a></li>
                <li><a href="#noticias" class="footer__link">Noticias</a></li>
                <li><a href="#membresias" class="footer__link">Membresías</a></li>
            </ul>
        </div>
        <div>
            <h3 class="footer__title">Cuenta</h3>
            <ul class="footer__links">
                <li><a href="login.php" class="footer__link">Iniciar sesión</a></li>
                <li><a href="register.php" class="footer__link">Registrarse</a></li>
            </ul>
        </div>
        <div>
            <h3 class="footer__title">Contacto</h3>
            <p class="footer__description" style="font-size:.8rem;">¿Dudas? Encuentra a un admin en Habbo o escríbenos en Discord.</p>
        </div>
    </div>
    <span class="footer__copy">© 2026 Reino Hogwarz — Agencia Habbo. Todos los derechos reservados.</span>
    <img src="/private/eventos/halloween/img/footer1-img.png" alt="" class="footer__img-one">
    <img src="/private/eventos/halloween/img/footer2-img.png" alt="" class="footer__img-two">
</footer>

<a href="#" class="scrollup" id="scroll-up">
    <i class='bx bx-up-arrow-alt scrollup__icon'></i>
</a>

<script src="/private/eventos/halloween/js/scrollreveal.min.js"></script>
<script src="/private/eventos/halloween/js/swiper-bundle.min.js"></script>
<script src="/private/eventos/halloween/js/main.js"></script>
<script>
// ===== SWIPER =====
const homeSwiper = new Swiper('.home-swiper', {
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
});

// ===== NOTIFICACIONES =====
function toggleNotif() {
    const d = document.getElementById('notifDropdown');
    d.classList.toggle('open');
}
document.addEventListener('click', function(e){
    if (!e.target.closest('#notifBtn') && !e.target.closest('#notifDropdown')) {
        document.getElementById('notifDropdown').classList.remove('open');
    }
});

// ===== RADIO =====
let radioPlaying = false;
const audio = document.getElementById('radioAudio');
const disc = document.getElementById('radioDisc');
const icon = document.getElementById('radioIcon');
const track = document.getElementById('radioTrack');

function toggleRadio() {
    if (radioPlaying) {
        audio.pause();
        disc.classList.add('paused');
        icon.className = 'bx bx-play';
        track.textContent = 'Pausado';
        radioPlaying = false;
    } else {
        audio.play().catch(() => { track.textContent = 'Stream no disponible'; });
        disc.classList.remove('paused');
        icon.className = 'bx bx-pause';
        track.textContent = '🎵 En vivo — Radio Reino Hogwarz';
        radioPlaying = true;
    }
}
function setVolume(v) { audio.volume = v; }

// ===== REACCIONES =====
function reaccionar(btn, emoji) {
    const active = btn.classList.contains('active');
    const span = btn.querySelector('span');
    let count = parseInt(span.textContent) || 0;
    if (active) {
        btn.classList.remove('active');
        span.textContent = Math.max(0, count - 1);
    } else {
        btn.classList.add('active');
        span.textContent = count + 1;
    }
}

// ===== COMENTARIOS =====
function toggleComments(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

function addComment(btn, noticiaId) {
    const wrap = btn.closest('.comment__input-wrap');
    const textarea = wrap.querySelector('textarea');
    const text = textarea.value.trim();
    if (!text) return;

    const list = document.getElementById('cl' + noticiaId);
    const item = document.createElement('div');
    item.className = 'comment__item';
    item.innerHTML = `
        <div class="comment__avatar">😊</div>
        <div class="comment__body">
            <div class="comment__header">
                <span class="comment__name">Tú</span>
                <span class="comment__time">Ahora</span>
            </div>
            <p class="comment__text">${text.replace(/</g,'&lt;')}</p>
            <div class="comment__actions">
                <span class="comment__like" onclick="this.style.color='hsl(340,90%,55%)'">❤️ Me gusta</span>
                <span class="comment__reply">↩️ Responder</span>
            </div>
        </div>`;
    list.prepend(item);
    textarea.value = '';
}

// ===== SCROLL UP =====
window.addEventListener('scroll', () => {
    document.getElementById('scroll-up').classList.toggle('show-scroll', window.scrollY >= 400);
    document.getElementById('header').classList.toggle('scroll-header', window.scrollY >= 50);
});
</script>

</body>
</html>
