<?php
/**
 * landing.php - Landing publica de verano
 * Acceso: radio.kusmedios.lat/landing
 * (Independiente del panel privado en /public/)
 */
session_start();
require_once(__DIR__ . '/private/procesos/db.php');

$admins = [];
$r = $conn->query("SELECT nombre, rango, cara, accion, bebida FROM modificar_administradores LIMIT 12");
if ($r) while($row = $r->fetch_assoc()) $admins[] = $row;

$membresias = [];
$r2 = $conn->query("SELECT id, nombre, precio, duracion, beneficios FROM modificar_membresias ORDER BY precio DESC");
if ($r2) while($row = $r2->fetch_assoc()) $membresias[] = $row;

$noticias = [];
$r3 = $conn->query("SELECT id, titulo, contenido, autor, imagen, fecha FROM publicaciones ORDER BY fecha DESC LIMIT 6");
if ($r3) while($row = $r3->fetch_assoc()) $noticias[] = $row;

$notificaciones = [];
$notif_count = 0;
if (isset($_SESSION['usuario_id'])) {
    $uid = intval($_SESSION['usuario_id']);
    $rn = $conn->query("SELECT id, mensaje, leida, fecha FROM notificaciones WHERE id_usuario = $uid ORDER BY fecha DESC LIMIT 5");
    if ($rn) while($row = $rn->fetch_assoc()) {
        $notificaciones[] = $row;
        if (!$row['leida']) $notif_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/private/eventos/halloween/img/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="/private/eventos/halloween/css/swiper-bundle.min.css">
    <link rel="stylesheet" href="/private/eventos/verano/css/styles.css">
    <title>Reino Hogwarz &#9728;&#65039;</title>
</head>
<body>

<!-- ======= HEADER ======= -->
<header class="header" id="header">
    <nav class="nav container">
        <a href="/landing" class="nav__logo">
            <img src="/private/eventos/halloween/img/reino.png" style="width:180px;" alt="logo">
        </a>
        <div class="nav__menu" id="nav-menu">
            <ul class="nav__list">
                <li><a href="#home" class="nav__link active-link">Inicio</a></li>
                <li><a href="#radio" class="nav__link">Radio</a></li>
                <li><a href="#noticias" class="nav__link">Noticias</a></li>
                <li><a href="#membresias" class="nav__link">Membres&iacute;as</a></li>
                <li><a href="#staff" class="nav__link">Staff</a></li>
            </ul>
            <div class="nav__close" id="nav-close"><i class='bx bx-x'></i></div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;">
            <div style="position:relative;">
                <div class="nav__notif" id="notifBtn" onclick="toggleNotif()">
                    <i class='bx bx-bell'></i>
                    <?php if($notif_count > 0): ?>
                    <span class="nav__notif-count"><?= $notif_count ?></span>
                    <?php endif; ?>
                </div>
                <div class="notif__dropdown" id="notifDropdown">
                    <div class="notif__title">&#128276; Notificaciones</div>
                    <?php if(empty($notificaciones)): ?>
                    <div class="notif__item">
                        <span class="notif__icon">&#9728;&#65039;</span>
                        <div class="notif__text">&iexcl;Bienvenido al Reino Hogwarz!
                            <div class="notif__time">Ahora</div>
                        </div>
                    </div>
                    <?php else: foreach($notificaciones as $n): ?>
                    <div class="notif__item">
                        <span class="notif__icon"><?= $n['leida'] ? '&#128233;' : '&#128276;' ?></span>
                        <div class="notif__text"><?= htmlspecialchars($n['mensaje']) ?>
                            <div class="notif__time"><?= date('d/m H:i', strtotime($n['fecha'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="/index.php" class="button button--ghost" style="padding:.5rem 1rem;font-size:.85rem;">Panel</a>
            <?php else: ?>
                <a href="/login.php" class="button button--ghost" style="padding:.5rem 1rem;font-size:.85rem;">Login</a>
                <a href="/register.php" class="button" style="padding:.5rem 1rem;font-size:.85rem;">Registrarse</a>
            <?php endif; ?>
            <div class="nav__toggle" id="nav-toggle"><i class='bx bx-grid-alt'></i></div>
        </div>
    </nav>
</header>

<main class="main">

<!-- ======= HOME ======= -->
<section class="home container" id="home">
    <div class="swiper home-swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home1-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">&#9728;&#65039; #1 Bienvenido al Reino Hogwarz</h3>
                        <h1 class="home__title">&iexcl;VERANO<br>M&Aacute;GICO<br>2026!</h1>
                        <p class="home__description">La agencia m&aacute;s &eacute;pica de Habbo est&aacute; de vuelta con todo para el verano.</p>
                        <div class="home__buttons">
                            <a href="/register.php" class="button button--flex"><i class='bx bx-star'></i> Unirme</a>
                            <a href="#radio" class="button button--ghost button--flex"><i class='bx bx-radio'></i> Radio</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home2-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">&#127942; #2 Compite y escala</h3>
                        <h1 class="home__title">SUBE DE<br>RANGO<br>R&Aacute;PIDO</h1>
                        <p class="home__description">Completa misiones, gana torneos y demuestra qui&eacute;n manda.</p>
                        <div class="home__buttons">
                            <a href="/register.php" class="button button--flex"><i class='bx bx-trophy'></i> Empezar</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-slide">
                <div class="home__content grid">
                    <div class="home__group">
                        <img src="/private/eventos/halloween/img/home3-img.png" alt="" class="home__img">
                    </div>
                    <div class="home__data">
                        <h3 class="home__subtitle">&#128176; #3 La mejor paga</h3>
                        <h1 class="home__title">MEJOR<br>PAGA &amp;<br>ADMIN</h1>
                        <p class="home__description">Paga semanal garantizada y administraci&oacute;n transparente.</p>
                        <div class="home__buttons">
                            <a href="#membresias" class="button button--flex"><i class='bx bx-diamond'></i> Membres&iacute;as</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<!-- ======= RADIO ======= -->
<section class="section" id="radio">
    <h2 class="section__title">&#128251; Radio Habbo</h2>
    <div class="container">
        <div class="radio__container">
            <div class="radio__icon-wrap" id="radioDisc">&#127925;</div>
            <div class="radio__info">
                <div class="radio__station">Radio Reino Hogwarz</div>
                <div class="radio__now">En vivo ahora:</div>
                <div class="radio__track" id="radioTrack">Cargando estaci&oacute;n...</div>
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
        <audio id="radioAudio" preload="none">
            <source src="https://stream.kusmedios.lat/radio" type="audio/mpeg">
        </audio>
    </div>
</section>

<!-- ======= NOTICIAS ======= -->
<?php if(!empty($noticias)): ?>
<section class="section" id="noticias">
    <h2 class="section__title">&#128240; Noticias de la Agencia</h2>
    <div class="container">
        <div class="news__grid">
        <?php foreach($noticias as $noticia):
            $tags = ['&#128293; Urgente','&#11088; Destacado','&#127919; Evento','&#128172; Comunidad','&#128226; Anuncio'];
            $tag = $tags[array_rand($tags)];
            $excerpt = mb_substr(strip_tags($noticia['contenido']), 0, 100) . '...';
            $fecha = date('d M Y', strtotime($noticia['fecha']));
        ?>
        <div class="news__card">
            <?php if(!empty($noticia['imagen'])): ?>
            <img src="<?= htmlspecialchars($noticia['imagen']) ?>" class="news__img" alt="" onerror="this.style.display='none'">
            <?php else: ?>
            <div class="news__img" style="display:flex;align-items:center;justify-content:center;font-size:3rem;">&#128240;</div>
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
                <div class="reactions" data-id="<?= $noticia['id'] ?>">
                    <button class="reaction__btn" onclick="reaccionar(this,'&#10084;&#65039;')">&#10084;&#65039; <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'&#128293;')">&#128293; <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'&#128514;')">&#128514; <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'&#128558;')">&#128558; <span>0</span></button>
                    <button class="reaction__btn" onclick="reaccionar(this,'&#128079;')">&#128079; <span>0</span></button>
                </div>
                <div style="margin-top:.75rem;">
                    <button class="reaction__btn" onclick="toggleComments('c<?= $noticia['id'] ?>')">
                        <i class='bx bx-comment'></i> Comentar
                    </button>
                </div>
                <div id="c<?= $noticia['id'] ?>" style="display:none;margin-top:1rem;">
                    <div class="comments__section">
                        <div class="comment__form">
                            <div class="comment__avatar">&#128526;</div>
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
    <h2 class="section__title">&#128142; Membres&iacute;as de Verano</h2>
    <div class="container">
        <div class="trick__container grid">
        <?php
        $imgs = [1=>'/private/images/images/membresias/diamante.jpg',2=>'/private/images/images/membresias/guarda_paga_plus.jpg',3=>'/private/images/images/membresias/level_up.jpg',4=>'/private/images/images/membresias/premium.jpg',5=>'/private/images/images/membresias/regla_libre.jpg',6=>'/private/images/images/membresias/guarda_paga.jpg'];
        foreach($membresias as $m): $img = $imgs[$m['id']] ?? ''; ?>
        <div class="trick__content">
            <?php if($img): ?>
            <img src="<?= htmlspecialchars($img) ?>" class="trick__img" alt="" onerror="this.style.display='none'">
            <?php else: ?>
            <div style="font-size:3rem;margin-bottom:.75rem;">&#127775;</div>
            <?php endif; ?>
            <span class="trick__subtitle"><?= htmlspecialchars($m['duracion']) ?></span>
            <span class="trick__title"><?= htmlspecialchars($m['nombre']) ?></span>
            <span class="trick__price"><?= htmlspecialchars($m['precio']) ?> Cr&eacute;ditos</span>
            <?php if(!empty($m['beneficios'])): ?>
            <p style="font-size:.75rem;color:var(--text-color-light);padding:.5rem .75rem 0;line-height:1.4;"><?= htmlspecialchars($m['beneficios']) ?></p>
            <?php endif; ?>
            <a href="/login.php" class="trick__button"><i class='bx bx-cart trick__icon'></i></a>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ======= STAFF ======= -->
<?php if(!empty($admins)): ?>
<section class="section" id="staff">
    <h2 class="section__title">&#128101; Nuestro Staff</h2>
    <div class="container">
        <div class="category__container grid">
        <?php foreach($admins as $a): ?>
        <div class="category__data">
            <img src="https://www.habbo.es/habbo-imaging/avatarimage?user=<?= urlencode($a['nombre']) ?>&direction=3&head_direction=3&gesture=<?= urlencode($a['cara']) ?>&action=<?= urlencode($a['accion']) ?>&size=l"
                alt="<?= htmlspecialchars($a['nombre']) ?>" class="category__img">
            <h3 class="category__title"><?= htmlspecialchars($a['nombre']) ?></h3>
            <span class="news__tag"><?= htmlspecialchars($a['rango']) ?></span>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

</main>

<footer class="footer section">
    <div class="container footer__container grid">
        <div>
            <a href="/landing" class="footer__logo">&#9728;&#65039; Reino Hogwarz</a>
            <p class="footer__description">La mejor agencia de Habbo. Paga garantizada y comunidad activa.</p>
            <div class="footer__social">
                <a href="https://whatsapp.com/channel/0029Vajlw9FDTkK9NgHlz81t" class="footer__social-link"><i class='bx bxl-whatsapp'></i></a>
                <a href="https://www.instagram.com/twitchagency_hbb" class="footer__social-link"><i class='bx bxl-instagram'></i></a>
                <a href="#radio" class="footer__social-link"><i class='bx bx-radio'></i></a>
            </div>
        </div>
        <div>
            <h3 class="footer__title">Navegaci&oacute;n</h3>
            <ul class="footer__links">
                <li><a href="#home" class="footer__link">Inicio</a></li>
                <li><a href="#radio" class="footer__link">Radio</a></li>
                <li><a href="#noticias" class="footer__link">Noticias</a></li>
                <li><a href="#membresias" class="footer__link">Membres&iacute;as</a></li>
            </ul>
        </div>
        <div>
            <h3 class="footer__title">Cuenta</h3>
            <ul class="footer__links">
                <li><a href="/login.php" class="footer__link">Iniciar sesi&oacute;n</a></li>
                <li><a href="/register.php" class="footer__link">Registrarse</a></li>
            </ul>
        </div>
    </div>
    <span class="footer__copy">&copy; 2026 Reino Hogwarz &mdash; Agencia Habbo.</span>
</footer>

<a href="#" class="scrollup" id="scroll-up">
    <i class='bx bx-up-arrow-alt scrollup__icon'></i>
</a>

<script src="/private/eventos/halloween/js/swiper-bundle.min.js"></script>
<script src="/private/eventos/halloween/js/main.js"></script>
<script>
const homeSwiper = new Swiper('.home-swiper', {
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true }
});
function toggleNotif() {
    document.getElementById('notifDropdown').classList.toggle('open');
}
document.addEventListener('click', function(e){
    if (!e.target.closest('#notifBtn') && !e.target.closest('#notifDropdown'))
        document.getElementById('notifDropdown').classList.remove('open');
});
let radioPlaying = false;
const audio = document.getElementById('radioAudio');
const disc  = document.getElementById('radioDisc');
const icon  = document.getElementById('radioIcon');
const track = document.getElementById('radioTrack');
function toggleRadio() {
    if (radioPlaying) {
        audio.pause(); disc.classList.add('paused');
        icon.className = 'bx bx-play'; track.textContent = 'Pausado'; radioPlaying = false;
    } else {
        audio.play().catch(()=>{ track.textContent = 'Stream no disponible'; });
        disc.classList.remove('paused');
        icon.className = 'bx bx-pause'; track.textContent = '&#127925; En vivo — Radio Reino Hogwarz'; radioPlaying = true;
    }
}
function setVolume(v){ audio.volume = v; }
function reaccionar(btn) {
    const span = btn.querySelector('span');
    const active = btn.classList.toggle('active');
    span.textContent = parseInt(span.textContent) + (active ? 1 : -1);
}
function toggleComments(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
function addComment(btn, id) {
    const ta = btn.closest('.comment__input-wrap').querySelector('textarea');
    if (!ta.value.trim()) return;
    const list = document.getElementById('cl' + id);
    const div = document.createElement('div');
    div.className = 'comment__item';
    div.innerHTML = `<div class="comment__avatar">&#128522;</div><div class="comment__body"><div class="comment__header"><span class="comment__name">T&uacute;</span><span class="comment__time">Ahora</span></div><p class="comment__text">${ta.value.replace(/</g,'&lt;')}</p></div>`;
    list.prepend(div); ta.value = '';
}
window.addEventListener('scroll', () => {
    document.getElementById('scroll-up').classList.toggle('show-scroll', scrollY >= 400);
    document.getElementById('header').classList.toggle('scroll-header', scrollY >= 50);
});
</script>
</body>
</html>
