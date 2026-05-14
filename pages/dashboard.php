<?php
session_start();
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

if (!$logged_in) {
    header("Location: reglog.php");
    exit();
}

require_once '../backend/config.php';
$user_id = $_SESSION['user_id'];

// Statistiky cest
$stats = [];
$res = $conn->prepare("SELECT status, COUNT(*) as count FROM trips WHERE user_id = ? GROUP BY status");
$res->bind_param("i", $user_id);
$res->execute();
$result = $res->get_result();
$stats['plánovaná'] = 0; $stats['probíhající'] = 0; $stats['dokončená'] = 0;
while ($row = $result->fetch_assoc()) $stats[$row['status']] = $row['count'];
$stats['celkem'] = array_sum($stats);
$res->close();

// Počet oblíbených
$fav = $conn->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
$fav->bind_param("i", $user_id);
$fav->execute();
$fav_count = $fav->get_result()->fetch_assoc()['count'];
$fav->close();

// Počet recenzí
$rev = $conn->prepare("SELECT COUNT(*) as count FROM reviews WHERE user_id = ?");
$rev->bind_param("i", $user_id);
$rev->execute();
$rev_count = $rev->get_result()->fetch_assoc()['count'];
$rev->close();

// Poslední 3 cesty
$recent = $conn->prepare("SELECT * FROM trips WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
$recent->bind_param("i", $user_id);
$recent->execute();
$recent_trips = $recent->get_result();
$recent->close();

// Oblíbené destinace
$favdest = $conn->prepare("SELECT d.name, d.image_url FROM favorites f JOIN destinations d ON f.destination_id = d.id WHERE f.user_id = ? LIMIT 4");
$favdest->bind_param("i", $user_id);
$favdest->execute();
$fav_destinations = $favdest->get_result();
$favdest->close();
?>
<!DOCTYPE html>
<html lang="cs" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | JOURNEYO</title>
    <link rel="icon" href="../img/favicon.ico">
    <link rel="stylesheet" href="../styles/dashboard.css">
    <link rel="stylesheet" href="../styles/themes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous">
</head>
<body>
    <header>
        <a href="uvod.php"><img src="../img/nvlogo.png" alt="JY" class="logo"></a>
        <div class="menu-btn"></div>
        <div class="nav">
            <div class="nav-items">
                <a href="uvod.php">Úvod</a>
                <a href="destinace.php">Destinace</a>
                <?php if ($logged_in): ?>
                    <a href="oblibene.php">Oblíbené</a>
                    <a href="cesty.php">Cesty</a>
                    <a href="dashboard.php" class="active">Dashboard</a>
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

    <div class="dashboard-hero">
        <span class="page-label">Přehled</span>
        <h1 class="page-title">Vítej zpět, <?php echo htmlspecialchars($username); ?>!</h1>
    </div>

    <main class="dashboard-main">
        <!-- Statistiky -->
        <section class="stats-grid">
            <div class="stat-card">
                <i class="fa-solid fa-earth-europe"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $stats['celkem']; ?></span>
                    <span class="stat-label">Cest celkem</span>
                </div>
            </div>
            <div class="stat-card planned">
                <i class="fa-regular fa-clock"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $stats['plánovaná']; ?></span>
                    <span class="stat-label">Plánované</span>
                </div>
            </div>
            <div class="stat-card ongoing">
                <i class="fa-solid fa-plane"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $stats['probíhající']; ?></span>
                    <span class="stat-label">Probíhající</span>
                </div>
            </div>
            <div class="stat-card done">
                <i class="fa-solid fa-check-circle"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $stats['dokončená']; ?></span>
                    <span class="stat-label">Dokončené</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-star"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $fav_count; ?></span>
                    <span class="stat-label">Oblíbených</span>
                </div>
            </div>
            <div class="stat-card">
                <i class="fa-solid fa-comment"></i>
                <div class="stat-info">
                    <span class="stat-number"><?php echo $rev_count; ?></span>
                    <span class="stat-label">Recenzí</span>
                </div>
            </div>
        </section>

        <div class="dashboard-columns">
            <!-- Poslední cesty -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2>Poslední cesty</h2>
                    <a href="cesty.php" class="section-link">Zobrazit vše</a>
                </div>
                <?php if ($recent_trips->num_rows === 0): ?>
                    <p class="empty-msg">Zatím žádné cesty. <a href="cesty.php">Přidejte první!</a></p>
                <?php else: ?>
                    <div class="recent-trips">
                        <?php while ($t = $recent_trips->fetch_assoc()): ?>
                            <div class="recent-trip-item status-<?php echo htmlspecialchars($t['status']); ?>">
                                <div class="recent-trip-info">
                                    <strong><?php echo htmlspecialchars($t['name']); ?></strong>
                                    <span><?php echo htmlspecialchars($t['destination']); ?></span>
                                    <?php if ($t['date_from']): ?>
                                        <small><?php echo date('d.m.Y', strtotime($t['date_from'])); ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="status-badge"><?php echo htmlspecialchars($t['status']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Oblíbené destinace -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2>Oblíbené destinace</h2>
                    <a href="oblibene.php" class="section-link">Zobrazit vše</a>
                </div>
                <?php if ($fav_destinations->num_rows === 0): ?>
                    <p class="empty-msg">Zatím žádné oblíbené. <a href="destinace.php">Prozkoumejte destinace!</a></p>
                <?php else: ?>
                    <div class="fav-dest-grid">
                        <?php while ($d = $fav_destinations->fetch_assoc()): ?>
                            <?php $page = strtolower($d['name']) . ".php"; ?>
                            <a href="../zeme/<?php echo $page; ?>" class="fav-dest-card" style="--img:url(../<?php echo htmlspecialchars($d['image_url']); ?>)">
                                <span><?php echo htmlspecialchars($d['name']); ?></span>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <!-- Rychlé akce -->
        <section class="quick-actions">
            <h2>Rychlé akce</h2>
            <div class="actions-row">
                <a href="cesty.php" class="action-btn"><i class="fa-solid fa-plus"></i> Přidat cestu</a>
                <a href="destinace.php" class="action-btn"><i class="fa-solid fa-compass"></i> Objevovat destinace</a>
                <a href="oblibene.php" class="action-btn"><i class="fa-solid fa-star"></i> Moje oblíbené</a>
            </div>
        </section>
    </main>

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
                    <li><a href="oblibene.php">Oblíbené</a></li>
                    <li><a href="cesty.php">Cesty</a></li>
                    <li><a href="dashboard.php">Dashboard</a></li>
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

        function toggleTheme() {
            document.body.classList.toggle('light-mode');
            localStorage.setItem('theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
        }
        if (localStorage.getItem('theme') === 'light') document.body.classList.add('light-mode');
    </script>
</body>
</html>
