<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== FAVICON ===============-->
    <link rel="shortcut icon" href="/private/eventos/halloween/img/favicon.png" type="image/x-icon">

    <!--=============== BOXICONS ===============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <!--=============== SWIPER CSS ===============-->
    <link rel="stylesheet" href="/private/eventos/halloween/css/swiper-bundle.min.css">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="/private/eventos/halloween/css/styles.css">

    <title>Reino Hogwarz</title>
</head>

<body>
    <!--==================== HEADER ====================-->
    <header class="header" id="header">
        <nav class="nav container">
            <a href="#" class="nav__logo">
                <img src="/private/eventos/halloween/img/reino.png" style="width: 200px;" class="nav__logo-img">
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <a href="login.php" class="button button--ghost">login</a>
                    <a href="register.php" class="button button--ghost">registrarse</a>
                </ul>

                <div class="nav__close" id="nav-close">
                    <i class='bx bx-x'></i>
                </div>

                <img src="/private/eventos/halloween/img/nav-img.png" alt="" class="nav__img">
            </div>

            <div class="nav__toggle" id="nav-toggle">
                <i class='bx bx-grid-alt'></i>
            </div>
        </nav>
    </header>

    <main class="main">
        <!--==================== HOME ====================-->
        <section class="home container" id="home">
            <div class="swiper home-swiper">
                <div class="swiper-wrapper">
                    <!-- HOME SLIDER 1 -->
                    <section class="swiper-slide">
                        <div class="home__content grid">
                            <div class="home__group">
                                <img src="/private/eventos/halloween/img/home1-img.png" alt="" class="home__img">
                            </div>
                            <div class="home__data">
                                <h3 class="home__subtitle">#1 Bienvenido al Reino Hogwarts</h3>
                                <h1 class="home__title">¡FELIZ <br> HALLOWEEN <br> MÁGICO!</h1>
                                <p class="home__description">
                                    Hola, soy un aprendiz de magia explorando los misterios de Hogwarts.
                                    Prepárate para hechizos, criaturas fantásticas y dulces encantados que
                                    te sorprenderán en esta noche especial.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- HOME SLIDER 2 -->
                    <section class="swiper-slide">
                        <div class="home__content grid">
                            <div class="home__group">
                                <img src="/private/eventos/halloween/img/home2-img.png" alt="" class="home__img">
                            </div>
                            <div class="home__data">
                                <h3 class="home__subtitle">#2 Únete a la Magia</h3>
                                <h1 class="home__title">¡ENTRA AL <br> REINO <br> HOGWARTS!</h1>
                                <p class="home__description">
                                    Descubre tu casa mágica, completa misiones épicas y compite en el Torneo de los
                                    Tres Magos. ¡Haz amigos, crea alianzas y convíiértete en un verdadero mago!
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- HOME SLIDER 3 -->
                    <section class="swiper-slide">
                        <div class="home__content grid">
                            <div class="home__group">
                                <img src="/private/eventos/halloween/img/home3-img.png" alt="" class="home__img">
                            </div>
                            <div class="home__data">
                                <h3 class="home__subtitle">#3 La mejor paga de todo habbo</h3>
                                <h1 class="home__title">LA MEJOR <br> PAGA Y <br> ADMINISTRACIÓN</h1>
                                <p class="home__description">
                                    Descubre un mundo donde la excelencia es nuestra norma. Aquí no solo recibirás la
                                    mejor paga, sino que disfrutarás del servicio más eficiente y una administración
                                    dedicada a tu crecimiento.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

        <!--==================== Membresias y administradores ====================-->
        <?php include "private/procesos/administradores.php"; ?>

        <br>

        <!--==================== RANGOS ====================-->
        <?php include "private/procesos/rangos.php"; ?>
    </main>

    <!--==================== FOOTER ====================-->
    <footer class="footer section">
        <span class="footer__copy">&#169; Ing. Medina. All rigths reserved</span>
        <img src="/private/eventos/halloween/img/footer1-img.png" alt="" class="footer__img-one">
        <img src="/private/eventos/halloween/img/footer2-img.png" alt="" class="footer__img-two">
    </footer>

    <!--=============== SCROLL UP ===============-->
    <a href="#" class="scrollup" id="scroll-up">
        <img src="/private/eventos/halloween/img/category1-img.png" alt="Fantasma" style="width: 24px; height: 24px; margin-right: 5px;">
        <i class='bx bx-up-arrow-alt scrollup__icon'></i>
    </a>

    <!--=============== SCROLL REVEAL ===============-->
    <script src="/private/eventos/halloween/js/scrollreveal.min.js"></script>

    <!--=============== SWIPER JS ===============-->
    <script src="/private/eventos/halloween/js/swiper-bundle.min.js"></script>

    <!--=============== MAIN JS ===============-->
    <script src="/private/eventos/halloween/js/main.js"></script>
</body>

</html>
