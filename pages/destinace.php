<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="cs" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Destinace | JOURNEYO</title>
        <link rel="icon" href="../img/favicon.ico">
        <link rel="stylesheet" href="../styles/destinace.css">
        <link rel="stylesheet" href="../styles/themes.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <header>
            <a href="uvod.php"><img src="../img/nvlogo.png" alt="JY" class="logo"></a>
            <div class="menu-btn"></div>
            <div class="nav">
                <div class="nav-items">
                    <a href="uvod.php">Úvod</a>
                    <a href="destinace.php" class="active">Destinace</a>
                    <?php if ($logged_in): ?>
                        <a href="oblibene.php">Oblíbené</a>
                        <a href="cesty.php">Cesty</a>
                        <a href="dashboard.php">Dashboard</a>
                    <?php endif; ?>
                    <?php if ($is_admin): ?>
                        <a href="../admin/admin.php">Admin</a>
                    <?php endif; ?>
                    <?php if ($logged_in): ?>
                        <div class="user-info">
                            <span><?php echo htmlspecialchars($username); ?></span>
                            <a href="../backend/logout.php">Odhlásit se</a>
                        </div>
                    <?php else: ?>
                        <a href="reglog.php">Přihlášení</a>
                    <?php endif; ?>
                    <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Hero sekce se searchem -->
        <div class="dest-hero">
            <div class="dest-hero-content">
                <h1>Objevte <span>svět</span></h1>
                <p>Inspirujte se a naplánujte své příští dobrodružství</p>
                <div class="search-container">
                    <i class="search-icon fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" placeholder="Hledat destinaci..." id="searchInput">
                </div>
            </div>
        </div>

        <!-- Mřížka destinací -->
        <section class="destinations-section">
            <h2>Populární destinace</h2>
            <p>Vyberte si svůj cíl</p>

            <div class="filter-bar">
                <button class="filter-btn active" data-filter="vse">Vše</button>
                <button class="filter-btn" data-filter="Evropa">Evropa</button>
                <button class="filter-btn" data-filter="Asie">Asie</button>
                <button class="filter-btn" data-filter="Amerika">Amerika</button>
                <button class="filter-btn" data-filter="Afrika">Afrika</button>
            </div>

            <div class="destinations-grid">

                <section class="dest-card" data-nazev="Skotsko" data-kontinent="Evropa">
                    <div class="dest-image" style="background-image: url(../img/edinburgh.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Evropa</span>
                        <h2>Skotsko</h2>
                        <p>Majestátní hrady, divoká vysočina a tajuplné jezero Loch Ness — země legend čeká na vaši návštěvu.</p>
                        <a href="../zeme/skotsko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Island" data-kontinent="Evropa">
                    <div class="dest-image" style="background-image: url(../img/island.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Evropa</span>
                        <h2>Island</h2>
                        <p>Gejzíry, sopky, polární záře a ledovce — Island je zem ohně a ledu jako ze sci-fi.</p>
                        <a href="../zeme/island.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Slovinsko" data-kontinent="Evropa">
                    <div class="dest-image" style="background-image: url(../img/slovinsko.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Evropa</span>
                        <h2>Slovinsko</h2>
                        <p>Alpské vrcholy, křišťálové jezero Bled a malebná Ljubljana — skrytý klenot střední Evropy.</p>
                        <a href="../zeme/slovinsko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Rakousko" data-kontinent="Evropa">
                    <div class="dest-image" style="background-image: url(../img/rakousko.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Evropa</span>
                        <h2>Rakousko</h2>
                        <p>Barokní Vídeň, alpské lyžování a příroda Tyrolska — srdce Evropy v celé své kráse.</p>
                        <a href="../zeme/rakousko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Japonsko" data-kontinent="Asie">
                    <div class="dest-image" style="background-image: url(../img/japonsko.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Asie</span>
                        <h2>Japonsko</h2>
                        <p>Rozkvetlé třešně, hora Fuji, zenové zahrady a neonové Tokio — tradice a budoucnost v jednom.</p>
                        <a href="../zeme/japonsko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Thajsko" data-kontinent="Asie">
                    <div class="dest-image" style="background-image: url(../img/thajsko.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Asie</span>
                        <h2>Thajsko</h2>
                        <p>Zlaté chrámy, tropické pláže a nezapomenutelná kuchyně — Země úsměvů vás okouzlí.</p>
                        <a href="../zeme/thajsko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Kanada" data-kontinent="Amerika">
                    <div class="dest-image" style="background-image: url(../img/kanada.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Amerika</span>
                        <h2>Kanada</h2>
                        <p>Tyrkysová jezera Banffu, Niagarské vodopády a nekonečné divočiny — příroda v největším měřítku.</p>
                        <a href="../zeme/kanada.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Peru" data-kontinent="Amerika">
                    <div class="dest-image" style="background-image: url(../img/peru.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Amerika</span>
                        <h2>Peru</h2>
                        <p>Machu Picchu ukryté v Andách, tajemné Nazca Lines a amazonský prales — kolébka Inků.</p>
                        <a href="../zeme/peru.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Maroko" data-kontinent="Afrika">
                    <div class="dest-image" style="background-image: url(../img/maroko.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Afrika</span>
                        <h2>Maroko</h2>
                        <p>Zlaté duny Sahary, labyrinty starých medín a vůně koření — Afrika na dosah ruky.</p>
                        <a href="../zeme/maroko.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

                <section class="dest-card" data-nazev="Keňa" data-kontinent="Afrika">
                    <div class="dest-image" style="background-image: url(../img/kena.jpg)"></div>
                    <div class="dest-overlay"></div>
                    <div class="dest-content">
                        <span class="dest-tag">Afrika</span>
                        <h2>Keňa</h2>
                        <p>Safari v Maasai Mara, Velká migrace pakoňů a balonový let nad savanami — Afrika v celé své síle.</p>
                        <a href="../zeme/kena.php" class="dest-btn">Prozkoumat <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </section>

            </div>
        </section>

        <footer>
            <div class="footerContainer">
                <div class="footerSocialMediaIcons">
                    <a href="https://www.youtube.com/channel/UC5_X-Wk23b04S3blgQPcBSw"><i class="fa-brands fa-youtube"></i></a>
                    <a href="https://www.instagram.com/honza.mak/"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.facebook.com/jan.makovicky.52/?locale=cs_CZ"><i class="fa-brands fa-facebook"></i></a>
                </div>
                <div class="footerBottom">
                    <p>Copyright &copy;2024-2025; Journeyo</p>
                </div>
                <div class="footerNavbar">
                    <ul>
                        <li><a href="uvod.php">Úvod</a></li>
                        <li><a href="destinace.php">Destinace</a></li>
                        <?php if ($logged_in): ?>
                            <li><a href="oblibene.php">Oblíbené</a></li>
                            <li><a href="cesty.php">Cesty</a></li>
                            <li><a href="dashboard.php">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="reglog.php">Přihlášení</a></li>
                        <?php endif; ?>
                        <?php if ($is_admin): ?>
                            <li><a href="../admin/admin.php">Admin</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </footer>

        <script>
        const menuBtn = document.querySelector(".menu-btn");
        const navigation = document.querySelector(".nav");
        menuBtn.addEventListener("click", () => {
            menuBtn.classList.toggle("active");
            navigation.classList.toggle("active");
        });

        document.addEventListener("DOMContentLoaded", function () {
            var header = document.querySelector("header");
            window.addEventListener("scroll", function () {
                header.classList.toggle("scrolled", window.scrollY > 0);
            });
        });

        // Kliknutí na celou kartu
        document.querySelectorAll('.dest-card').forEach(function (card) {
            card.addEventListener('click', function (e) {
                if (!e.target.closest('.dest-btn')) {
                    const btn = card.querySelector('.dest-btn');
                    if (btn) window.location.href = btn.href;
                }
            });
        });

        // Filtrování (search + kontinent)
        let activeKontinent = 'vse';

        function applyFilters() {
            const query = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.dest-card').forEach(function (card) {
                const name = card.getAttribute('data-nazev').toLowerCase();
                const kontinent = card.getAttribute('data-kontinent');
                const matchesSearch = name.includes(query);
                const matchesKontinent = activeKontinent === 'vse' || kontinent === activeKontinent;
                card.style.display = (matchesSearch && matchesKontinent) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('input', applyFilters);

        document.querySelectorAll('.filter-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeKontinent = this.getAttribute('data-filter');
                applyFilters();
            });
        });

        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        }
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');
        </script>
    </body>
</html>
