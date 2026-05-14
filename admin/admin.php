<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../pages/uvod.php");
    exit();
}
$logged_in = isset($_SESSION['username']);
$username = $logged_in ? $_SESSION['username'] : null;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace | JOURNEYO</title>
    <link rel="icon" href="../img/favicon.ico">
    <link rel="stylesheet" href="../styles/admin.css">
    <link rel="stylesheet" href="../styles/themes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <a href="../pages/uvod.php"><img src="../img/nvlogo.png" alt="JY" class="logo"></a>
        <div class="menu-btn"></div>
        <div class="nav">
            <div class="nav-items">
                <a href="../pages/uvod.php">Úvod</a>
                <a href="../pages/destinace.php">Destinace</a>
                <?php if ($logged_in): ?>
                    <a href="../pages/oblibene.php">Oblíbené</a>
                    <a href="../pages/cesty.php">Cesty</a>
                    <a href="../pages/dashboard.php">Dashboard</a>
                <?php endif; ?>
                <?php if ($is_admin): ?>
                    <a href="../admin/admin.php" class="active">Admin</a>
                <?php endif; ?>
                <?php if ($logged_in): ?>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($username); ?></span>
                        <a href="../backend/logout.php">Odhlásit se</a>
                    </div>
                <?php else: ?>
                    <a href="../pages/reglog.php">Přihlášení</a>
                <?php endif; ?>
                <button class="theme-toggle" onclick="toggleTheme()" title="Přepnout motiv">
                    <i class="fa-solid fa-circle-half-stroke"></i>
                </button>
            </div>
        </div>
    </header>

    <main>
        <section class="admin-dashboard">
            <h1>Administrátorský panel</h1>
            <p>Vítejte, <?php echo htmlspecialchars($username); ?>. Vyberte, co chcete spravovat:</p>
            <div class="admin-links">
                <a href="manage_reviews.php">Správa recenzí</a>
                <a href="manage_users.php">Správa uživatelů</a>
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
                    <li><a href="../pages/uvod.php">Úvod</a></li>
                    <li><a href="../pages/destinace.php">Destinace</a></li>
                    <li><a href="../pages/oblibene.php">Oblíbené</a></li>
                    <li><a href="../admin/admin.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script type="text/javascript">
        const menuBtn = document.querySelector(".menu-btn");
        const navigation = document.querySelector(".nav");
        menuBtn.addEventListener("click", () => {
            menuBtn.classList.toggle("active");
            navigation.classList.toggle("active");
        });

        document.addEventListener("DOMContentLoaded", function() {
            var header = document.querySelector("header");
            window.addEventListener("scroll", function() {
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